<?php

declare(strict_types=1);

/*
 * Deutsche und englische README laufen nicht auseinander.
 *
 * Seit build 147 gibt es neben README.md die englische Fassung README.en.md (Store-Doku für Englisch).
 * check-readme.php prüft beide nur zusammen; eine Eigenschaft, die nur in einer Sprache beschrieben ist,
 * fiele dort nicht auf. Dieser Test vergleicht, was sich ohne Übersetzung vergleichen lässt:
 *   - gegenseitiger Sprachverweis oben in beiden Dateien;
 *   - dieselben Eigenschaften in der Tabelle (Bezeichner in Backticks in der ersten Spalte);
 *   - dieselben Skriptfunktionen (BLC_…, BLCGM_…) und Statusvariablen-Idents;
 *   - dieselben Bilder und gleich viele Überschriften je Ebene.
 *
 * Aufruf: php tests/check-readme-languages.php (Exit-Code 1 bei Fehlern)
 */

$wurzel = dirname(__DIR__);
$de     = is_file("$wurzel/README.md") ? (string)file_get_contents("$wurzel/README.md") : '';
$en     = is_file("$wurzel/README.en.md") ? (string)file_get_contents("$wurzel/README.en.md") : '';

$pruefungen = 0;
$fehler     = 0;
function pruefe(bool $ok, string $text): void
{
    global $pruefungen, $fehler;
    $pruefungen++;
    if (!$ok) {
        $fehler++;
    }
    echo ($ok ? '  ok   ' : '  FEHL ') . $text . "\n";
}

/** Vergleicht zwei Mengen und nennt, was auf welcher Seite fehlt. */
function gleicheMenge(array $de, array $en, string $was): void
{
    $de = array_values(array_unique($de));
    $en = array_values(array_unique($en));
    sort($de);
    sort($en);
    $nurDe = array_diff($de, $en);
    $nurEn = array_diff($en, $de);
    $text  = sprintf('%s gleich (%d)', $was, count($de));
    if ($nurDe !== []) {
        $text .= ' - fehlt englisch: ' . implode(', ', $nurDe);
    }
    if ($nurEn !== []) {
        $text .= ' - fehlt deutsch: ' . implode(', ', $nurEn);
    }
    pruefe($nurDe === [] && $nurEn === [] && $de !== [], $text);
}

/** Bezeichner in Backticks aus der ersten Spalte der Eigenschaftstabelle. */
function eigenschaften(string $text): array
{
    $namen = [];
    foreach (preg_split('/\R/', $text) as $zeile) {
        if (preg_match('/^\|([^|]*)\|/', $zeile, $m) && preg_match_all('/`([A-Za-z]\w*)`/', $m[1], $t)) {
            array_push($namen, ...$t[1]);
        }
    }
    return $namen;
}

function treffer(string $muster, string $text, int $gruppe = 0): array
{
    preg_match_all($muster, $text, $t);
    return $t[$gruppe];
}

echo "\nA. Beide Fassungen vorhanden und verlinkt\n";
pruefe($de !== '' && $en !== '', 'README.md und README.en.md vorhanden');
pruefe(str_contains(substr($de, 0, 800), '(README.en.md)'), 'die deutsche Fassung verweist oben auf die englische');
pruefe(str_contains(substr($en, 0, 800), '(README.md)'), 'die englische Fassung verweist oben auf die deutsche');

echo "\nB. Gleicher Inhalt, soweit ohne Übersetzung vergleichbar\n";
gleicheMenge(eigenschaften($de), eigenschaften($en), 'Eigenschaften in der Tabelle');
gleicheMenge(treffer('/\bBLC(?:GM)?_\w+/', $de), treffer('/\bBLC(?:GM)?_\w+/', $en), 'Skriptfunktionen');
$idents = '/^#+\s*([A-Z][A-Z_]+)\s*$/m';
gleicheMenge(treffer($idents, $de, 1), treffer($idents, $en, 1), 'Statusvariablen');
gleicheMenge(treffer('/!\[[^]]*\]\(([^)]+)\)/', $de, 1), treffer('/!\[[^]]*\]\(([^)]+)\)/', $en, 1), 'Bilder');
foreach ([2, 3, 4] as $ebene) {
    $muster = '/^' . str_repeat('#', $ebene) . ' /m';
    $nDe    = preg_match_all($muster, $de);
    $nEn    = preg_match_all($muster, $en);
    pruefe($nDe === $nEn, sprintf('Überschriften der Ebene %d: deutsch %d, englisch %d', $ebene, $nDe, $nEn));
}

echo "\n$pruefungen Prüfungen, $fehler Fehler\n";
exit($fehler === 0 ? 0 : 1);
