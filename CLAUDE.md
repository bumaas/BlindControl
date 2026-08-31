# BlindControl — Projekt-Hinweise

Symcon-Modulbibliothek zur Rollladen-/Jalousiesteuerung (`IPSModuleStrict`, `declare(strict_types=1)`).

## Struktur

- `BlindController/` — Hauptmodul (Präfix `BLC`), die gesamte Steuerungslogik in `module.php` (~4.700 Zeilen)
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
C:/php/php -l BlindController/module.php          # Syntaxprüfung (auch GroupMaster, check_locale.php)
C:/php/php tests/check_locale.php                 # Übersetzungs-Vollständigkeit, Exit-Code 1 bei Lücken
```

`tests/` enthält **keine** PHPUnit-Tests, nur diesen Locale-Prüfer. Die CI
(`.github/workflows/check.yml`, PHP 8.4) fährt genau drei Schritte: `php -l`, JSON-Validität
aller `*.json` und `check_locale.php` — lokal also dasselbe vor dem Commit laufen lassen.

Geänderte Bibliothek auf der Produktivanlage ohne Kernel-Neustart einlesen:

```bash
C:/php/php C:/Users/Burkhard/.claude/tools/symcon_rpc.php MC_ReloadModule 51062 '"BlindControl"'
```

Das eingebettete PHP von Symcon ist nicht das CLI-PHP — `php -l` findet nur Syntaxfehler
(siehe globale CLAUDE.md).

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

### Manuelle Bedienung

Erkannt wird sie daran, dass sich die Aktor-Variable ohne eigenen Fahrbefehl geändert hat
(`syncManualMovementAttribute()`, Attribut `manualMovement`). Verspätete Rückmeldungen der
eigenen Fahrt — typisch bei KNX — fängt `isFeedbackOfOwnMovement()` /
`matchesLastCommandedPosition()` innerhalb von `FEEDBACK_MOVEMENT_TIME` ab; sonst würde
jede Fahrt sich selbst als manuelle Bedienung melden und die Automatik lahmlegen.

### Instanzstatus und Validierung

`SetInstanceStatusAndTimerEvent()` ruft der Reihe nach die `check*Group()`-Methoden auf und
setzt beim ersten Fehler den zugehörigen `STATUS_INST_*`-Code (201–242, Konstanten am
Dateianfang). Eine neue Property mit Prüfbedarf braucht daher: Konstante `PROP_*`,
Registrierung in `RegisterProperties()`, ggf. `RegisterReferences()`/`RegisterMessages()`,
einen Zweig in der passenden `check*Group()` und — bei neuem Fehlerfall — einen neuen
`STATUS_INST_*`-Code samt Text in `form.json`/`locale.json`.

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

## Support-Kontext

Fehlerberichte kommen aus dem Symcon-Forum. Fixes gehen als Beta über `master` raus; Antworttexte fürs Forum werden auf Deutsch formuliert.
