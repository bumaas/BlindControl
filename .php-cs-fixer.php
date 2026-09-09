<?php

declare(strict_types=1);

/**
 * Schlankes Regelwerk für php-cs-fixer — bewusst NICHT das volle StylePHP von Symcon.
 *
 * Aufgenommen sind nur Regeln, die echte Mängel beheben und keine gewachsene Ordnung
 * antasten. Messung am gesamten Repo (09.09.2026): Diese Auswahl fasst 349 Zeilen an
 * (davon 340 die Ausrichtung der `*` in Doc-Blöcken, 8 falsche Einrückungen und ein
 * doppelt gequoteter String); das volle StylePHP-Regelwerk käme auf ein Vielfaches.
 * Bewusst ausgelassen:
 *
 *  - ordered_class_elements  — sortiert die Klasse nach Sichtbarkeit um; reißt inhaltlich
 *    zusammengehörige Methoden auseinander und entwertet `git blame`.
 *  - binary_operator_spaces  — entfernt die ausgerichteten Zuweisungsspalten; ausgerichtete
 *    Konstantenblöcke sind hier bewusst so geschrieben und besser lesbar.
 *  - cast_spaces, single_space_around_construct, function_declaration, method_argument_space
 *    — Geschmacksfragen ((string)$x → (string) $x, fn( → fn ().
 *
 * `declare_strict_types` ist als „risky" eingestuft; beide Module tragen es bereits, die
 * Regel hält den Stand nur fest. Deshalb läuft der Check mit `--allow-risky=yes`.
 *
 * Fünf Regeln stehen als reine Wächter — sie ändern heute nichts und fangen künftig echte
 * Fehlerklassen ab, nicht Kosmetik:
 *
 *  - encoding            UTF-8 ohne BOM. Ein BOM in einer Moduldatei hat schon einmal eine
 *                        ganze Bibliothek lahmgelegt (MarstekShellyEmulator unter Rust).
 *  - no_closing_tag      kein `?>` am Dateiende — sonst wandern Leerzeichen dahinter in die
 *                        Ausgabe und zerstören JSON-Antworten.
 *  - logical_operators   `and`/`or` → `&&`/`||`; die alten Formen binden schwächer als `=`
 *                        und führen zu still falschen Bedingungen.
 *  - no_alias_functions  echte Funktionsnamen statt `sizeof`, `join`, `is_writeable` —
 *                        einige Aliase sind abgekündigt.
 *  - no_break_comment    ein durchfallendes `case` braucht ein ausdrückliches `// no break`.
 *
 * Zu `line_ending`: Lokal unter Windows meldet die Regel beide Moduldateien, weil der
 * Arbeitsbaum CRLF trägt. Im Repo stehen sie dank `.gitattributes` (`* text=auto`) als LF,
 * in der CI unter Linux greift sie deshalb nicht.
 *
 * Aufruf: php php-cs-fixer.phar fix --dry-run --diff --allow-risky=yes
 *         (ohne --dry-run wird korrigiert)
 */

$finder = PhpCsFixer\Finder::create()
    ->exclude('tests/stubs') // Kernel-Stub von symcon/SymconStubs, fremder Code
    ->exclude('docs')
    ->in(__DIR__);

return (new PhpCsFixer\Config())
    ->setRules([
        'align_multiline_comment'            => ['comment_type' => 'all_multiline'],
        'array_indentation'                  => true,
        'array_syntax'                       => ['syntax' => 'short'],
        'blank_line_after_opening_tag'       => true,
        'constant_case'                      => ['case' => 'lower'],
        'declare_strict_types'               => true,
        'encoding'                           => true,
        'line_ending'                        => true,
        'logical_operators'                  => true,
        'no_alias_functions'                 => true,
        'no_blank_lines_after_class_opening' => true,
        'no_break_comment'                   => true,
        'no_closing_tag'                     => true,
        'no_extra_blank_lines'               => true,
        'no_trailing_whitespace'             => true,
        'no_unneeded_control_parentheses'    => true,
        'single_quote'                       => true,
        'statement_indentation'              => true,
    ])
    ->setFinder($finder);
