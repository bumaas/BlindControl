<?php

declare(strict_types=1);

/*
 * Debug ohne Rohdaten-Dumps: Wochenplan (BlindController::formatScheduleDebug) und Formular.
 *
 * Anlass ist die MCP-Evaluierung vom 01.10.2026: getUpDownTime() schrieb bei jedem Steuerungslauf
 * das vollständige Wochenplan-Ereignis als JSON ins Debug (am nuc rund 2 kB je Lauf, samt Farben
 * und Skripttexten der Schaltaktionen). Gebraucht werden daraus genau zwei Angaben: wann heute
 * laut Plan geöffnet und wann geschlossen wird. Die eigentlichen Entscheidungszeilen gingen
 * dahinter unter.
 *
 * Erwartet: eine kurze Zeile mit Ereignis-ID, Wochentag und den beiden Zeiten; fehlt eine Zeit im
 * Plan, steht das im Klartext da.
 *
 * Aufruf: git submodule update --init (einmalig), dann
 *         php tests/check-debug-schedule.php (Exit-Code 1 bei Fehlern)
 */

require_once __DIR__ . '/harness.php';

function zeile(BlindControllerHarness $m, mixed ...$argumente): string
{
    try {
        return (string)$m->ruf('formatScheduleDebug', ...$argumente);
    } catch (ReflectionException) {
        return ''; // Stand ohne die Kurzfassung
    }
}

$m = neueInstanz();

echo "\nA. Beide Zeiten vorhanden\n";
$text = zeile($m, 27935, 4, '07:30', '23:00');
pruefe(str_contains($text, '#27935'), "nennt das Ereignis ($text)");
pruefe(str_contains($text, '07:30') && str_contains($text, '23:00'), 'nennt beide Zeiten');
pruefe(str_contains($text, 'Donnerstag'), 'nennt den Wochentag beim Namen');
pruefe($text !== '' && strlen($text) < 120, 'ist kurz (' . strlen($text) . ' Zeichen)');

echo "\nB. Zeit fehlt im Plan\n";
$text = zeile($m, 27935, 7, null, '22:00');
pruefe(str_contains($text, 'Sonntag') && str_contains($text, '22:00'), "nennt Tag und vorhandene Zeit ($text)");
pruefe(str_contains($text, 'keine Zeit'), 'sagt im Klartext, dass die Öffnungszeit fehlt');

echo "\nC. Das Ereignis selbst landet nicht mehr im Debug\n";
$methode = new ReflectionMethod(BlindController::class, 'getUpDownTime');
$quelle  = implode('', array_slice(file($methode->getFileName()), $methode->getStartLine() - 1, $methode->getEndLine() - $methode->getStartLine() + 1));
pruefe(!str_contains($quelle, 'json_encode($event'), 'getUpDownTime schreibt das Ereignis nicht mehr als JSON');
pruefe(str_contains($quelle, 'formatScheduleDebug('), 'getUpDownTime nutzt die Kurzfassung');

/* D. Dasselbe beim Formular: GetConfigurationForm schrieb bei jedem Öffnen das ganze Formular
 *    (rund 54 kB) ins Debug. Wer es braucht, holt es mit IPS_GetConfigurationForm. */
echo "\nD. Das Formular landet nicht mehr im Debug\n";
$methode = new ReflectionMethod(BlindController::class, 'GetConfigurationForm');
$quelle  = implode('', array_slice(file($methode->getFileName()), $methode->getStartLine() - 1, $methode->getEndLine() - $methode->getStartLine() + 1));
pruefe(!str_contains($quelle, 'SendDebug'), 'GetConfigurationForm schreibt nichts ins Debug');
$formular = $m->GetConfigurationForm();
pruefe(strlen($formular) > 10000 && json_decode($formular, true) !== null, 'das Formular selbst wird weiter vollständig geliefert (' . strlen($formular) . ' Zeichen)');

ergebnis();
