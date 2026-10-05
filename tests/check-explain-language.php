<?php

declare(strict_types=1);

/*
 * Erklärung (BLC_ExplainControlBlind) und Ablaufprotokoll in der Sprache der Anlage.
 *
 * Anlass (05.10.2026): Kopf- und Hinweiszeilen der Erklärung liefen über Translate(), die eigentlichen
 * Entscheidungszeilen ("Aktuelle Position: …", "Tageszeit: …", "Bewegungssperre: …", "Ergebnis: …") standen
 * fest auf Deutsch im Code. Eine englische Anlage bekam also eine gemischte Erklärung. Dieselben Zeilen landen
 * in der Statusvariable "Letzte Entscheidung" und im HTML-Ablaufprotokoll.
 *
 * Erwartet:
 *   A. Statisch: In den Funktionen, deren Text im Ablaufprotokoll landet, steht kein Textliteral außerhalb von
 *      Translate() (Debug-Ausgaben ausgenommen).
 *   B. Die Bausteine der Erklärung liefern mit dem Stub (Translate gibt den Schlüssel zurück) englischen Text
 *      und mit locale.json den bisherigen deutschen Wortlaut.
 *
 * Einen ganzen Steuerlauf trägt der Kernel-Stub nicht (seine Ereignisfunktionen sind Attrappen), deshalb werden
 * die Bausteine einzeln aufgerufen; der Durchstich ist an der Anlage belegt.
 *
 * Aufruf: git submodule update --init (einmalig), dann
 *         php tests/check-explain-language.php (Exit-Code 1 bei Fehlern)
 */

require_once __DIR__ . '/harness.php';

/* A. Statisch: kein freies Textliteral auf dem Weg ins Ablaufprotokoll */
echo "\nA. Textliterale auf dem Trace-Pfad\n";

const TRACE_FUNKTIONEN = [
    'executeControlBlindRun', 'calculateBasePosition', 'applyShadowingLogic', 'applyContactLogic', 'formatContactLabels',
    'contactFunctionLabel', 'getSingleContactPosition', 'getPositionsOfShadowingBySunPosition', 'buildShadowingBrightnessInfo',
    'buildThresholdTempNote', 'GetBrightness', 'getPositionsOfShadowingByBrightness', 'shouldBlockMovement',
    'isSameMovementRecently', 'shouldPerformMovement', 'buildUnconfirmedMoveTrace', 'buildDayStateTrace',
    'traceDecisionResult', 'buildDecisionTraceHtml', 'describeTargetPositions', 'describeLevel', 'formatTraceTime',
];
// Aufrufe, deren Argumente nicht ins Ablaufprotokoll gehen bzw. keine Anzeigetexte sind
const AUSGENOMMENE_AUFRUFE = [
    'Translate', 'Logger_Dbg', 'Logger_Err', 'Logger_Inf', 'SendDebug', 'trigger_error', 'date', 'strtotime', 'array_column',
    'IPS_GetInstanceListByModuleID', 'json_encode', 'json_decode', 'htmlspecialchars', 'getDefinedContacts',
    'ReadPropertyInteger', 'ReadPropertyFloat', 'ReadPropertyBoolean', 'ReadPropertyString', 'ReadAttributeInteger',
    'ReadAttributeString', 'ReadAttributeBoolean', 'WriteAttributeInteger', 'WriteAttributeString', 'WriteAttributeBoolean',
];

