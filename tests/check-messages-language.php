<?php

declare(strict_types=1);

/*
 * „Letzte Nachricht“ und Symcon-Log in der Sprache der Anlage.
 *
 * Anlass (05.10.2026): Seit 2.51 build 143 laufen Erklärung, Hinweise (Tag/Nacht/WS) und Kontaktlabels über
 * Translate(). Die Meldungen von WriteInfo() und die übrigen Logger_Inf/Logger_Err-Texte standen aber weiter fest
 * auf Deutsch im Code. Eine englische Anlage bekam also Mischtext wie „'X' wurde geschlossen (Day)“.
 *
 * Erwartet:
 *   A. Statisch: Jede Meldung an Logger_Inf/Logger_Err beginnt mit Translate() (direkt oder als sprintf-Format)
 *      oder reicht eine Variable weiter; in WriteInfo() steht kein Textliteral außerhalb von Translate().
 *   B. Die Meldungen liefern mit dem Stub (Translate gibt den Schlüssel zurück) englischen Text und mit
 *      locale.json den bisherigen deutschen Wortlaut — im Log wie in „Letzte Nachricht“.
 *
 * Aufruf: git submodule update --init (einmalig), dann
 *         php tests/check-messages-language.php (Exit-Code 1 bei Fehlern)
 */

require_once __DIR__ . '/harness.php';

const MODUL = __DIR__ . '/../BlindController/module.php';

/* A. Statisch */
echo "\nA. Meldungstexte im Quelltext\n";

/** Index des nächsten Tokens, das kein Leerraum/Kommentar ist. */
function naechstes(array $t, int $i): int
{
    while (isset($t[$i]) && is_array($t[$i]) && in_array($t[$i][0], [T_WHITESPACE, T_COMMENT, T_DOC_COMMENT], true)) {
        $i++;
    }
    return $i;
}

/** Prüft, ob ab Index $i `$this->Translate(` steht. */
function istTranslate(array $t, int $i): bool
{
    $i = naechstes($t, $i);
    if (!is_array($t[$i]) || $t[$i][1] !== '$this') {
        return false;
    }
    $i = naechstes($t, $i + 1);
    if (!is_array($t[$i]) || $t[$i][0] !== T_OBJECT_OPERATOR) {
        return false;
    }
    $i = naechstes($t, $i + 1);
    return is_array($t[$i]) && $t[$i][1] === 'Translate';
}

$t      = token_get_all((string)file_get_contents(MODUL));
$n      = count($t);
$roh    = [];
$aufrufe = 0;
for ($i = 0; $i < $n; $i++) {
    if (!is_array($t[$i]) || $t[$i][0] !== T_STRING || !in_array($t[$i][1], ['Logger_Inf', 'Logger_Err'], true)) {
        continue;
    }
    $p = $i - 1;
    while (is_array($t[$p]) && $t[$p][0] === T_WHITESPACE) {
        $p--;
    }
    if (is_array($t[$p]) && $t[$p][0] === T_FUNCTION) {
        continue; // Deklaration
    }
    $k = naechstes($t, $i + 1);
    if ($t[$k] !== '(') {
        continue;
    }
    $aufrufe++;
    $a = naechstes($t, $k + 1);
    if (istTranslate($t, $a)) {
        continue;
    }
    if (is_array($t[$a]) && $t[$a][1] === 'sprintf') {
        $b = naechstes($t, $a + 1);
        if ($t[$b] === '(' && istTranslate($t, $b + 1)) {
            continue;
        }
    }
    if (is_array($t[$a]) && $t[$a][0] === T_VARIABLE && $t[$a][1] !== '$this' && in_array($t[naechstes($t, $a + 1)], [')', ','], true)) {
        continue;
    }
    $roh[] = $t[$i][2];
}
foreach ($roh as $zeile) {
    echo "       Zeile $zeile: Meldung ohne Translate()\n";
}
pruefe($aufrufe > 20, "Meldungsaufrufe gefunden ($aufrufe)");
pruefe($roh === [], 'jede Meldung an Logger_Inf/Logger_Err läuft über Translate() (' . count($roh) . ' ohne)');

// WriteInfo: kein Textliteral außerhalb von Translate()
$frei   = [];
$tiefe  = 0;
$innen  = false;
$offen  = [];
$klammer = 0;
for ($i = 0; $i < $n; $i++) {
    $x = is_array($t[$i]) ? $t[$i][1] : $t[$i];
    if (!$innen) {
        if (is_array($t[$i]) && $t[$i][0] === T_FUNCTION && ($t[naechstes($t, $i + 1)][1] ?? '') === 'WriteInfo') {
            while ($t[$i] !== '{') {
                $i++;
            }
            $innen = true;
            $tiefe = 1;
        }
        continue;
    }
    if ($x === '{' || (is_array($t[$i]) && in_array($t[$i][0], [T_CURLY_OPEN, T_DOLLAR_OPEN_CURLY_BRACES], true))) {
        $tiefe++;
    } elseif ($x === '}') {
        if (--$tiefe === 0) {
            break;
        }
    }
    if (is_array($t[$i]) && $t[$i][0] === T_STRING && $x === 'Translate') {
        $offen[] = $klammer;
    }
    if ($x === '(') {
        $klammer++;
    } elseif ($x === ')') {
        $klammer--;
        if ($offen !== [] && end($offen) === $klammer) {
            array_pop($offen);
        }
    }
    if ($offen === [] && is_array($t[$i]) && in_array($t[$i][0], [T_CONSTANT_ENCAPSED_STRING, T_ENCAPSED_AND_WHITESPACE], true)
        && preg_match('/\p{L}{2,}/u', trim($x, "'\""))) {
        $p = $i - 1;
        while (is_array($t[$p]) && $t[$p][0] === T_WHITESPACE) {
            $p--;
        }
        if (is_array($t[$p]) && in_array($t[$p][0], [T_IS_IDENTICAL, T_IS_NOT_IDENTICAL], true)) {
            continue; // Vergleich mit einer Kennung
        }
        if ($t[$p] === '[' && $t[naechstes($t, $i + 1)] === ']') {
            continue; // Index ($profile['MinValue'])
        }
        $frei[] = $t[$i][2] . ': ' . $x;
    }
}
foreach ($frei as $f) {
    echo "       WriteInfo, Zeile $f\n";
}
pruefe($frei === [], 'WriteInfo: kein Textliteral außerhalb von Translate() (' . count($frei) . ' gefunden)');

