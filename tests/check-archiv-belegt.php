<?php

declare(strict_types=1);

/*
 * Helligkeit bei gesperrtem Archiv (BlindController::GetBrightness).
 *
 * Anlass ist der 01.10.2026: Während der monatlichen Verdichtung war das Archiv knapp eine
 * Minute gesperrt. Jeder AC_*-Aufruf wartete 30 s und scheiterte dann mit der Warnung
 * „Instanz #21496 ist belegt (Zeitüberschreitung beim Warten auf eine parallele Operation)",
 * auch AC_GetLoggingStatus. Auf dem nuc standen dazu um 00:01:22 zehn Warnungen aus
 * BlindController/module.php (AC_GetLoggingStatus in GetBrightness).
 *
 * Erwartet: keine Warnung, und die Helligkeit fällt auf den aktuellen Sensorwert zurück —
 * so, wie es GetBrightness schon tut, wenn AC_GetAggregatedValues nichts liefert.
 *
 * Das gesperrte Archiv ist eine Unterklasse der ArchiveControl aus dem Kernel-Stub, die
 * genau diese Meldung als Warnung auslöst und false liefert.
 *
 * Aufruf: git submodule update --init (einmalig), dann
 *         php tests/check-archiv-belegt.php (Exit-Code 1 bei Fehlern)
 */

require_once __DIR__ . '/harness.php';

// Die Kernmodule (Archive Control u. a.) bringt der Stub als eigene Bibliothek mit
IPS\ModuleLoader::loadLibrary(__DIR__ . '/stubs/CoreStubs/library.json');

const ARCHIV_MODUL = '{43192F0B-135B-4CE7-A0A7-1475603F3060}';

final class BelegtesArchiv extends ArchiveControl
{
    private function belegt(): bool
    {
        trigger_error(
            sprintf('Instanz #%d ist belegt (Zeitüberschreitung beim Warten auf eine parallele Operation)', $this->InstanceID),
            E_USER_WARNING
        );
        return false;
    }

    public function GetLoggingStatus(int $VariableID)
    {
        return $this->belegt();
    }

    public function GetAggregatedValues(int $VariableID, int $AggregationSpan, int $StartTime, int $EndTime, int $Limit)
    {
        return $this->belegt();
    }
}

/** Ersetzt die Schnittstelle einer Stub-Instanz, damit AC_* auf das gesperrte Archiv laufen. */
function sperreArchiv(int $archivID): void
{
    $eigenschaft = new ReflectionProperty(IPS\InstanceManager::class, 'interfaces');
    $eigenschaft->setAccessible(true);
    $schnittstellen             = $eigenschaft->getValue();
    $schnittstellen[$archivID]  = new BelegtesArchiv($archivID);
    $eigenschaft->setValue(null, $schnittstellen);
}

$archivID = IPS_CreateInstance(ARCHIV_MODUL);

$sensor = IPS_CreateVariable(2 /* Float */);
SetValueFloat($sensor, 1234.0);
AC_SetLoggingStatus($archivID, $sensor, true);

$m = neueInstanz();
IPS_SetProperty($m->id(), 'BrightnessID', $sensor);
IPS_SetProperty($m->id(), 'BrightnessAvgMinutes', 10);
IPS_ApplyChanges($m->id());

/* Archiv gesperrt wie am 01.10.2026. Dass der Archivpfad überhaupt erreicht wird, belegt der
 * Rotlauf: Ohne Fix kommt die Warnung aus BelegtesArchiv bis hierher durch. */
echo "\nArchiv gesperrt (monatliche Verdichtung)\n";
sperreArchiv($archivID);
try {
    $hell = $m->ruf('GetBrightness', 'BrightnessID', 'BrightnessAvgMinutes', 0.0, false);
    pruefe(true, 'keine Warnung aus GetBrightness');
    pruefe($hell === 1234.0, 'Rückfall auf den aktuellen Sensorwert 1234 (war ' . var_export($hell, true) . ')');
} catch (Throwable $e) {
    pruefe(false, 'Warnung durchgereicht: ' . $e->getMessage());
}

ergebnis();
