# BlindControl — Projekt-Hinweise

Symcon-Modulbibliothek zur Rollladen-/Jalousiesteuerung (`IPSModuleStrict`, `declare(strict_types=1)`).

## Struktur

- `BlindController/` — Hauptmodul (Präfix `BLC`), die gesamte Steuerungslogik in `module.php`
  - `form.json` — statisches Konfigurationsformular; **englische Labels sind zugleich die Übersetzungsschlüssel**
  - `locale.json` — deutsche Übersetzungen (Schlüssel müssen exakt den form.json-/`Translate()`-Texten entsprechen)
- `BlindControlGroupMaster/` — Gruppen-Master (Präfix `BLCGM`), liest/setzt Properties mehrerer Blind-Controller-Instanzen; Formular **dynamisch** in `GetConfigurationForm()` erzeugt, kein `form.json`
- `docs/ARCHITECTURE.md` — verbindliche Konventionen für neuen Code (Lifecycle, Konstantenschema, Logging, Methodengröße) plus offene To-dos
- `README.md` — Endnutzer-Doku (Funktionsumfang, Konfiguration, Statusvariablen)
- `library.json` (Repo-Wurzel) — Version, Build, Datum (Build-Konvention siehe globale CLAUDE.md)

Beide Module sind eigenständige Geräte-Instanzen (`type: 3`) ohne Parent; `ReceiveData()` im
BlindController ist bewusst ein `trigger_error` — es gibt keinen Datenfluss.

## Prüfen und Ausrollen

```bash
git submodule update --init                       # einmalig: .style (Regelwerk) und tests/stubs (Kernel-Stub)
C:/php/php -l BlindController/module.php          # Syntaxprüfung (auch GroupMaster und die Dateien unter tests/)
C:/php/php php-cs-fixer.phar fix --config=.style/.php-cs-fixer.php --dry-run --diff --allow-risky=yes
C:/php/php tests/check_locale.php                 # Übersetzungs-Vollständigkeit, Exit-Code 1 bei Lücken
C:/php/php tests/check-level-conversion.php       # Umrechnung Profilwerte/Prozent, Exit-Code 1 bei Fehlern
C:/php/php tests/check-archiv-belegt.php         # Helligkeit bei gesperrtem Archiv (Monatsverdichtung)
C:/php/php tests/check-form-hints.php            # Skalenhinweise und Temperaturschwellen im Formular
C:/php/php tests/check-value-ranges.php          # Wertebereiche: Formulargrenzen = Modulprüfung, Status 243
C:/php/php tests/check-status-recovery.php       # Meldung „Konfiguration ist gültig" nach Fehlerstatus
C:/php/php tests/check-debug-schedule.php        # Debug-Zeile zum Wochenplan (Kurzfassung statt Ereignis-JSON)
C:/php/php tests/check-weekly-schedule.php       # Auf-/Abzeit aus dem Wochenplan (Fixtures: echte IPS_GetEvent-Mitschnitte)
```

Die CI (`.github/workflows/check.yml`, PHP 8.4, Checkout mit Submodulen) fährt diese Schritte:
`php -l` auf beide `module.php` und `tests/check_locale.php`, `tests/harness.php`,
`tests/check-level-conversion.php`; Code-Stil mit php-cs-fixer gegen das Regelwerk im Submodul
`.style` (`--dry-run`); JSON-Validität aller `*.json` außer `tests/stubs`; `check_locale.php`;
danach jede `tests/check-*.php` (derzeit `check-level-conversion.php`, `check-archiv-belegt.php`,
`check-form-hints.php`, `check-value-ranges.php`, `check-status-recovery.php`, `check-debug-schedule.php`,
`check-weekly-schedule.php`, `check-readme.php`). Bis auf `check-readme.php` sind es Regressionstests gegen den offiziellen
Kernel-Stub (`tests/stubs`, über `tests/harness.php`) — lokal also dasselbe vor dem Commit
laufen lassen. Zuletzt die statischen MCP-Regeln über die Action `bumaas/symcon-mcp-check@v1`
(privates Repo, für eigene Repos freigegeben); sie scheitert nur an Fehlern, Warnungen sind offene Punkte.

Geänderte Bibliothek per `MC_ReloadModule` mit Ordnername `BlindControl` neu einlesen;
eingebettetes PHP ≠ CLI-PHP — beides siehe globale CLAUDE.md.

## Der Steuerungslauf (Kern der Architektur)

