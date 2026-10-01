<?php

declare(strict_types=1);

/*
 * Auf- und Abzeit aus dem Wochenplan (BlindController::getUpTimeOfDay / getDownTimeOfDay).
 *
 * Anlass (01.10.2026, Blindtest über MCP): Der Wochenplan, den der Knopf "Wochenplan anlegen"
 * erzeugt (seit build 114), hat zwei Schaltpunkte - 07:00 öffnen, 22:00 schließen. Als Abzeit nahm
 * das Modul aber den ZWEITEN Schließen-Punkt eines Tages; es setzte also stillschweigend einen
 * Schließen-Punkt um 00:00 voraus, wie ihn ein in der Konsole angelegter Plan hat. Beim Plan aus
 * dem Knopf gab es keine Abzeit: Der Probelauf zeigte "Tag (07:00–—)", der Rollladen schlösse nach
 * Plan nie.
 *
 * Erwartet: Abzeit ist der erste Schließen-Punkt NACH dem Öffnen-Punkt - gleich, ob davor ein
 * Schließen-Punkt um 00:00 steht. Ein Plan ohne Schließen-Punkt nach dem Öffnen (der im README
 * beschriebene 24-h-Plan für reine Beschattung) hat weiterhin keine Abzeit.
 *
 * Die Fixtures unter tests/fixtures sind echte Mitschnitte von IPS_GetEvent (Quelle in der Datei).
 *
 * Aufruf: git submodule update --init (einmalig), dann
 *         php tests/check-weekly-schedule.php (Exit-Code 1 bei Fehlern)
 */

require_once __DIR__ . '/harness.php';

function gruppen(string $datei): array
{
    return json_decode(file_get_contents(__DIR__ . '/fixtures/' . $datei), true, 512, JSON_THROW_ON_ERROR)['ScheduleGroups'];
}

function punkt(int $stunde, int $minute, int $aktion, int $id): array
{
    return ['ActionID' => $aktion, 'ID' => $id, 'Start' => ['Hour' => $stunde, 'Minute' => $minute, 'Second' => 0]];
}

/** [Aufzeit, Abzeit] eines Wochentags (1 = Montag … 7 = Sonntag) */
function zeiten(BlindControllerHarness $m, int $wochentag, array $gruppen): array
{
    return [$m->ruf('getUpTimeOfDay', $wochentag, $gruppen), $m->ruf('getDownTimeOfDay', $wochentag, $gruppen)];
}

function zeige(array $zeiten): string
{
    return ($zeiten[0] ?? 'keine') . ' / ' . ($zeiten[1] ?? 'keine');
}

$m = neueInstanz();

/* A. In der Konsole angelegter Plan (00:00 zu, auf, zu) - muss unverändert funktionieren */
echo "\nA. Plan aus der Konsole (nuc #27935)\n";
$konsole = gruppen('wochenplan_nuc_27935.json');
$z       = zeiten($m, 4, $konsole);
pruefe($z === ['07:30', '23:00'], 'Donnerstag: auf 07:30, ab 23:00 (' . zeige($z) . ')');
$z = zeiten($m, 6, $konsole);
pruefe($z === ['08:00', '23:30'], 'Samstag: auf 08:00, ab 23:30 (' . zeige($z) . ')');

/* B. Plan aus dem Knopf, wie er im Feld liegt (auf, zu - ohne 00:00-Punkt) */
echo "\nB. Plan aus dem Knopf \"Wochenplan anlegen\" (build 114 bis 139)\n";
$knopf = gruppen('wochenplan_knopf_build139.json');
foreach ([1 => 'Montag', 7 => 'Sonntag'] as $tag => $name) {
    $z = zeiten($m, $tag, $knopf);
    pruefe($z === ['07:00', '22:00'], "$name: auf 07:00, ab 22:00 (" . zeige($z) . ')');
}

/* C. 24-h-Plan für reine Beschattung (README 5.2): nur Öffnen ab 00:00 - bewusst keine Abzeit.
 *    Aufbau nach der README-Beschreibung, Punktformat wie in den Mitschnitten. */
echo "\nC. 24-h-Plan ohne Schließen\n";
$z = zeiten($m, 3, [['Days' => 127, 'ID' => 0, 'Points' => [punkt(0, 0, 2, 0)]]]);
pruefe($z === ['00:00', null], 'auf 00:00, keine Abzeit (' . zeige($z) . ')');

/* D. Schließen-Punkte VOR dem Öffnen zählen nicht als Abzeit. Vorher galt 05:00 als Abzeit, und
 *    weil die vor der Aufzeit liegt, war nie Tag. */
echo "\nD. Zwei Schließen-Punkte vor dem Öffnen\n";
$z = zeiten($m, 2, [['Days' => 127, 'ID' => 0, 'Points' => [punkt(0, 0, 1, 0), punkt(5, 0, 1, 1), punkt(7, 0, 2, 2), punkt(22, 0, 1, 3)]]]);
pruefe($z === ['07:00', '22:00'], 'auf 07:00, ab 22:00 (' . zeige($z) . ')');

/* E. Der Knopf legt künftig den Aufbau an, den auch die Konsole erzeugt: 00:00 zu, 07:00 auf, 22:00 zu */
echo "\nE. Vorgabe des Knopfs\n";
try {
    $vorgabe = $m->ruf('defaultWeeklySchedulePoints');
} catch (ReflectionException) {
    $vorgabe = []; // Stand ohne die Korrektur
}
pruefe($vorgabe === [[0, 0, 1], [7, 0, 2], [22, 0, 1]], 'Punkte 00:00 schließen, 07:00 öffnen, 22:00 schließen (' . json_encode($vorgabe) . ')');
$punkte = [];
foreach ($vorgabe as $nr => [$stunde, $minute, $aktion]) {
    $punkte[] = punkt($stunde, $minute, $aktion, $nr);
}
$z = zeiten($m, 5, [['Days' => 127, 'ID' => 0, 'Points' => $punkte]]);
pruefe($z === ['07:00', '22:00'], 'daraus liest das Modul auf 07:00, ab 22:00 (' . zeige($z) . ')');
$methode = new ReflectionMethod(BlindController::class, 'CreateWeeklySchedule');
$quelle  = implode('', array_slice(file($methode->getFileName()), $methode->getStartLine() - 1, $methode->getEndLine() - $methode->getStartLine() + 1));
pruefe(str_contains($quelle, 'defaultWeeklySchedulePoints()'), 'CreateWeeklySchedule legt genau diese Punkte an');

ergebnis();
