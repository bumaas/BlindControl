<?php

declare(strict_types=1);

/*
 * Mit WriteLogInformationToIPSLogger gehen Info-Meldungen zusätzlich, nicht statt ins Symcon-Log in die IPSLibrary.
 *
 * Anlass (01.10.2026, SonyTV 2.2 build 42 mit demselben Logger-Code): Bis 2.51 build 140 landeten Infos bei
 * gesetztem Schalter nur in der IPSLibrary, im Debug und in "Letzte Nachricht". Eine KI über den MCP-Server sieht
 * aber nur das Symcon-Log und einen Debug-Puffer von wenigen Minuten. Das README sagte schon "zusätzlich".
 *
 * Aufruf: git submodule update --init (einmalig), dann
 *         php tests/check-logging.php (Exit-Code 1 bei Fehlern)
 */

/** Attrappe der IPSLibrary: sammelt, was das Modul an den IPSLogger gibt. */
$ipsLogger = [];
function IPSLogger_Inf(string $sender, string $message): void
{
    global $ipsLogger;
    $ipsLogger[] = ['Inf', $message];
}
function IPSLogger_Err(string $sender, string $message): void
{
    global $ipsLogger;
    $ipsLogger[] = ['Err', $message];
}

require_once __DIR__ . '/harness.php';

function symconLog(BlindControllerHarness $m): array
{
    return array_column($m->logsSeitMarke(), 'Message');
}

$m = neueInstanz();

/* A. Schalter aus: nur Symcon-Log */
echo "\nA. Schalter aus\n";
$m->logsZuruecksetzen();
$m->ruf('Logger_Inf', 'Testmeldung A');
pruefe(in_array('Testmeldung A', symconLog($m), true), 'Info im Symcon-Log');
pruefe($ipsLogger === [], 'nichts an den IPSLogger');

/* B. Schalter an: Symcon-Log und IPSLibrary */
echo "\nB. Schalter an\n";
IPS_SetProperty($m->id(), 'WriteLogInformationToIPSLogger', true);
IPS_ApplyChanges($m->id());
$m->logsZuruecksetzen();
$m->ruf('Logger_Inf', 'Testmeldung B');
pruefe(in_array('Testmeldung B', symconLog($m), true), 'Info weiterhin im Symcon-Log');
pruefe(in_array(['Inf', 'Testmeldung B'], $ipsLogger, true), 'Info zusätzlich an den IPSLogger');

$m->logsZuruecksetzen();
$m->ruf('Logger_Err', 'Fehlermeldung B');
pruefe(in_array('Fehlermeldung B', symconLog($m), true) && in_array(['Err', 'Fehlermeldung B'], $ipsLogger, true), 'Fehler wie bisher in beiden Logs');

/* C. Formular sagt „additionally" */
echo "\nC. Formular\n";
$form = (string)file_get_contents(dirname(__DIR__) . '/BlindController/form.json');
pruefe(!str_contains($form, 'instead of standard logfile'), 'kein „instead of standard logfile" mehr');

ergebnis();