Alles läuft über `ControlBlind(bool $considerDeactivationTimeAuto)` — angestoßen vom
Update-Timer, vom Verzögerungs-Timer, von `MessageSink()` bei Änderung einer beobachteten
Variablen oder von außen per Skript. `ControlBlind()` selbst prüft nur Instanzstatus und
Semaphore (`<InstanceID>- Blind`, 30 s; bei Timeout wird der Lauf per `RegisterOnceTimer`
neu angestoßen, nicht verworfen) und delegiert an `executeControlBlindRun()`.

Dort liegt die Pipeline, deren Reihenfolge das gesamte Verhalten bestimmt:

1. **Tageszeit** — `determineDayState()` (Wochenplan, Weckzeit/Bettzeit, Helligkeit, IsDay-Indikator)
2. **Bewegungssperre** — `checkIsDayChange()` verwirft beim Tag/Nacht-Wechsel eine erkannte
   manuelle Bedienung; sonst entscheidet `shouldBlockMovement()`
3. **Basis-Zielposition** — `calculateBasePosition()` → `calculateDayPosition()` / `calculateNightPosition()`
4. **Beschattung** — `applyShadowingLogic()` (nur tagsüber und ohne Sperre): Sonnenstand
   (`getPositionsOfShadowingBySunPosition()`) und/oder Helligkeit (`getPositionsOfShadowingByBrightness()`),
   zusammengeführt in `mergePositions()`
5. **Kontakte** — `applyContactLogic()` überschreibt Positionen (Fenster/Tür, Notfallkontakt)
6. **Fahrt** — `MoveBlind()` → `MoveToPosition()` → `RequestAction` auf die Aktor-Variable
7. **Dokumentation** — `traceDecisionResult()` und `writeDecisionTraceVariable()`

Jeder Schritt schreibt über `addTrace()` in `$this->decisionTrace`. Das Protokoll landet
im Debug-Log, optional als HTML in der Statusvariablen `DECISION_TRACE` und ist zugleich
die Antwort von `ExplainControlBlind()`.

**`$dryRun`** (gesetzt nur von `ExplainControlBlind()`) macht denselben Lauf zustandsfrei:
kein Fahrbefehl, keine Änderung an Attributen, Variablen oder Timern. Wer Logik ergänzt,
die schreibt, muss `$this->dryRun` berücksichtigen — sonst verändert der „Erklären"-Knopf
den Anlagenzustand.

### Wochenplan

Gelesen werden nur Zeiten und Aktionstyp (`getUpTimeOfDay()`/`getDownTimeOfDay()`): Aufzeit ist der
erste Punkt mit ActionID 2, **Abzeit der erste Punkt mit ActionID 1 danach**. Bis build 139 galt der
*zweite* Schließen-Punkt des Tages als Abzeit — das setzte stillschweigend einen Schließen-Punkt um
00:00 voraus, und der vom Formular-Knopf angelegte Plan (seit build 114: nur 07:00 auf, 22:00 zu)
hatte dadurch keine Abzeit; der Rollladen wäre nach Plan nie geschlossen worden (gefunden im
MCP-Blindtest am 01.10.2026). **Ein Plan ohne Abzeit ist kein Fehler:** Der 24-h-Plan für reine
Beschattung (README 5.2) hat bewusst keine — `checkTimeTable()` darf das nicht beanstanden.
Der Knopf legt jetzt `defaultWeeklySchedulePoints()` an (00:00 zu, 07:00 auf, 22:00 zu).

### Manuelle Bedienung

Erkannt wird sie daran, dass sich die Aktor-Variable ohne eigenen Fahrbefehl geändert hat
(`syncManualMovementAttribute()`, Attribut `manualMovement`). Verspätete Rückmeldungen der
eigenen Fahrt — typisch bei KNX — fängt `isFeedbackOfOwnMovement()` /
`matchesLastCommandedPosition()` innerhalb von `FEEDBACK_MOVEMENT_TIME` ab; sonst würde
jede Fahrt sich selbst als manuelle Bedienung melden und die Automatik lahmlegen.

### Instanzstatus und Validierung

`SetInstanceStatusAndTimerEvent()` ruft der Reihe nach die `check*Group()`-Methoden auf und
setzt beim ersten Fehler den zugehörigen `STATUS_INST_*`-Code (201–243, Konstanten am
Dateianfang). Eine neue Property mit Prüfbedarf braucht daher: Konstante `PROP_*`,
Registrierung in `RegisterProperties()`, ggf. `RegisterReferences()`/`RegisterMessages()`,
einen Zweig in der passenden `check*Group()` und — bei neuem Fehlerfall — einen neuen
`STATUS_INST_*`-Code samt Text in `form.json`/`locale.json`.

