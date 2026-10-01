<?php

declare(strict_types=1);

/*
 * Hinweise im Konfigurationsformular (BlindController::GetConfigurationForm).
 *
 * Anlass ist die MCP-Evaluierung vom 01.10.2026: Das Formular sagte an zwei Stellen weniger als
 * das README, sodass sich die Instanz ohne README nicht richtig einstellen ließ.
 *
 *   1. Rund 50 Höhen- und Lamellenfelder erwarten den Rohwert der Höhen- bzw. Lamellenvariable
 *      (bei Homematic 0..1, sonst oft 0..100 oder 0..255). Skala und Richtung standen nirgends.
 *      Erwartet: Unter der Variable steht ein Hinweis mit dem Wert für „geöffnet" und dem für
 *      „geschlossen" — nach der Level-Konvention ist MinValue immer offen, MaxValue immer zu.
 *   2. Die Temperaturschwellen der Beschattung nach Sonnenstand (24/10 °C für den
 *      Helligkeitsschwellwert, 27 und 30 °C für die Behanghöhe) stehen fest im Code. Erwartet:
 *      Das Label am Temperatursensor nennt sie.
 *
 * Aufruf: git submodule update --init (einmalig), dann
 *         php tests/check-form-hints.php (Exit-Code 1 bei Fehlern)
 */

require_once __DIR__ . '/harness.php';

/** Sucht ein Formularelement rekursiv über seinen Namen. */
function formularElement(array $elemente, string $name): ?array
{
    foreach ($elemente as $e) {
        if (!is_array($e)) {
            continue;
        }
        if (($e['name'] ?? '') === $name) {
            return $e;
        }
        if (isset($e['items']) && is_array($e['items']) && ($treffer = formularElement($e['items'], $name)) !== null) {
            return $treffer;
        }
    }
    return null;
}

/** Alle Label-Texte des Formulars, rekursiv. */
function labelTexte(array $elemente): array
{
    $texte = [];
    foreach ($elemente as $e) {
        if (!is_array($e)) {
            continue;
        }
        if (($e['type'] ?? '') === 'Label') {
            $texte[] = (string)($e['caption'] ?? '');
        }
        if (isset($e['items']) && is_array($e['items'])) {
            $texte = array_merge($texte, labelTexte($e['items']));
        }
    }
    return $texte;
}

function formular(BlindControllerHarness $m): array
{
    return json_decode($m->GetConfigurationForm(), true, 512, JSON_THROW_ON_ERROR)['elements'];
}

/** Die beiden Zahlen des Hinweises: [Wert für geöffnet, Wert für geschlossen] — sprachunabhängig. */
function hinweisWerte(?array $label): ?array
{
    if ($label === null || preg_match('/: (\S+) = [^,]+, (\S+) = /u', (string)($label['caption'] ?? ''), $t) !== 1) {
        return null;
    }
    return [$t[1], $t[2]];
}

function variableMitDarstellung(int $typ, array $darstellung): int
{
    $id = IPS_CreateVariable($typ);
    IPS_SetVariableCustomPresentation($id, $darstellung);
    return $id;
}

$m = neueInstanz();

/* A. Ohne Höhenvariable gibt es nichts zu sagen: Die Hinweise bleiben unsichtbar. */
echo "\nA. Keine Variable gewählt\n";
$form = formular($m);
foreach (['BlindLevelRangeHint', 'SlatsLevelRangeHint'] as $name) {
    $label = formularElement($form, $name);
    pruefe($label !== null, "$name ist im Formular vorhanden");
    pruefe(($label['visible'] ?? true) === false, "$name ist unsichtbar");
}

/* B. Schieberegler 0..100: 0 = geöffnet, 100 = geschlossen */
echo "\nB. Höhenvariable mit Schieberegler 0..100\n";
$hoehe = variableMitDarstellung(1 /* Integer */, ['PRESENTATION' => VARIABLE_PRESENTATION_SLIDER, 'MIN' => 0, 'MAX' => 100]);
IPS_SetProperty($m->id(), 'BlindLevelID', $hoehe);
IPS_ApplyChanges($m->id());
$label = formularElement(formular($m), 'BlindLevelRangeHint');
pruefe(($label['visible'] ?? false) === true, 'BlindLevelRangeHint ist sichtbar');
pruefe(hinweisWerte($label) === ['0', '100'], 'geöffnet 0, geschlossen 100 (' . json_encode($label['caption'] ?? null, JSON_UNESCAPED_UNICODE) . ')');
pruefe((formularElement(formular($m), 'SlatsLevelRangeHint')['visible'] ?? true) === false, 'SlatsLevelRangeHint bleibt unsichtbar');

/* C. Rollladen-Darstellung wie bei Homematic: 1 = geöffnet, 0 = geschlossen. MinValue ist hier
 *    numerisch größer als MaxValue — der Hinweis muss trotzdem „offen" zuerst nennen. */
echo "\nC. Höhenvariable mit Rollladen-Darstellung 1..0 (Homematic)\n";
$homematic = variableMitDarstellung(2 /* Float */, ['PRESENTATION' => VARIABLE_PRESENTATION_SHUTTER, 'OPEN_OUTSIDE_VALUE' => 1.0, 'CLOSE_INSIDE_VALUE' => 0.0]);
IPS_SetProperty($m->id(), 'BlindLevelID', $homematic);
IPS_ApplyChanges($m->id());
$label = formularElement(formular($m), 'BlindLevelRangeHint');
pruefe(($label['visible'] ?? false) === true, 'BlindLevelRangeHint ist sichtbar');
pruefe(hinweisWerte($label) === ['1', '0'], 'geöffnet 1, geschlossen 0 (' . json_encode($label['caption'] ?? null, JSON_UNESCAPED_UNICODE) . ')');

/* D. Lamellenvariable 0..255 */
echo "\nD. Lamellenvariable mit Schieberegler 0..255\n";
$lamellen = variableMitDarstellung(1 /* Integer */, ['PRESENTATION' => VARIABLE_PRESENTATION_SLIDER, 'MIN' => 0, 'MAX' => 255]);
IPS_SetProperty($m->id(), 'SlatsLevelID', $lamellen);
IPS_ApplyChanges($m->id());
$label = formularElement(formular($m), 'SlatsLevelRangeHint');
pruefe(($label['visible'] ?? false) === true, 'SlatsLevelRangeHint ist sichtbar');
pruefe(hinweisWerte($label) === ['0', '255'], 'geöffnet 0, geschlossen 255 (' . json_encode($label['caption'] ?? null, JSON_UNESCAPED_UNICODE) . ')');

/* E. Das Label am Temperatursensor nennt die festen Schwellen. */
echo "\nE. Temperaturschwellen im Formular\n";
$temperaturLabel = '';
foreach (labelTexte(formular($m)) as $text) {
    if (stripos($text, 'temperature sensor') !== false || stripos($text, 'Temperatursensor') !== false) {
        $temperaturLabel = $text;
    }
}
pruefe($temperaturLabel !== '', 'Label zum Temperatursensor gefunden');
foreach (['24 °C', '10 °C', '27 °C', '30 °C', '15 %', '90 %'] as $angabe) {
    pruefe(str_contains($temperaturLabel, $angabe), "Label nennt $angabe");
}

ergebnis();
