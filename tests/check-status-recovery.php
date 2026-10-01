<?php

declare(strict_types=1);

/*
 * Meldung nach behobener Fehlkonfiguration (BlindController::logRecoveryFromErrorStatus).
 *
 * Anlass (01.10.2026): Die Statusvariable "Letzte Nachricht" ist ein Protokoll - Fahrten und Fehler
 * landen in derselben Variable. Nach einer korrigierten Fehlkonfiguration blieb der Fehlertext
 * stehen, bis die nächste Fahrt ihn überschrieb; bei ausgeschalteter Automatik also unbegrenzt,
 * obwohl der Instanzstatus längst wieder in Ordnung war.
 *
 * Erwartet: Wechselt die Instanz aus einem Fehlerstatus (>= 200) in einen gültigen Status, schreibt
 * das Modul einmal "Konfiguration ist gültig". Kam die Instanz nicht aus einem Fehlerstatus,
 * schreibt es nichts - sonst füllte jedes Übernehmen das archivierte Protokoll.
 *
 * Eine vollständig gültige Konfiguration trägt der Kernel-Stub nicht (seine Ereignisfunktionen sind
 * Attrappen, IPS_GetEvent liefert ein leeres Array). Geprüft wird deshalb die Entscheidung selbst
 * und ihre Einbindung in die Statusermittlung; der Durchstich ist an der Anlage belegt.
 *
 * Aufruf: git submodule update --init (einmalig), dann
 *         php tests/check-status-recovery.php (Exit-Code 1 bei Fehlern)
 */

require_once __DIR__ . '/harness.php';

const MELDUNG = 'Konfiguration ist gültig.';

/** Ruft die Entscheidung mit dem bisherigen Status; liefert die dabei geschriebenen Nachrichten. */
function nachrichtenBei(BlindControllerHarness $m, int $bisherigerStatus): array
{
    $m->writes = [];
    $m->logsZuruecksetzen();
    try {
        $m->ruf('logRecoveryFromErrorStatus', $bisherigerStatus);
    } catch (ReflectionException) {
        // Stand ohne die Meldung: Es wird nichts geschrieben
    }
    return [
        'variable' => array_values(array_filter(array_map(static fn(array $w): string => $w[0] === 'LAST_MESSAGE' ? (string)$w[1] : '', $m->writes))),
        'log'      => array_column($m->logsSeitMarke(), 'Message'),
    ];
}

function enthaelt(array $texte, string $gesucht): bool
{
    foreach ($texte as $t) {
        if (str_contains($t, $gesucht)) {
            return true;
        }
    }
    return false;
}

$m = neueInstanz();

/* A. Aus einem Fehlerstatus heraus wird die Behebung gemeldet */
echo "\nA. Vorher Fehlerstatus\n";
foreach ([203 => 'Rollladen-Variable ungültig', 243 => 'Wert außerhalb des Bereichs'] as $status => $bedeutung) {
    $n = nachrichtenBei($m, $status);
    pruefe(enthaelt($n['variable'], MELDUNG), "vorher $status ($bedeutung): „Letzte Nachricht“ meldet die Behebung (" . json_encode($n['variable'], JSON_UNESCAPED_UNICODE) . ')');
    pruefe(count($n['variable']) === 1, "vorher $status: genau eine Nachricht");
    pruefe(enthaelt($n['log'], MELDUNG), "vorher $status: die Meldung steht auch im Log");
}

/* B. Ohne Fehlerstatus vorher bleibt das Protokoll unberührt */
echo "\nB. Vorher kein Fehlerstatus\n";
foreach ([102 => 'aktiv', 104 => 'Automatik aus', 101 => 'wird erstellt'] as $status => $bedeutung) {
    $n = nachrichtenBei($m, $status);
    pruefe($n['variable'] === [] && $n['log'] === [], "vorher $status ($bedeutung): keine Nachricht (" . json_encode($n, JSON_UNESCAPED_UNICODE) . ')');
}

/* C. Solange die Konfiguration fehlerhaft bleibt, meldet niemand eine Behebung. Die nackte Instanz
 *    steht auf 203; ein weiteres Übernehmen ändert daran nichts. */
echo "\nC. Fehler besteht fort\n";
$m->writes = [];
$m->logsZuruecksetzen();
IPS_ApplyChanges($m->id());
pruefe(in_array(203, $m->status, true), 'Instanz steht weiter auf 203');
pruefe(!enthaelt(array_column($m->logsSeitMarke(), 'Message'), MELDUNG), 'keine Behebung gemeldet');

/* D. Einbindung: Der bisherige Status wird vor den Prüfungen gelesen, die Meldung kommt erst nach
 *    dem Setzen des gültigen Status. */
echo "\nD. Einbindung in die Statusermittlung\n";
$methode = new ReflectionMethod(BlindController::class, 'SetInstanceStatusAndTimerEvent');
$quelle  = implode('', array_slice(file($methode->getFileName()), $methode->getStartLine() - 1, $methode->getEndLine() - $methode->getStartLine() + 1));
$lesen   = strpos($quelle, 'GetStatus()');
$erste   = strpos($quelle, 'checkBlindLevelGroup()');
$final   = strpos($quelle, 'configureTimersAndFinalStatus()');
$melden  = strpos($quelle, 'logRecoveryFromErrorStatus(');
pruefe($lesen !== false && $erste !== false && $lesen < $erste, 'bisheriger Status wird vor der ersten Prüfung gelesen');
pruefe($melden !== false && $final !== false && $melden > $final, 'Meldung folgt auf das Setzen des gültigen Status');

ergebnis();
