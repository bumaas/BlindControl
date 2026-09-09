<?php

declare(strict_types=1);

/*
 * Gemeinsamer Testrahmen: bindet BlindController an den offiziellen Kernel-Stub
 * (symcon/SymconStubs, Submodul tests/stubs, auf einen festen Commit gepinnt) und macht die
 * privaten Rechenmethoden für Tests aufrufbar. Der Kernel-Stub trägt Properties, Attribute,
 * Variablen, Profile und Debug; der Harness zeichnet nur auf, was der Stub nicht beobachtbar
 * macht (SetValue, SetStatus, SetTimerInterval).
 *
 * Der Stub ist strenger als eine handgeschriebene Attrappe: RegisterVariable verlangt
 * existierende Profile mit passendem Typ, SetValue castet nicht (TypeError statt stiller
 * Umwandlung), ReadAttribute* wirft bei unregistriertem Attribut. Befunde des Stubs sind
 * deshalb Modulfehler und werden im Modul behoben, nicht im Test kaschiert.
 *
 * Einbinden mit require_once __DIR__ . '/harness.php'; Instanzen über neueInstanz().
 */

require_once __DIR__ . '/stubs/autoload.php';

// PHP-Warnungen und -Notices des Moduls sollen Tests abbrechen, nicht still durchlaufen.
// E_USER_NOTICE bleibt außen vor: Der Stub meldet so einen unbekannten Ident und liefert
// false — das ist beim Registrieren der normale Ablauf.
set_error_handler(static function (int $nr, string $text, string $datei, int $zeile): bool {
    if (!(error_reporting() & $nr)) {
        return false; // mit @ unterdrückt — kein Testfehler
    }
    if ($nr & (E_USER_ERROR | E_USER_WARNING | E_WARNING | E_NOTICE)) {
        throw new ErrorException($text, 0, $nr, $datei, $zeile);
    }
    return false;
});

require_once dirname(__DIR__) . '/BlindController/module.php';

final class BlindControllerHarness extends BlindController
{
    public const MODULE_ID = '{538F6461-5410-4F4C-91D3-B39122152D56}'; // BlindController/module.json

    /** @var list<array{0: string, 1: mixed}> jedes SetValue */
    public array $writes = [];
    /** @var list<int> jedes SetStatus (auch die aus Create/ApplyChanges) */
    public array $status = [];
    /** @var array<string, int> letztes SetTimerInterval je Timer */
    public array $timer = [];

    private int $logOffset = 0;

    public function id(): int
    {
        return $this->InstanceID;
    }

    /* --- Zugriff auf die privaten Rechenmethoden -----------------------------------------
     * Die Umrechnung zwischen Profilwerten und Prozent ist die Stelle, an der ein Fehler den
     * Rollladen tatsächlich falsch fahren lässt — deshalb der erste Prüfstein. Die Methoden
     * sind `private`; der Zugriff läuft bewusst über Reflection, damit das Modul für die
     * Tests nicht aufgeweicht werden muss (kein private → protected nur fürs Testen).
     */
    public function ruf(string $methode, mixed ...$argumente): mixed
    {
        $r = new ReflectionMethod(BlindController::class, $methode);
        $r->setAccessible(true);
        return $r->invoke($this, ...$argumente);
    }

    public function normalisiere(float $position, array $profil): int
    {
        return $this->ruf('calculateNormalizedLevel', $position, $profil);
    }

    public function positionAusProzent(float $prozent, array $profil): float
    {
        return $this->ruf('calculateProfilePositionByPercent', $prozent, $profil);
    }

    /* --- Stub-Overrides: Signaturen exakt wie tests/stubs/ModuleStrictStubs.php ----------
     * Achtung, IPSModuleStrict weicht von IPSModule ab: SetTimerInterval hat zwei Parameter
     * und gibt bool zurück, SetStatus nimmt int und gibt bool. Eine abweichende Signatur
     * ist ein Fatal beim Klassenladen.
     */

    protected function getTime(): int
    {
        return time(); // RegisterTimer/SetTimerInterval brauchen eine Uhr
    }

    protected function SetValue(string $Ident, mixed $Value): bool
    {
        $ok             = parent::SetValue($Ident, $Value); // typstreng: TypeError statt Cast
        $this->writes[] = [$Ident, $Value];
        return $ok;
    }

    protected function SetStatus(int $Status): bool
    {
        $this->status[] = $Status;
        return parent::SetStatus($Status);
    }

    protected function SetTimerInterval(string $Ident, int $Milliseconds): bool
    {
        $this->timer[$Ident] = $Milliseconds;
        return parent::SetTimerInterval($Ident, $Milliseconds);
    }

    /** Startpunkt für das Protokoll des nächsten Testabschnitts setzen. */
    public function logsZuruecksetzen(): void
    {
        $this->logOffset = count(IPS\LogServer::getLogMessages((string)$this->InstanceID));
    }

    /** @return list<array{Message: string, Type: int}> Einträge seit dem letzten Zurücksetzen */
    public function logsSeitMarke(): array
    {
        return array_values(array_slice(IPS\LogServer::getLogMessages((string)$this->InstanceID), $this->logOffset));
    }

    /** Systemprofile, die der Stub nicht mitbringt (sein ProfileManager startet leer) */
    public static function systemProfile(): void
    {
        $profile = [
            '~Shutter'           => VARIABLETYPE_INTEGER,
            '~Intensity.100'     => VARIABLETYPE_INTEGER,
            '~Intensity.1'       => VARIABLETYPE_FLOAT,
            '~Switch'            => VARIABLETYPE_BOOLEAN,
            '~UnixTimestampTime' => VARIABLETYPE_INTEGER,
            '~UnixTimestamp'     => VARIABLETYPE_INTEGER,
            '~Illumination'      => VARIABLETYPE_FLOAT,
            '~Temperature'       => VARIABLETYPE_FLOAT,
        ];
        foreach ($profile as $name => $typ) {
            if (!IPS_VariableProfileExists($name)) {
                IPS_CreateVariableProfile($name, $typ);
            }
        }
    }
}

/** Legt eine eingerichtete Instanz im Kernel-Stub an (Create + ApplyChanges laufen in createInstance). */
function neueInstanz(): BlindControllerHarness
{
    BlindControllerHarness::systemProfile();
    $id = IPS\ObjectManager::registerObject(1 /* Instance */);
    IPS\InstanceManager::createInstance($id, [
        'ModuleID'   => BlindControllerHarness::MODULE_ID,
        'ModuleName' => 'BlindController',
        'ModuleType' => 3,
        'Class'      => BlindControllerHarness::class,
    ]);
    return IPS\InstanceManager::getInstanceInterface($id);
}

/* --- Minimaler Testrunner (kein PHPUnit, wie im ganzen Repo) --------------------------- */

$pruefungen = 0;
$fehler     = [];

function pruefe(bool $ok, string $text): void
{
    global $pruefungen, $fehler;
    $pruefungen++;
    if (!$ok) {
        $fehler[] = $text;
    }
    echo($ok ? '  ok   ' : '  FEHL ') . $text . "\n";
}

/** Schlusszeile und Exit-Code */
function ergebnis(): never
{
    global $pruefungen, $fehler;
    echo "\n$pruefungen Prüfungen, " . count($fehler) . " Fehler\n";
    exit($fehler === [] ? 0 : 1);
}

IPS\Kernel::reset(); // einmal je Testlauf; weitere Instanzen entstehen im selben Kernel