/** @return list<array{0: int, 1: string, 2: string}> [Zeile, Funktion, Literal] */
function freieLiterale(string $datei): array
{
    $t        = token_get_all((string)file_get_contents($datei));
    $n        = count($t);
    $funde    = [];
    $aktuell  = null;
    $tiefe    = 0;
    $klammer  = 0;
    $offen    = [];
    for ($i = 0; $i < $n; $i++) {
        $x = is_array($t[$i]) ? $t[$i][1] : $t[$i];
        $syntax = is_array($t[$i]) ? null : $t[$i]; // Klammern in interpolierten Strings zählen nicht
        if ($aktuell === null) {
            if (is_array($t[$i]) && $t[$i][0] === T_FUNCTION) {
                $j = $i + 1;
                while (is_array($t[$j]) && $t[$j][0] === T_WHITESPACE) {
                    $j++;
                }
                if (is_array($t[$j]) && in_array($t[$j][1], TRACE_FUNKTIONEN, true)) {
                    $aktuell = $t[$j][1];
                    while ($t[$i] !== '{') {
                        $i++;
                    }
                    $tiefe = 1;
                }
            }
            continue;
        }
        if ($syntax === '{' || (is_array($t[$i]) && in_array($t[$i][0], [T_CURLY_OPEN, T_DOLLAR_OPEN_CURLY_BRACES], true))) {
            $tiefe++;
        } elseif ($syntax === '}') {
            $tiefe--;
            if ($tiefe === 0) {
                $aktuell = null;
                continue;
            }
        }
        if (is_array($t[$i]) && $t[$i][0] === T_STRING && in_array($x, AUSGENOMMENE_AUFRUFE, true)) {
            $offen[] = $klammer;
        }
        if ($syntax === '(') {
            $klammer++;
        } elseif ($syntax === ')') {
            $klammer--;
            if ($offen !== [] && end($offen) === $klammer) {
                array_pop($offen);
            }
        }
        if ($offen !== [] || !is_array($t[$i]) || !in_array($t[$i][0], [T_CONSTANT_ENCAPSED_STRING, T_ENCAPSED_AND_WHITESPACE], true)) {
            continue;
        }
        $text = trim($x, "'\"");
        if (!preg_match('/\p{L}{2,}/u', $text) || str_starts_with(ltrim($text), '<') || str_contains($text, 'style=')
            || preg_match('/^[a-z-]+:[^;]*;/', $text) || preg_match('/^\{[0-9A-F-]{36}\}$/', $text)) {
            continue; // reine Formatzeichen, HTML-/CSS-Gerüst, GUID
        }
        // Array-Schlüssel ('key' => …) und Indizes ($a['key'])
        $k = $i + 1;
        while (is_array($t[$k] ?? null) && $t[$k][0] === T_WHITESPACE) {
            $k++;
        }
        $p = $i - 1;
        while (is_array($t[$p] ?? null) && $t[$p][0] === T_WHITESPACE) {
            $p--;
        }
        if ((is_array($t[$k] ?? null) && $t[$k][0] === T_DOUBLE_ARROW) || (($t[$p] ?? null) === '[' && ($t[$k] ?? null) === ']')) {
            continue;
        }
        // Vergleiche mit Kennungen (=== 'abc') sind keine Anzeigetexte
        if (is_array($t[$p] ?? null) && in_array($t[$p][0], [T_IS_IDENTICAL, T_IS_NOT_IDENTICAL, T_IS_EQUAL, T_IS_NOT_EQUAL], true)) {
            continue;
        }
        $funde[] = [$t[$i][2], $aktuell, $x];
    }
    return $funde;
}

$funde = freieLiterale(dirname(__DIR__) . '/BlindController/module.php');
foreach ($funde as [$zeile, $funktion, $literal]) {
    echo "       Zeile $zeile ($funktion): $literal\n";
}
pruefe($funde === [], 'kein Anzeigetext außerhalb von Translate() (' . count($funde) . ' gefunden)');

/* B. Bausteine in beiden Sprachen */
echo "\nB. Bausteine englisch (Stub) und deutsch (locale.json)\n";

$m = neueInstanz();
// Profil wie ~Shutter: 0 = geöffnet, 100 = geschlossen; Lamellen ebenso
$m->setzeEigenschaft('profileBlindLevel', ['MinValue' => 0, 'MaxValue' => 100]);
$m->setzeEigenschaft('profileSlatsLevel', ['MinValue' => 0, 'MaxValue' => 100]);

$dayState = [
    'isDay'               => true,
    'isDayByTimeSchedule' => true,
    'isDayByDayDetection' => false,
    'brightness'          => null,
    'brightnessAvgNote'   => '',
    'scheduleAuf'         => '07:00',
    'scheduleAb'          => '21:30',
];

