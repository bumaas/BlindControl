# Architektur-Guidelines: BlindControl

Dieses Dokument beschreibt verbindliche Konventionen für neuen bzw. geänderten Code in den
Modulen `BlindController` und `BlindControlGroupMaster` sowie eine To-do-Liste zur schrittweisen
Angleichung des Bestandscodes. Es ersetzt nicht die `README.md` (endnutzerorientiert), sondern
richtet sich an Entwickler dieses Repos.

## 1. Modul-Lifecycle

- **`Create()`**: nur Registrierung von Properties, Attributes und Timern. Kein Zugriff auf andere
  Instanzen, kein `IPS_GetKernelRunlevel()`-Check nötig.
- **`ApplyChanges()`**: zuerst `parent::ApplyChanges()`, dann Kernel-Ready-Check
  (`if (IPS_GetKernelRunlevel() !== KR_READY) { return; }`), danach Registrierung von
  References/Messages/Variablen und Aufruf von `SetInstanceStatusAndTimerEvent()`.
- **`RequestAction()`**: ein zentraler `switch` über `$Ident`, der an private Handler-Methoden
  delegiert; unbekannte Idents lösen `trigger_error` aus.
- Referenzimplementierung: `BlindController::Create/ApplyChanges/RequestAction`
  (`BlindController/module.php`).

## 2. Konstanten-Namensschema

Für jedes Modul – auch kleine – gilt:

- `private const string PROP_*` für Property-Keys
- `ATTR_*` für Attribute
- `TIMER_*` für Timer-Namen
- `STATUS_*` (int) für Instanzstatus-Codes
- `VAR_IDENT_*` für Variablen-Idents

Auch Module mit nur einer Property (z. B. `BlindControlGroupMaster`) verwenden dieses Schema statt
Literal-Strings – keine Ausnahme "weil das Modul klein ist".

## 3. Logging

Einheitliches dreistufiges Logging:

- `Logger_Dbg` → `SendDebug` (Entwickler-Diagnose, nicht für Endnutzer sichtbar)
- `Logger_Inf` → `LogMessage(..., KL_NOTIFY)` (informative Meldung im IPS-Meldungsfenster)
- `Logger_Err` → `LogMessage(..., KL_ERROR)` (Fehler) plus Schreiben in die
  `LAST_MESSAGE`-Statusvariable, sofern für den Endnutzer relevant

Aktuell ist dieses Schema nur in `BlindController` implementiert. Neue Module sollen es
übernehmen; siehe To-do-Liste zur Vereinheitlichung mit `BlindControlGroupMaster`.

## 4. form.json ↔ PHP-Synchronisation

Zwei Strategien sind im Repo zulässig, je nach Formkomplexität:

- **Statisches `form.json`** – ab einer gewissen Anzahl Properties oder bei Bedarf an
  dynamischer Sichtbarkeitssteuerung (Vorbild: `BlindController`).
- **Dynamisch generiertes Form in PHP** (`GetConfigurationForm()`) – für einfache oder generische
  Formulare (Vorbild: `BlindControlGroupMaster`).

Bei jeder Änderung an Properties gilt folgende Checkliste (manuell, keine automatische Prüfung
vorhanden):

- [ ] Property-Name in `form.json` (`name`) stimmt mit der `PROP_*`-Konstante überein
- [ ] `onChange`-Handler in `form.json`, der `IPS_RequestAction()` aufruft, hat ein passendes
      `case` im `RequestAction()`-Switch

## 5. Methodengröße / Zerlegung

- Neue Entscheidungslogik wird als eigene private Hilfsmethode ergänzt (wie bei
  `determineDayState`, `applyShadowingLogic` etc. bereits praktiziert), nicht als zusätzlicher
  Block in einer bestehenden großen Methode wie `ControlBlind()`.
- Richtwert: Methoden über ca. 100 Zeilen sind Kandidaten für eine Aufteilung.
  `SetInstanceStatusAndTimerEvent()` wurde entsprechend in Validierungsmethoden pro
  Property-Gruppe (`check*Group()`) aufgeteilt.

## 6. Sprache in Code/Kommentaren

- Bezeichner (Klassen, Methoden, Variablen): Englisch.
- Kommentare sowie benutzer- und log-sichtbare Texte: Deutsch.

Dies entspricht dem Status quo im Code und wird bewusst nicht vereinheitlicht, um
großflächige Umbenennungen zu vermeiden.

## 7. Tests

Aktuell existieren keine automatisierten Tests. Für neuen oder geänderten Code in reinen
Berechnungsmethoden ohne IPS-Kernel-Zugriff (Kandidaten: `calculateDayPosition`,
`applyShadowingLogic`, `checkContactLimit` u. ä.) sollen künftig PHPUnit-Tests ergänzt werden.
Kein Zwang zur Vollabdeckung des Bestandscodes – aber neuer/geänderter Code in diesen Methoden
braucht Tests.

## 8. To-do-Liste (Bestandscode-Angleichung)

- [ ] `BlindControlGroupMaster` auf gemeinsames Logging-Schema (`Logger_Err/Inf/Dbg`) umstellen.
- [ ] `BlindControlGroupMaster`: Property-Konstanten (`PROP_*`) statt Literal-Strings einführen.
- [x] `SetInstanceStatusAndTimerEvent()` (~420 Zeilen) in kleinere Validierungsmethoden pro
      Property-Gruppe aufteilen.
- [ ] Erste PHPUnit-Tests für 2–3 reine Berechnungsmethoden als Referenzbeispiel anlegen
      (Testverzeichnisstruktur `tests/` fehlt noch komplett).
- [ ] Prüfen, ob eine gemeinsame Basisklasse/Trait für Logging zwischen `BlindController` und
      `BlindControlGroupMaster` sinnvoll ist (Vorbereitung für künftige weitere Module).