/* B. Meldungen in beiden Sprachen */
echo "\nB. Meldungen englisch (Stub) und deutsch (locale.json)\n";

$m = neueInstanz();
$m->setzeEigenschaft('profileBlindLevel', ['MinValue' => 0, 'MaxValue' => 100]);
$m->setzeEigenschaft('profileSlatsLevel', ['MinValue' => 0, 'MaxValue' => 100]);
$m->setzeEigenschaft('objectName', 'Küche');

/** Ruft eine Methode und liefert die dabei ins Symcon-Log und in „Letzte Nachricht“ geschriebenen Texte. */
function meldung(BlindControllerHarness $m, string $methode, mixed ...$argumente): string
{
    $m->writes = [];
    $m->logsZuruecksetzen();
    $m->ruf($methode, ...$argumente);
    $log      = array_column($m->logsSeitMarke(), 'Message');
    $variable = array_values(array_filter(array_map(static fn (array $w): string => $w[0] === 'LAST_MESSAGE' ? (string)$w[1] : '', $m->writes)));
    $text     = end($log) ?: '';
    return ($variable !== [] && end($variable) !== $text) ? $text . ' ≠ ' . end($variable) : $text;
}

function meldungen(BlindControllerHarness $m): array
{
    return [
        'Fahrt zu mit Hinweis' => meldung($m, 'WriteInfo', 'BlindLevelID', 100.0, 'X'),
        'Fahrt auf'            => meldung($m, 'WriteInfo', 'BlindLevelID', 0.0, ''),
        'Fahrt 40 %'           => meldung($m, 'WriteInfo', 'BlindLevelID', 40.0, ''),
        'Lamellen zu'          => meldung($m, 'WriteInfo', 'SlatsLevelID', 100.0, ''),
        'manuell zu'           => meldung($m, 'logManualMovementInfo', 100.0, null),
        'manuell auf'          => meldung($m, 'logManualMovementInfo', 0.0, null),
        'manuell 40 %'         => meldung($m, 'logManualMovementInfo', 40.0, null),
        'manuell mit Lamellen' => meldung($m, 'logManualMovementInfo', 40.0, 100.0),
        'Konfiguration gültig' => meldung($m, 'logRecoveryFromErrorStatus', 203),
    ];
}

BlindControllerHarness::$sprache = null;
$englisch                        = meldungen($m);
BlindControllerHarness::$sprache = 'de';
$deutsch                         = meldungen($m);
BlindControllerHarness::$sprache = null;

// Deutsch: Wortlaut wie bis build 143 (aus dem bisherigen Code übernommen)
$erwartetDeutsch = [
    'Fahrt zu mit Hinweis' => "'Küche' wurde geschlossen (X)",
    'Fahrt auf'            => "'Küche' wurde geöffnet.",
    'Fahrt 40 %'           => "'Küche' wurde auf 40% gefahren.",
    'Lamellen zu'          => "Die Lamellen 'Küche' wurden geschlossen.",
    'manuell zu'           => "'Küche' wurde manuell geschlossen.",
    'manuell auf'          => "'Küche' wurde manuell geöffnet.",
    'manuell 40 %'         => "'Küche' wurde manuell auf 40% gefahren.",
    'manuell mit Lamellen' => "'Küche' wurde manuell auf 40%(Höhe), 100%(Lamellen) gefahren.",
    'Konfiguration gültig' => "'Küche': Konfiguration ist gültig.",
];
foreach ($erwartetDeutsch as $fall => $text) {
    pruefe($deutsch[$fall] === $text, "deutsch, $fall: " . json_encode($deutsch[$fall], JSON_UNESCAPED_UNICODE));
}

// Englisch: kein deutsches Wort (der Objektname „Küche“ ist ausgenommen)
$deutscheWoerter = '/\b(wurde|wurden|geschlossen|geöffnet|gefahren|manuell|Lamellen|Höhe|Konfiguration|gültig|Die)\b/u';
foreach ($englisch as $fall => $text) {
    pruefe($text !== '' && !preg_match($deutscheWoerter, $text), "englisch, $fall: " . json_encode($text, JSON_UNESCAPED_UNICODE));
}

ergebnis();
