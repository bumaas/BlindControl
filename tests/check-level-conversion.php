<?php

declare(strict_types=1);

/*
 * Umrechnung zwischen Profilwerten und Prozent (BlindController).
 *
 * Erster Regressionstest auf dem offiziellen Kernel-Stub. Diese Strecke ist bewusst
 * gewählt: Sie entscheidet, auf welchen Rohwert eine Prozentangabe gefahren wird — ein
 * Fehler hier fährt den Rollladen falsch, und zwar still. Rollladenprofile gibt es in
 * drei Ausprägungen, alle drei kommen im Bestand vor:
 *
 *   0..100   die üblichen Prozentprofile (~Shutter u. ä.)
 *   0..1     Profile mit Anteil statt Prozent (etwa Homematic-Rollläden)
 *   100..0   invertierte Profile, bei denen 0 „offen" bedeutet (reversed)
 *
 * Geprüft wird der Rundgang: Prozent → Rohwert → Prozent muss wieder denselben Wert
 * ergeben, und die Randwerte müssen sauber klemmen statt über das Profil hinauszulaufen.
 *
 * Aufruf: git submodule update --init (einmalig), dann
 *         php tests/check-level-conversion.php (Exit-Code 1 bei Fehlern)
 */

require_once __DIR__ . '/harness.php';

$m = neueInstanz();

$profile = [
    'Prozentprofil 0..100' => ['MinValue' => 0, 'MaxValue' => 100],
    'Anteilprofil 0..1'    => ['MinValue' => 0, 'MaxValue' => 1],
    'invertiert 100..0'    => ['MinValue' => 100, 'MaxValue' => 0],
];

/* A. Die Instanz entsteht überhaupt: Create() und ApplyChanges() laufen am Stub durch.
 *    Ohne Konfiguration meldet das Modul 203 (Rollladen-Variable ungültig) — genau das
 *    ist der erwartete Zustand einer nackten Instanz. */
echo "\nA. Instanz am Kernel-Stub\n";
pruefe($m->id() > 0, 'Instanz angelegt (ID ' . $m->id() . ')');
pruefe(in_array(203, $m->status, true), 'Status 203 „Rollladen-Variable ungültig" bei leerer Konfiguration (' . implode(',', $m->status) . ')');

/* B. Rundgang Prozent → Rohwert → Prozent */
echo "\nB. Rundgang Prozent → Rohwert → Prozent\n";
foreach ($profile as $name => $p) {
    foreach ([0, 25, 50, 75, 100] as $prozent) {
        $roh     = $m->positionAusProzent((float)$prozent, $p);
        $zurueck = $m->normalisiere($roh, $p);
        pruefe(
            $zurueck === $prozent,
            sprintf('%s: %d %% → %s → %d %%', $name, $prozent, rtrim(rtrim(sprintf('%.4f', $roh), '0'), '.'), $zurueck)
        );
    }
}

/* C. Randwerte: Was außerhalb des Profils liegt, muss klemmen — sonst käme ein Rohwert
 *    heraus, den der Aktor nicht kennt. */
echo "\nC. Werte außerhalb des Profils klemmen\n";
foreach ($profile as $name => $p) {
    $min = min($p['MinValue'], $p['MaxValue']);
    $max = max($p['MinValue'], $p['MaxValue']);
    pruefe($m->positionAusProzent(-50.0, $p) >= $min, $name . ': -50 % bleibt im Profil');
    pruefe($m->positionAusProzent(200.0, $p) <= $max, $name . ': 200 % bleibt im Profil');
    pruefe($m->normalisiere((float)($max * 10), $p) <= 100, $name . ': Rohwert über MaxValue ergibt höchstens 100 %');
    pruefe($m->normalisiere((float)($min - $max), $p) >= 0, $name . ': Rohwert unter MinValue ergibt mindestens 0 %');
}

/* D. Das invertierte Profil dreht die Richtung wirklich um. */
echo "\nD. Invertiertes Profil dreht die Richtung\n";
$inv = $profile['invertiert 100..0'];
pruefe($m->positionAusProzent(0.0, $inv) === 100.0, '0 % entspricht dem Rohwert 100');
pruefe($m->positionAusProzent(100.0, $inv) === 0.0, '100 % entspricht dem Rohwert 0');
pruefe($m->normalisiere(100.0, $inv) === 0, 'Rohwert 100 entspricht 0 %');
pruefe($m->normalisiere(0.0, $inv) === 100, 'Rohwert 0 entspricht 100 %');

/* E. Entartetes Profil (MinValue === MaxValue) darf nicht durch Null teilen. */
echo "\nE. Entartetes Profil\n";
$entartet = ['MinValue' => 50, 'MaxValue' => 50];
pruefe($m->normalisiere(50.0, $entartet) === 0, 'MinValue === MaxValue ergibt 0 % statt einer Division durch Null');

ergebnis();