/** Ruft die Bausteine und liefert ihre Texte; das Ergebnis des letzten Laufs kommt aus dem Ablaufprotokoll. */
function bausteine(BlindControllerHarness $m, array $dayState): array
{
    $texte = [
        'describeLevel offen'      => $m->ruf('describeLevel', 0.0, ['MinValue' => 0, 'MaxValue' => 100]),
        'describeLevel zu'         => $m->ruf('describeLevel', 100.0, ['MinValue' => 0, 'MaxValue' => 100]),
        'describeLevel 40'         => $m->ruf('describeLevel', 40.0, ['MinValue' => 0, 'MaxValue' => 100]),
        'Höhe und Lamellen'        => $m->ruf('describeTargetPositions', ['BlindLevel' => 40.0, 'SlatsLevel' => 100.0]),
        'Tageszeit'                => $m->ruf('buildDayStateTrace', $dayState),
        'gestern'                  => $m->ruf('formatTraceTime', strtotime('yesterday 08:15')),
        'Kontaktlabel Öffnen'      => $m->ruf('contactFunctionLabel', true),
        'Kontaktlabel Schließen'   => $m->ruf('contactFunctionLabel', false),
        'Kontakt ohne Label'       => $m->ruf('formatContactLabels', []),
    ];

    $m->setzeEigenschaft('decisionTrace', []);
    $m->setzeEigenschaft('moveSkipReason', '');
    $m->ruf('traceDecisionResult', true, '', ['BlindLevel' => 100.0, 'SlatsLevel' => 100.0], '');
    $m->ruf('traceDecisionResult', false, '', ['BlindLevel' => 100.0, 'SlatsLevel' => 100.0], 'X');
    $texte['Ergebnis Sperre'] = $m->eigenschaft('decisionTrace')[0];
    $texte['Ergebnis Fahrt']  = $m->eigenschaft('decisionTrace')[1];
    $html                     = $m->ruf('buildDecisionTraceHtml');
    $texte['Ergebnis fett']   = str_contains($html, 'font-weight:bold;"><td') ? 'hervorgehoben' : 'nicht hervorgehoben';
    return $texte;
}

BlindControllerHarness::$sprache = null;
$englisch                        = bausteine($m, $dayState);
BlindControllerHarness::$sprache = 'de';
$deutsch                         = bausteine($m, $dayState);
BlindControllerHarness::$sprache = null;

// Deutsch: Wortlaut wie bis build 142 (aus dem bisherigen Code übernommen)
$erwartetDeutsch = [
    'describeLevel offen'    => 'geöffnet',
    'describeLevel zu'       => 'geschlossen',
    'describeLevel 40'       => '40 % geschlossen',
    'Höhe und Lamellen'      => 'Höhe 40 % geschlossen, Lamellen geschlossen',
    'Tageszeit'              => 'Tag (Wochenplan: Tag (07:00–21:30), Tagerkennung: Nacht)',
    'gestern'                => 'gestern 08:15',
    'Kontaktlabel Öffnen'    => 'Öffnen-Kontakt',
    'Kontaktlabel Schließen' => 'Schließen-Kontakt',
    'Kontakt ohne Label'     => 'Kontakt offen',
    'Ergebnis Sperre'        => 'Ergebnis: Keine Fahrt: Bewegungssperre aktiv.',
    'Ergebnis Fahrt'         => 'Ergebnis: Fahrt: geschlossen (X).',
    'Ergebnis fett'          => 'hervorgehoben',
];
foreach ($erwartetDeutsch as $baustein => $text) {
    pruefe($deutsch[$baustein] === $text, "deutsch, $baustein: " . json_encode($deutsch[$baustein], JSON_UNESCAPED_UNICODE));
}

// Englisch: kein deutsches Wort, kein Umlaut
$deutscheWoerter = '/[äöüÄÖÜß]|\b(geschlossen|offen|Tag|Nacht|Wochenplan|Tagerkennung|gestern|Kontakt|Ergebnis|Keine|Fahrt|Bewegungssperre|aktiv|Höhe|Lamellen)\b/u';
foreach ($englisch as $baustein => $text) {
    if ($baustein === 'Ergebnis fett') {
        pruefe($text === 'hervorgehoben', 'englisch: Ergebniszeile im HTML hervorgehoben');
        continue;
    }
    pruefe(!preg_match($deutscheWoerter, $text), "englisch, $baustein: " . json_encode($text, JSON_UNESCAPED_UNICODE));
}

ergebnis();
