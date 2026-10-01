<?php

declare(strict_types=1);

/*
 * Wertebereiche der Konfiguration (BlindController::checkValueRangesGroup).
 *
 * Anlass ist die MCP-Evaluierung vom 01.10.2026: Die Grenzen der Zahlenfelder (minimum/maximum
 * in form.json) wirken nur in der Konsole. Wer per Skript, über den Gruppen-Master oder per MCP
 * konfiguriert, konnte jeden Wert setzen — eine Fensterneigung von 200° wurde gespeichert, die
 * Instanz blieb auf "aktiv".
 *
 * Erwartet: Ein Wert außerhalb der Formulargrenzen führt zum Status 243, und die Meldung nennt
 * das Feld. Die Grenzen stehen zweimal — im Formular und in den Tabellen des Moduls —, deshalb
 * prüft Abschnitt A, dass beide übereinstimmen: Eine neue Formulargrenze, die das Modul nicht
 * prüft, lässt den Test rot werden.
 *
 * Sonderfall Sonnenrichtung: Ein Bereich über Norden hinweg darf als "240 bis 120" oder als
 * "240 bis 480" angegeben werden. Beide Schreibweisen müssen gültig bleiben und gleich wirken.
 *
 * Aufruf: git submodule update --init (einmalig), dann
 *         php tests/check-value-ranges.php (Exit-Code 1 bei Fehlern)
 */

require_once __DIR__ . '/harness.php';

const STATUS_BEREICH = 243;

/** Zahlenfelder mit Grenze aus dem Abschnitt "elements": Name => [minimum|null, maximum|null] */
function begrenzteFelder(array $elemente): array
{
    $felder = [];
    foreach ($elemente as $e) {
        if (!is_array($e)) {
            continue;
        }
        if (($e['type'] ?? '') === 'NumberSpinner' && isset($e['name']) && (isset($e['minimum']) || isset($e['maximum']))) {
            $felder[$e['name']] = [$e['minimum'] ?? null, $e['maximum'] ?? null];
        }
        if (isset($e['items']) && is_array($e['items'])) {
            $felder += begrenzteFelder($e['items']);
        }
    }
    return $felder;
}

/** Tabellen des Moduls; im Stand ohne die Prüfung gibt es sie nicht (leeres Array). */
function modulTabelle(string $name): array
{
    $wert = (new ReflectionClass(BlindController::class))->getConstant($name);
    return is_array($wert) ? $wert : [];
}

function gleicheGrenze(int|float|null $a, int|float|null $b): bool
{
    return ($a === null || $b === null) ? $a === $b : (float)$a === (float)$b;
}

/** Ruft die Prüfgruppe; fehlt sie (Stand ohne die Prüfung), gilt das als "nichts beanstandet". */
function pruefgruppe(BlindControllerHarness $m): int
{
    try {
        return $m->ruf('checkValueRangesGroup');
    } catch (ReflectionException) {
        return -1;
    }
}

/** Setzt Werte, wendet an und liefert das Urteil der Prüfgruppe samt deren Meldungen. */
function urteil(BlindControllerHarness $m, array $werte): array
{
    foreach ($werte as $name => $wert) {
        IPS_SetProperty($m->id(), $name, $wert);
    }
    IPS_ApplyChanges($m->id());
    $m->logsZuruecksetzen();
    $status = pruefgruppe($m);
    return [$status, implode(' | ', array_column($m->logsSeitMarke(), 'Message'))];
}

$form = json_decode(file_get_contents(dirname(__DIR__) . '/BlindController/form.json'), true, 512, JSON_THROW_ON_ERROR);
$m    = neueInstanz();

/* A. Formular und Modul nennen dieselben Grenzen */
echo "\nA. Grenzen im Formular und im Modul stimmen überein\n";
$formular = begrenzteFelder($form['elements']);
$tabelle  = modulTabelle('INTEGER_PROPERTY_RANGES') + modulTabelle('FLOAT_PROPERTY_RANGES');
pruefe(count($formular) >= 20, count($formular) . ' begrenzte Zahlenfelder im Formular gefunden');
foreach ($formular as $name => [$min, $max]) {
    $t = $tabelle[$name] ?? null;
    pruefe(
        $t !== null && gleicheGrenze($t[0], $min) && gleicheGrenze($t[1], $max),
        sprintf('%s: Formular %s bis %s, Modul %s', $name, $min ?? 'offen', $max ?? 'offen', $t === null ? 'fehlt' : ($t[0] ?? 'offen') . ' bis ' . ($t[1] ?? 'offen'))
    );
}
$zuviel = array_diff(array_keys($tabelle), array_keys($formular));
pruefe($zuviel === [], 'keine Modulgrenze ohne Formularfeld' . ($zuviel !== [] ? ': ' . implode(', ', $zuviel) : ''));

