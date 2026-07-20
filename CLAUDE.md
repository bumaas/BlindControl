# BlindControl — Projekt-Hinweise

IP-Symcon-Modul zur Rollladen-/Jalousiesteuerung (`IPSModuleStrict`).

## Struktur

- `BlindController/module.php` — Hauptmodul (gesamte Logik)
- `BlindController/form.json` — Konfigurationsformular; englische Labels dienen als Übersetzungsschlüssel
- `BlindController/locale.json` — deutsche Übersetzungen (Schlüssel müssen exakt den form.json-Texten entsprechen)
- `library.json` (Repo-Wurzel) — Version, Build, Datum (Build-Konvention siehe globale CLAUDE.md)

## Level-Konvention (wichtig!)

`profileBlindLevel['MinValue']` und `profileSlatsLevel['MinValue']` sind **per Definition immer die Offen-Position**, `MaxValue` immer die Geschlossen-Position — auch bei reversierten Profilen. Bei der Shutter-Darstellung wird `MinValue` direkt aus `OPEN_OUTSIDE_VALUE` befüllt und kann daher numerisch größer als `MaxValue` sein; genau das erkennt `isMinMaxReversed()`.

- Wer „offen" anfahren will, nimmt `MinValue`; wer „geschlossen" will, `MaxValue`.
- **Nie** per `isMinMaxReversed()`-Ternary zwischen Min- und Maxwert als Zielposition wählen — so entstand der Notfallkontakt-Bug (fuhr zu statt auf, gefixt in 2.50 build 101).
- `isMinMaxReversed()` nur für Vergleichs-/Richtungslogik verwenden (z. B. min/max-Auswahl bei Begrenzungen).
- `calculateNormalizedLevel` bildet MinValue auf 0 % (geschlossen-Anteil) ab; `combineContactLimits`/`pickContactLimit` und die Abwärts-Erkennung folgen derselben Konvention.

## Rollenverteilung der Darstellungs-Funktionen

- `GetPresentationInformation()` liefert **ausschließlich Min/Max** (bei Legacy-Profilen zusätzlich `Reversed`). Darstellungen ohne echte MIN/MAX-Felder (z. B. boolesche/String-Wertanzeigen) geben `null` zurück.
- Zustandswerte und deren Beschriftungen (z. B. OPTIONS einer Wertanzeige) dort **nicht** hineininterpretieren — dafür ist `GetValueFormattedEx()` zuständig (so werden auch die Kontakt-Labels in Trace/Hinweis formatiert).

## Texte pflegen

Bei Änderungen an Formular-/Hilfetexten immer synchron halten:

1. `form.json` — englischer Text (zugleich Übersetzungsschlüssel)
2. `locale.json` — deutscher Text unter exakt diesem Schlüssel
3. `README.md` — falls die Stelle dort ebenfalls dokumentiert ist

## Support-Kontext

Fehlerberichte kommen aus dem Symcon-Forum. Fixes gehen als Beta über `master` raus; Antworttexte fürs Forum werden auf Deutsch formuliert.