**Wertebereiche stehen zweimal:** als `minimum`/`maximum` am `NumberSpinner` in `form.json` (wirkt
nur in der Konsole) und in `INTEGER_PROPERTY_RANGES`/`FLOAT_PROPERTY_RANGES` (wirkt immer, auch bei
Konfiguration per Skript, Gruppen-Master oder MCP). `checkValueRangesGroup()` läuft als letzte Gruppe
und setzt Status 243; die Meldung nennt Feld, Wert und Bereich. `tests/check-value-ranges.php` wird
rot, sobald Formular und Tabellen auseinanderlaufen — eine neue Formulargrenze gehört also in die
Tabelle des passenden Typs. **Azimut geht bewusst bis 720:** Ein Bereich über Norden hinweg darf als
`240 – 120` oder als `240 – 480` angegeben werden (Vorgabe Burkhard, 01.10.2026).

**„Letzte Nachricht" ist ein Protokoll, kein Zustand** — Fahrten und Fehler stehen in derselben
Variable. Kommt die Instanz aus einem Fehlerstatus (>= `IS_EBASE`) in einen gültigen, schreibt
`logRecoveryFromErrorStatus()` deshalb einmal „Konfiguration ist gültig" (ohne „wieder" — auch eine
neue Instanz kommt bei der ersten gültigen Konfiguration aus dem Fehlerstatus 203); sonst bliebe der
Fehlertext bei ausgeschalteter Automatik unbegrenzt stehen. Nur beim Wechsel, nie bei jedem
Übernehmen (`SetInstanceStatusAndTimerEvent()` läuft auch aus Timern). **Der Kernel-Stub trägt keine
vollständig gültige Konfiguration** (`IPS_GetEvent` liefert `[]`, der Wochenplan scheitert mit 201) —
Tests prüfen daher die einzelne Gruppe bzw. Entscheidung per `ruf()` und die Einbindung am Quelltext.

## Level-Konvention (wichtig!)

`profileBlindLevel['MinValue']` und `profileSlatsLevel['MinValue']` sind **per Definition immer die Offen-Position**, `MaxValue` immer die Geschlossen-Position — auch bei reversierten Profilen. Bei der Shutter-Darstellung wird `MinValue` direkt aus `OPEN_OUTSIDE_VALUE` befüllt und kann daher numerisch größer als `MaxValue` sein; genau das erkennt `isMinMaxReversed()`.

- Wer „offen" anfahren will, nimmt `MinValue`; wer „geschlossen" will, `MaxValue`.
- **Nie** per `isMinMaxReversed()`-Ternary zwischen Min- und Maxwert als Zielposition wählen — so entstand der Notfallkontakt-Bug (fuhr zu statt auf, gefixt in 2.50 build 101).
- `isMinMaxReversed()` nur für Vergleichs-/Richtungslogik verwenden (z. B. min/max-Auswahl bei Begrenzungen).
- `calculateNormalizedLevel` bildet MinValue auf 0 % (geschlossen-Anteil) ab; `combineContactLimits`/`pickContactLimit` und die Abwärts-Erkennung folgen derselben Konvention.

## Rollenverteilung der Darstellungs-Funktionen

- `GetPresentationInformation()` liefert **ausschließlich Min/Max** (bei Legacy-Profilen zusätzlich `Reversed`). Darstellungen ohne echte MIN/MAX-Felder (z. B. boolesche/String-Wertanzeigen) geben `null` zurück.
- Zustandswerte und deren Beschriftungen (z. B. OPTIONS einer Wertanzeige) dort **nicht** hineininterpretieren — dafür ist `GetValueFormattedEx()` zuständig (so werden auch die Kontakt-Labels in Trace/Hinweis formatiert).

## Konventionen für neuen Code

Details in `docs/ARCHITECTURE.md`; die Kurzfassung:

- `Create()` registriert nur Properties/Attribute/Timer; `ApplyChanges()` prüft
  `IPS_GetKernelRunlevel() !== KR_READY` und endet mit `SetInstanceStatusAndTimerEvent()`.