/* B. Die Vorgabewerte einer neuen Instanz liegen im Bereich */
echo "\nB. Vorgabewerte\n";
[$status] = urteil($m, []);
pruefe($status === 0, "Vorgabewerte werden nicht beanstandet (Urteil $status)");

/* C. Werte außerhalb führen zu 243, die Meldung nennt das Feld. Danach jeweils zurück auf einen gültigen Wert. */
echo "\nC. Werte außerhalb der Grenzen\n";
$faelle = [
    ['WindowsSlope', 200, 90],
    ['WindowOrientation', 361, 0],
    ['UpdateInterval', -1, 1],
    ['AltitudeTo', -91.0, 90.0],
    ['AzimuthFrom', -1.0, 0.0],
    ['AzimuthTo', 721.0, 360.0],
];
foreach ($faelle as [$name, $falsch, $gut]) {
    [$status, $meldungen] = urteil($m, [$name => $falsch]);
    pruefe($status === STATUS_BEREICH, "$name = $falsch ergibt Status 243 (Urteil $status)");
    pruefe(str_contains($meldungen, $name), "Meldung nennt $name" . ($meldungen !== '' ? " ($meldungen)" : ' (keine Meldung)'));
    [$status] = urteil($m, [$name => $gut]);
    pruefe($status === 0, "$name = $gut ist wieder gültig (Urteil $status)");
}

/* D. Die Grenzwerte selbst sind gültig */
echo "\nD. Grenzwerte selbst\n";
[$status] = urteil($m, ['AzimuthFrom' => 0.0, 'AzimuthTo' => 720.0, 'AltitudeFrom' => -90.0, 'AltitudeTo' => 90.0, 'WindowsSlope' => 180, 'WindowOrientation' => 360]);
pruefe($status === 0, "0, 720, -90, 90, 180 und 360 werden nicht beanstandet (Urteil $status)");

/* E. Sonnenrichtung über Norden hinweg: beide Schreibweisen sind gültig und wirken gleich */
echo "\nE. Sonnenrichtung über Norden hinweg\n";
foreach ([[240.0, 120.0], [240.0, 480.0]] as [$von, $bis]) {
    [$status] = urteil($m, ['AzimuthFrom' => $von, 'AzimuthTo' => $bis]);
    pruefe($status === 0, "$von bis $bis wird nicht beanstandet (Urteil $status)");
    pruefe($m->ruf('isAzimuthInRange', 300.0, $von, $bis) === true, "$von bis $bis: Sonne bei 300° liegt im Bereich");
    pruefe($m->ruf('isAzimuthInRange', 60.0, $von, $bis) === true, "$von bis $bis: Sonne bei 60° liegt im Bereich");
    pruefe($m->ruf('isAzimuthInRange', 180.0, $von, $bis) === false, "$von bis $bis: Sonne bei 180° liegt außerhalb");
}

/* F. Die Prüfung hängt in der Statusermittlung, und zwar hinter den spezifischeren Prüfungen.
 *    (Eine vollständig gültige Konfiguration samt Wochenplan trägt der Stub nicht; der Durchstich
 *    bis zum Instanzstatus ist an der Anlage belegt.) */
echo "\nF. Einbindung in die Statusermittlung\n";
$methode = new ReflectionMethod(BlindController::class, 'SetInstanceStatusAndTimerEvent');
$quelle  = implode('', array_slice(file($methode->getFileName()), $methode->getStartLine() - 1, $methode->getEndLine() - $methode->getStartLine() + 1));
$neu     = strpos($quelle, 'checkValueRangesGroup()');
$davor   = strpos($quelle, 'checkDeactivationTimesGroup()');
pruefe($neu !== false, 'SetInstanceStatusAndTimerEvent ruft checkValueRangesGroup auf');
pruefe($neu !== false && $davor !== false && $neu > $davor, 'und zwar nach checkDeactivationTimesGroup (207/208 behalten Vorrang)');

ergebnis();