- `RequestAction()` ist ein zentraler `switch` über `$Ident`, der an private Handler delegiert;
  unbekannte Idents lösen `trigger_error` aus. Er bedient dreierlei: Statusvariablen (`ACTIVATED`),
  Formular-Ereignisse (`onChange` → Sichtbarkeit) und Entprellungs-Timer (Timer-Name = Ident).
- Konstantenschema durchgängig: `PROP_*`, `ATTR_*`, `TIMER_*`, `STATUS_*`, `VAR_IDENT_*` —
  keine Literal-Strings, auch nicht im kleinen GroupMaster.
- Logging dreistufig: `Logger_Dbg` (SendDebug), `Logger_Inf` (`KL_NOTIFY`),
  `Logger_Err` (`KL_ERROR` + Statusvariable `LAST_MESSAGE`).
- **Debug-Zeilen nennen das Ergebnis, nicht die Rohdaten.** Kein `json_encode` ganzer Kernel-Objekte
  (Ereignis, Variable) im Steuerungslauf — das Debug wird auch über MCP (`symcon_debug`) gelesen, und
  ein 2-kB-Dump je Lauf verdeckt die Entscheidungszeilen. Vorbild: `formatScheduleDebug()`. Aus
  demselben Grund schreibt `GetConfigurationForm()` das Formular (54 kB) nicht mehr ins Debug.
- Bezeichner englisch, Kommentare und benutzersichtbare Texte deutsch.
- Neue Entscheidungslogik als eigene private Methode, nicht als weiterer Block in
  `executeControlBlindRun()` oder `SetInstanceStatusAndTimerEvent()`.

## Texte pflegen

Bei Änderungen an Formular-/Hilfetexten immer synchron halten:

1. `form.json` — englischer Text (zugleich Übersetzungsschlüssel)
2. `locale.json` — deutscher Text unter exakt diesem Schlüssel
3. `README.md` — falls die Stelle dort ebenfalls dokumentiert ist

Danach `tests/check_locale.php` laufen lassen — es prüft `caption`/`label`/`suffix` aus
`form.json` **und** alle `Translate('…')`-Aufrufe in `module.php` und in form.json-Skripten.

**Das Formular muss ohne README verständlich sein** (MCP-Evaluierung 01.10.2026: Eine KI liest nur
das Formular). Diese Stellen tragen deshalb Wissen, das sonst nur im README stand:

- Die Labels `BlindLevelRangeHint`/`SlatsLevelRangeHint` nennen die Skala der Höhen- bzw.
  Lamellenfelder („… 1 = geöffnet, 0 = geschlossen"). Sie werden in `applyLevelRangeHints()` aus
  der Darstellung der gewählten Variable gefüllt und per `onChange` live nachgezogen; Texte und
  Zuordnung stehen in `LEVEL_RANGE_HINTS`.
- Das Label am Temperatursensor nennt die festen Schwellen (24/10 °C, 27 °C, 30 °C). **Die Zahlen
  stehen als Literale im Code** (`getBrightnessThreshold()`, `getPositionsOfShadowingBySunPosition()`)
  — wer sie ändert, muss Label, `locale.json` und README mitziehen; `tests/check-form-hints.php`
  prüft nur, dass das Label sie nennt.
- Das Label `ExplainControlBlindHint` unter `actions` nennt `BLC_ExplainControlBlind` als
  Skriptfunktion. **Es ist absichtlich unsichtbar** (`visible: false`, Vorgabe Burkhard): In der
  Konsole soll es nicht erscheinen, im Formular-JSON findet es eine KI trotzdem. Nicht sichtbar schalten.
  Dasselbe gilt für `CreateWeeklyScheduleHint`: `BLC_CreateWeeklySchedule` wählt den Plan nur im
  offenen Formular aus (`UpdateFormField`); per Skript muss `WeeklyTimeTableEventID` selbst gesetzt werden.
- Sichtbare Labels nennen drei Regeln, die im Blindtest nur durch Probieren zu finden waren:
  Sonnenrichtung über Norden (300 bis 60 oder 300 bis 420), Aufbau des Wochenplans (Aktion 1/2,
  Schließzeit = erster Punkt der Aktion 1 nach dem Öffnen) und die Tangens-Interpolation der
  einfachen Beschattungsvariante (`calculateAltitudeDependentPosition()`).

## Support-Kontext

Fehlerberichte kommen aus dem Symcon-Forum. Fixes gehen als Beta über `master` raus; Antworttexte fürs Forum werden auf Deutsch formuliert.
