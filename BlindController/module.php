<?php
declare(strict_types=1);

if (function_exists('IPSUtils_Include')){
    IPSUtils_Include('IPSLogger.inc.php', 'IPSLibrary::app::core::IPSLogger');
}

/** @noinspection AutoloadingIssuesInspection */

class BlindController extends IPSModule
{

    //invalid IDs
    private const STATUS_INST_TIMETABLE_ID_IS_INVALID = 201;
    private const STATUS_INST_HOLYDAY_INDICATOR_ID_IS_INVALID = 202;
    private const STATUS_INST_BLIND_LEVEL_ID_IS_INVALID = 203;
    private const STATUS_INST_BRIGHTNESS_ID_IS_INVALID = 204;
    private const STATUS_INST_BRIGHTNESS_THRESHOLD_ID_IS_INVALID = 205;
    private const STATUS_INST_ISDAY_INDICATOR_ID_IS_INVALID = 206;
    private const STATUS_INST_DEACTIVATION_TIME_MANUAL_IS_INVALID = 207;
    private const STATUS_INST_DEACTIVATION_TIME_AUTOMATIC_IS_INVALID = 208;


    // Überschreibt die interne IPS_Create($id) Funktion
    public function Create()
    {
        // Diese Zeile nicht löschen.
        parent::Create();

        $this->RegisterProperties();
        $this->RegisterAttributes();

        $this->RegisterTimer('Update', 0, 'BLC_ControlBlind(' . $this->InstanceID . ');');
    }

    public function ApplyChanges()
    {
        //Never delete this line!
        parent::ApplyChanges();

        if (function_exists('IPSLogger_Inf')){
            IPSLogger_Inf(__FILE__, __FUNCTION__);
        }

        $this->RegisterReferences();

        $this->RegisterVariables();

        if ($this->GetValue('ACTIVATED')) {
            $this->SetTimerInterval('Update', $this->ReadPropertyInteger('UpdateInterval') * 60 * 1000);
        } else {
            $this->SetTimerInterval('Update', 0);
        }

        $this->SetInstanceStatus();
    }

    public function RequestAction($Ident, $Value): bool
    {
        switch (strtoupper($Ident)) {
            case 'ACTIVATED':
                if ($Value) {
                    $this->SetTimerInterval('Update', $this->ReadPropertyInteger('UpdateInterval') * 60 * 1000);
                } else {
                    $this->SetTimerInterval('Update', 0);
                }
                break;
            default:
                trigger_error('Unknown Ident: ' . $Ident);
                return false;
        }
        if (is_bool($Value)) {
            $this->Logger_Dbg(__FUNCTION__, sprintf('Ident: %s, Value: %s', $Ident, (int) $Value));
        } else {
            $this->Logger_Dbg(__FUNCTION__, sprintf('Ident: %s, Value: %s', $Ident, $Value));
        }
        return $this->SetValue($Ident, $Value);
    }

    /**
     * Die folgenden Funktionen stehen automatisch zur Verfügung, wenn das Modul über die "Module Control" eingefügt wurden.
     * Die Funktionen werden, mit dem selbst eingerichteten Prefix, in PHP und JSON-RPC zur Verfügung gestellt:
     */
    public function ControlBlind(): bool
    {

        /*        global $rSunAzimuth;
                global $rSunAltitude;
                global $rTemperature;*/

        //optional Values
        $brightnessID = $this->ReadPropertyInteger('BrightnessID');
        if ($brightnessID) {
            $brightness = GetValue($brightnessID);
        }

        $brightnessThresholdID = $this->ReadPropertyInteger('BrightnessThresholdID');
        if ($brightnessThresholdID) {
            $brightnessThreshold = GetValue($brightnessThresholdID);
        }


        //$Hinweis = '';

        // Eingansparameter prüfen
        /*        if (!ParametersOK($sRoom, $ini)) {
                    return;
                }*/

        if (!IPS_SemaphoreEnter($this->InstanceID . '- Blind', 9 * 1000)) {
            return false;
        }

        // $deactivationTimeAuto: Zeitraum, in dem das automatisch gesetzte Level
        // erhalten bleibt bevor es überschrieben wird.

        $deactivationTimeAuto = $this->ReadPropertyInteger('DeactivationAutomaticMovement') * 60;

        //Ermitteln, welche Zeiten heute und gestern gelten

        $heute_auf   = '';
        $heute_ab    = '';
        $gestern_auf = '';
        $gestern_ab  = '';

        if (!$this->getUpAndDownPoints($heute_auf, $heute_ab, $gestern_auf, $gestern_ab)) {
            return false;
        }

        //Level ID ermitteln
        $idLevel = $this->ReadPropertyInteger('BlindLevelID');

        //Zeitpunkt der letzten Rollladenbewegung
        $tsBlindLastMovement = $this->GetBlindLastTimeStampAndCheckAutomatic($idLevel);

        // Attribut TimestampAutomatik auslesen
        $tsAutomatik = $this->ReadAttributeInteger('AttrTimeStampAutomatic');

        // ...dann prüfen ob neues Level in Aktor geschrieben werden muss.

        // Das aktuelle Level im Jalousieaktor auslesen
        $levelAct = GetValue($idLevel);

        $profile = $this->GetProfileInformation();

        // Das neue Solllevel für den Rollladen festlegen
        // wurde der Rollladen manuell bewegt?
        $bNoMove = $this->isMovementLocked(
            $levelAct, $tsBlindLastMovement, $tsAutomatik, $gestern_ab, $heute_auf, $heute_ab, $profile['LevelClosed'], $profile['LevelOpened']
        );

        if ($bNoMove) {
            $levelNew = $levelAct;
        } else {
            $levelNew = $profile['LevelOpened'];
        }


        // 'tagsüber' ermitteln
        if ($this->ReadPropertyInteger('IsDayIndicatorID') > 0) {
            $isDay = GetValueBoolean($this->ReadPropertyInteger('IsDayIndicatorID'));
        } else {
            $isDay = ($brightness > $brightnessThreshold);
        }


        //        $bWindowOpen = isWindowOpened($aContact); //todo
        $bWindowOpen = null;

        $this->Logger_Dbg(
            __FUNCTION__, sprintf(
                            'gestern_ab: %s, heute_auf: %s, heute_ab: %s, TSAutomatik: %s, levelAct: %s, TSBlind: %s, bWindowOpen: %s'
                            . ', bNoMove: %s, isDay: %s, brightness: %s', $gestern_ab, $heute_auf, $heute_ab, $this->FormatTimeStamp($tsAutomatik),
                            $levelAct, $this->FormatTimeStamp($tsBlindLastMovement), (isset($bWindowOpen) ? (int) $bWindowOpen : 'null'),
                            (int) $bNoMove, (isset($isDay) ? (int) $isDay : 'null'), $brightness ?? 'null'
                        )
        );

        // am Tag (d.h. es ist hell und nach der Öffnungszeit) wird überprüft, ob das Fenster beschattet werden soll
        $now = time();
        if ($isDay && ($now >= strtotime($heute_auf)) && ($now <= strtotime($heute_ab))) {

            $levelNew = $profile['LevelOpened'];

            /*            // soll die Beschattungsfunktion aktiv sein?
                        $bShadowingEnabled = array_key_exists('LevelControlledBySunPosition', $ini[$sIniSection]);

                        $brightnessThreshold = getBrightnessThreshold($levelAct, $rTemperature);
                        if ($bShadowingEnabled && istBeschattungNotwendig($sIniSection, $levelAct, $brightness, $brightness, $brightnessThreshold)) {

                            $bLevelControlledBySunPosition = (boolean) $ini[$sIniSection]['LevelControlledBySunPosition'];

                            if ($bLevelControlledBySunPosition) {
                                $levelNew = getLevelFromSunPosition($ini[$sIniSection], $rSunAzimuth, $rSunAltitude);
                                Logger_Trc("$sIniSection: LevelFromSunPosition (levelNew) = " . $levelNew);
                            } else {
                                // bei Rollläden steht in der Zeitleiste das Öffnungslevel (0..9) in 10% Schritten
                                trigger_error($sIniSection . ': Überprüfen, ob die Berechnung noch richtig ist');
                                $levelNew = (int) getValueFromZeitleiste($ini[$sIniSection]['Zeitleiste'], $ini[$sIniSection]['iWoche']) / 10;
                                if ($levelNew === $this->blindLevelClosed) {
                                    $levelNew = $bladeLevelOpened;
                                }

                                Logger_Trc("$sIniSection: LevelFromZeitleiste (levelNew) = $levelNew");
                            }

                            if ($levelNew < $this->blindLevelOpened) {

                                //wenn Wärmeschutz notwenig oder bereits eingeschaltet und Hysterese nicht unterschritten
                                if (($rTemperature > 27.0) || ((round($levelAct, 2) === round($levelNew, 2) - 0.15) && ($rTemperature > (27.0 - 0.5)))) {
                                    Logger_Dbg("$sIniSection: levelAct: " . round($levelAct, 2) . ', levelNew: ' . round($levelNew, 2));

                                    $levelNew -= 0.15;
                                    $Hinweis   = 'Temp > 27°';
                                }

                                //wenn Hitzeschutz notwenig oder bereits eingeschaltet und Hysterese nicht unterschritten
                                if (($rTemperature > 30.0) || (($levelAct === 0.1) && ($rTemperature > (30.0 - 0.5)))) {
                                    Logger_Dbg("$sIniSection: levelAct: $levelAct");
                                    $levelNew = 0.1;
                                    $Hinweis   = 'Temp > 30°';
                                }
                            }

                        }
            */

            /*            // prüfen, ob Beschattung für TV notwendig
                        if ($tv_position_anfahren) {
                            $rLevelneuTV = $this->blindLevelClosed;

                            $brightnessThreshold = getBrightnessThresholdTV($levelAct);
                            if (istBeschattungNotwendig($sIniSection, $levelAct, $brightness, $brightness, $brightnessThreshold)) {
                                $rLevelneuTV = getLevelForTV($sIniSection, $ini[$sIniSection], $brightness, $brightness);
                            }

                            if ($rLevelneuTV < $levelNew) {
                                $levelNew = $rLevelneuTV;
                                $Hinweis   = 'TV Abdunklung';
                            }

                        }
            */
        }

        /*
                if (array_key_exists('MinLevelWindowOpen', $ini[$sIniSection])) {
                    $BlindLevelWhenWindowIsOpen = $ini[$sIniSection]['MinLevelWindowOpen'];
                } else {
                    $BlindLevelWhenWindowIsOpen = 0.4;
                }

                //prüfen, ob der Rollladen abends unabhängig von der Helligkeit geschlossen werden soll
                $closeBladeClockDependentOnly = false;
                if (getSecsOfDeltaHHMM($sZeit) > getSecsOfDeltaHHMM('12:00')) {
                    if (array_key_exists('CloseBladeClockDependentOnly', $ini[$sIniSection])) {
                        $closeBladeClockDependentOnly = (boolean) $ini[$sIniSection]['CloseBladeClockDependentOnly'];
                    } else {
                        $closeBladeClockDependentOnly = false;
                    }
                }

                //wenn das Script aufgrund einer Änderung des Fensterstatus aufgerufen wurde, soll der Rollladen auf jeden Fall bewegt werden
                if ($_IPS['SENDER'] === 'Variable') {
                    //Bewegung erzwingen
                    $deactivationTimeAuto = 0;
                } else {
                    //Umwandeln sDeltaStdMinAuto in Sekunden
                    $deactivationTimeAuto = getSecsOfDeltaHHMM($sDeltaStdMinAuto);
                }
        */
        // wenn es dunkel ist hängt das Level nur vom Fensterstatus ab
        $closeBladeClockDependentOnly = false;
        if ((!$isDay && !$closeBladeClockDependentOnly) || ($now <= strtotime($heute_auf)) || ($now >= strtotime($heute_ab))) {
            $deactivationTimeAuto = 0;
            if ($bWindowOpen) {
                //$levelNew = $BlindLevelWhenWindowIsOpen;
                //$Hinweis   = 'Fenster geöffnet';
            } else {
                $levelNew = $profile['LevelClosed'];
            }
        } /*elseif ($levelAct === $BlindLevelWhenWindowIsOpen) {
            // es ist hell
            // wenn die Rolladenposition noch auf Lüftungsposition steht
            $deactivationTimeAuto = 0;
        }*/

        /*
        // wenn der Rollladen geschlossen ist oder geschlossen werden soll und das Fenster geöffnet ist, dann
        // wird die Bewegungssperre aufgehoben und das Level auf das Mindestlevel bei geöffnetem Fenster/Tür gesetzt
        if (($levelAct <= $BlindLevelWhenWindowIsOpen) && ($levelNew <= $BlindLevelWhenWindowIsOpen) && $bWindowOpen) {

            Logger_Dbg("$sIniSection: Fenster geöffnet (levelAct: $levelAct, levelNew: $levelNew");
            //Bewegung erzwingen
            $deactivationTimeAuto = 0;
            $bNoMove     = false;
            $levelNew   = $BlindLevelWhenWindowIsOpen;
        }

        if (($BlindLevelWhenWindowIsOpen < $levelNew) && $bWindowOpen && GetValueBoolean(ID_REGEN_UND_WIND)
            && ((time() - GetTimeStampVariableChanged(
                        ID_REGEN_UND_WIND
                    )) >= 180)) {
            $levelNew = $BlindLevelWhenWindowIsOpen;
            $Hinweis   = 'Regen';
            $bNoMove   = false;
            //Bewegung erzwingen
            $deactivationTimeAuto = 0;
        }
*/

        if (!$bNoMove) {
            $level = $levelNew / ($profile['MaxValue'] - $profile['MinValue']);
            if ($profile['Reversed']) {
                $level = 1 - $level;
            }
            $this->CloseBlind((int) ($level * 100), $deactivationTimeAuto);
        }

        IPS_SemaphoreLeave($this->InstanceID . '- Blind');

        return true;

    }


    private function RegisterProperties(): void
    {
        $this->RegisterPropertyInteger('WeeklyTimeTableEventID', 0);
        $this->RegisterPropertyInteger('BlindLevelID', 0);
        $this->RegisterPropertyInteger('WakeUpTimeID', 0);
        $this->RegisterPropertyInteger('UpdateInterval', 1);
        $this->RegisterPropertyInteger('HolidayIndicatorID', 0);
        $this->RegisterPropertyInteger('BrightnessID', 0);
        $this->RegisterPropertyInteger('BrightnessThresholdID', 0);
        $this->RegisterPropertyInteger('IsDayIndicatorID', 0);
        $this->RegisterPropertyInteger('DayUsedWhenHoliday', 0);
        $this->RegisterPropertyInteger('DeactivationAutomaticMovement', 20);
        $this->RegisterPropertyInteger('DeactivationManualMovement', 120);
        $this->RegisterPropertyBoolean('WriteDebugInformationToIPSLogger', false);
        $this->RegisterPropertyBoolean('WriteLogInformationToIPSLogger', false);
    }

    private function RegisterReferences(): void
    {
        $objectIDs = [
            $this->ReadPropertyInteger('WeeklyTimeTableEventID'),
            $this->ReadPropertyInteger('BlindLevelID'),
            $this->ReadPropertyInteger('WakeUpTimeID'),
            $this->ReadPropertyInteger('HolidayIndicatorID'),
            $this->ReadPropertyInteger('BrightnessID'),
            $this->ReadPropertyInteger('BrightnessThresholdID'),
            $this->ReadPropertyInteger('IsDayIndicatorID'),];

        foreach ($this->GetReferenceList() as $ref) {
            $this->UnregisterReference($ref);
        }

        foreach ($objectIDs as $id) {
            if ($id !== 0) {
                $this->RegisterReference($id);
            }
        }
    }

    private function RegisterAttributes(): void
    {
        $this->RegisterAttributeInteger('AttrTimeStampAutomatic', 0);
        $this->RegisterAttributeInteger('AttrTimeStampManual', 0);
    }

    private function RegisterVariables(): void
    {
        $this->RegisterVariableBoolean('ACTIVATED', 'Activated', '~Switch');
        $this->RegisterVariableString('LAST_MESSAGE', 'Last Message');

        $this->EnableAction('ACTIVATED');
    }

    private function SetInstanceStatus(): void
    {

        if ($ret =
            $this->checkVariableId('BlindLevelID', false, [VARIABLETYPE_INTEGER, VARIABLETYPE_FLOAT], self::STATUS_INST_BLIND_LEVEL_ID_IS_INVALID)) {
            $this->SetStatus($ret);
            return;
        }

        if ($ret = $this->checkEventId('WeeklyTimeTableEventID', false, EVENTTYPE_SCHEDULE, self::STATUS_INST_TIMETABLE_ID_IS_INVALID)) {
            $this->SetStatus($ret);
            return;
        }

        if ($ret = $this->checkVariableId('HolidayIndicatorID', true, [VARIABLETYPE_BOOLEAN], self::STATUS_INST_HOLYDAY_INDICATOR_ID_IS_INVALID)) {
            $this->SetStatus($ret);
            return;
        }

        if ($ret = $this->checkVariableId(
            'BrightnessID', true, [VARIABLETYPE_INTEGER, VARIABLETYPE_FLOAT], self::STATUS_INST_BRIGHTNESS_ID_IS_INVALID
        )) {
            $this->SetStatus($ret);
            return;
        }

        if ($ret = $this->checkVariableId(
            'BrightnessThresholdID', true, [VARIABLETYPE_INTEGER, VARIABLETYPE_FLOAT], self::STATUS_INST_BRIGHTNESS_THRESHOLD_ID_IS_INVALID
        )) {
            $this->SetStatus($ret);
            return;
        }

        if ($ret = $this->checkVariableId('IsDayIndicatorID', true, [VARIABLETYPE_BOOLEAN], self::STATUS_INST_ISDAY_INDICATOR_ID_IS_INVALID)) {
            $this->SetStatus($ret);
            return;
        }

        if ($ret = $this->checkRangeInteger('DeactivationManualMovement', 0, 100000, self::STATUS_INST_DEACTIVATION_TIME_MANUAL_IS_INVALID)) {
            $this->SetStatus($ret);
            return;
        }

        if ($ret = $this->checkRangeInteger('DeactivationAutomaticMovement', 0, 100000, self::STATUS_INST_DEACTIVATION_TIME_AUTOMATIC_IS_INVALID)) {
            $this->SetStatus($ret);
            return;
        }

        $this->SetStatus(IS_ACTIVE);

    }

    private function checkVariableId(string $propName, bool $optional, array $variableTypes, int $errStatus): int
    {
        $variableID = $this->ReadPropertyInteger($propName);

        if (!$optional && $variableID === 0) {
            IPS_LogMessage(__CLASS__ . '.' . __FUNCTION__, sprintf('ID nicht gesetzt: %s', $propName));
            return $errStatus;
        }

        if ($variableID !== 0) {

            if (!$variable = @IPS_GetVariable($variableID)) {
                IPS_LogMessage(__CLASS__ . '.' . __FUNCTION__, sprintf('falsche Variablen ID #%s', $propName));
                return $errStatus;
            }

            if (!in_array($variable['VariableType'], $variableTypes, true)) {
                IPS_LogMessage(
                    __CLASS__ . '.' . __FUNCTION__, sprintf('falscher Variablentyp - nur %s erlaubt', implode(', ', $variableTypes))
                );
                return $errStatus;
            }
        }

        return 0;

    }

    private function checkEventId(string $propName, bool $optional, int $eventType, int $errStatus): int
    {
        $eventID = $this->ReadPropertyInteger($propName);

        if (!$optional && $eventID === 0) {
            IPS_LogMessage(__CLASS__ . '.' . __FUNCTION__, sprintf('ID nicht gesetzt: %s', $propName));
            return $errStatus;
        }

        if ($eventID !== 0) {

            if (!$variable = @IPS_GetEvent($eventID)) {
                IPS_LogMessage(__CLASS__ . '.' . __FUNCTION__, sprintf('falsche Event ID #%s', $propName));
                return $errStatus;
            }

            if ($variable['EventType'] !== $eventType) {
                IPS_LogMessage(__CLASS__ . '.' . __FUNCTION__, sprintf('falscher Eventtyp - nur %s erlaubt', $eventType));
                return $errStatus;
            }
        }

        return 0;

    }

    private function checkRangeInteger(string $propName, int $min, int $max, int $errStatus): int
    {
        $value = $this->ReadPropertyInteger($propName);

        if ($value < $min || $value > $max) {
            IPS_LogMessage(__CLASS__ . '.' . __FUNCTION__, sprintf('%s: Wert nicht im gültigen Bereich (%s - %s)', $propName, $min, $max));
            return $errStatus;
        }

        return 0;
    }

    private function isMovementLocked($levelAct, int $tsBlindLastMovement, int $tsAutomatik, string $gestern_ab, string $heute_auf, string $heute_ab,
                                      $blindLevelClosed, $blindLevelOpened): bool
    {

        //zuerst prüfen, ob der Rollladen nach der letzten aut. Bewegung (+60sec) manuell bewegt wurde
        if ($tsBlindLastMovement <= strtotime('+1 minute', $tsAutomatik)) {
            return false;
        }

        $objectName = IPS_GetObject($this->InstanceID)['ObjectName'];

        $deactivationTimeManu = $this->ReadPropertyInteger('DeactivationManualMovement') * 60;

        //Zeitpunkt festhalten, sofern noch nicht geschehen
        if ($tsBlindLastMovement !== $this->ReadAttributeInteger('AttrTimeStampManual')) {
            $this->WriteAttributeInteger('AttrTimeStampManual', $tsBlindLastMovement);

            $this->Logger_Dbg(
                __FUNCTION__, "Rollladenlevel wurde manuell gesetzt - Value: " . $levelAct . ', tsBlindLastMovement: ' . $this->FormatTimeStamp(
                                $tsBlindLastMovement
                            ) . ', TimestampManual: ' . $this->FormatTimeStamp(
                                $this->ReadAttributeInteger('AttrTimeStampManual')
                            ) . ', deactivationTimeManu: ' . (time() - $tsBlindLastMovement) . '/' . $deactivationTimeManu
            );

            if ($levelAct === $blindLevelClosed) {
                $this->Logger_Inf("Der Rollladen '" . $objectName . "' wurde manuell geschlossen.");
            } else if ($levelAct === $blindLevelOpened) {
                $this->Logger_Inf("Der Rollladen '" . $objectName . "' wurde manuell geöffnet.");
            } else {
                $this->Logger_Inf(
                    "Der Rollladen wurde manuell auf " . sprintf('%.0f', 100 * $levelAct) . '% gefahren.'
                );
            }

        }

        $bNoMove          = false;
        $tsManualMovement = $this->ReadAttributeInteger('AttrTimeStampManual');

        if (($tsManualMovement > strtotime($heute_auf)) && ($tsManualMovement < strtotime($heute_ab))) {
            //tagsüber gilt:

            // der Rollladen ist nicht bereits manuell geschlossen worden
            if ($levelAct === $blindLevelClosed) {
                $bNoMove = true;
            } else {
                $bNoMove = ((time() - $tsBlindLastMovement) < $deactivationTimeManu);
            }

            if ($bNoMove) {
                $this->Logger_Dbg(
                    __FUNCTION__, sprintf('Rollladen wurde manuell bewegt (Tag). DeactivationTimeManu: %s/%s', time() - $tsBlindLastMovement,
                    $deactivationTimeManu));
            }

        } elseif (($tsManualMovement > strtotime($heute_ab))
                  || (($tsManualMovement < strtotime($heute_auf))
                      && ($tsManualMovement > strtotime('-1 day', strtotime($gestern_ab))))) {
            //nachts gilt:
            //wenn die Bewegung nachts passiert ist
            $bNoMove = true;
            $this->Logger_Dbg(__FUNCTION__, 'Rollladen wurde manuell bewegt (Nacht)');
        }

        return $bNoMove;

    }

    //-----------------------------------------------
    public function CloseBlind(int $percent, int $deactivationTimeAuto): bool
    {

        $this->Logger_Dbg(__FUNCTION__, sprintf('Parameter percent: %s, deactivationTimeAuto: %s', $percent, $deactivationTimeAuto));

        $objectName = IPS_GetObject($this->InstanceID)['ObjectName'];

        $profile = $this->GetProfileInformation();
        $this->Logger_Dbg(__FUNCTION__, sprintf('Profile: %s', json_encode($profile)));

        if ($profile === false) {
            return false;
        }

        $newLevel = $profile['MinValue'] + ($percent / 100) * ($profile['MaxValue'] - $profile['MinValue']);

        if ($profile['Reversed']) {
            $newLevel = $profile['MaxValue'] - $newLevel;
        }

        $levelID             = $this->ReadPropertyInteger('BlindLevelID');
        $actualLevel         = GetValue($levelID); //integer and float are supported
        $tsBlindLastMovement = IPS_GetVariable($levelID)['VariableChanged'];
        $LeveldiffPercentage = abs($newLevel - $actualLevel) / ($profile['MaxValue'] - $profile['MinValue']);
        $timediff            = time() - $tsBlindLastMovement;

        $this->Logger_Dbg(__FUNCTION__, sprintf('actualLevel: %s, newLevel: %s', $actualLevel, $newLevel));

        $ret = true;

        // Wenn sich das aktuelle Level um mehr als 5% von neuem Level unterscheidet
        if (($LeveldiffPercentage > 0.05) && ($timediff >= $deactivationTimeAuto)) {

            // Level setzen
            //Wert übertragen
            if (@RequestAction($levelID, $newLevel)) {
                // Timestamp der Automatik merken (sonst wird die Bewegung später als manuelle Bewegung erkannt)
                $this->WriteAttributeInteger('AttrTimeStampAutomatic', time());
                $this->Logger_Dbg(
                    __FUNCTION__, "$objectName: TimestampAutomatik: " . $this->ReadAttributeInteger('AttrTimeStampAutomatic')
                );

                $this->WriteInfo($newLevel, $profile['LevelClosed'], $profile['LevelOpened']);
            } else {
                $this->Logger_Dbg(
                    __FUNCTION__, 'Fehler beim Setzen der Werte. (id = ' . $levelID . ', Value = ' . $percent . ')'
                );
                $ret = false;
            }
            $this->Logger_Dbg(__FUNCTION__, $objectName . ': ' . $actualLevel . ' to ' . $newLevel);

            // kleine Pause, um Kommunikationsstörungen zu vermeiden
            sleep(5);

        } else {
            $this->Logger_Dbg(
                __FUNCTION__, "DeactivationTimeAuto: $timediff " . '/' . $deactivationTimeAuto . ', LeveldiffPercentage: ' . $LeveldiffPercentage
            );
        }

        return $ret;
    }

    private function WriteInfo($rLevelneu, $blindLevelClosed, $blindLevelOpened)
    {
        $objectName = IPS_GetObject($this->InstanceID)['ObjectName'];

        if ($this->ReadPropertyInteger('BrightnessID') > 0){
            $brightness = sprintf (' (%s)', GetValueFormatted($this->ReadPropertyInteger('BrightnessID')));
        } else {
            $brightness = '';
        }

        if ($rLevelneu === $blindLevelClosed) {
            $this->Logger_Inf("Der Rollladen '" . $objectName . "' wurde geschlossen." . $brightness);
        } else if ($rLevelneu === $blindLevelOpened) {
            $this->Logger_Inf("Der Rollladen '" . $objectName . "' wurde geöffnet." . $brightness);
        } else {
            $this->Logger_Inf("Der Rollladen '" . $objectName . "' wurde auf " . sprintf('%.0f', 100 * $rLevelneu) . '% gefahren.');
        }
    }

    //-------------------------------------
    private function getUpAndDownPoints(?string &$heute_auf, ?string &$heute_ab, ?string &$gestern_auf, ?string &$gestern_ab): bool
    {

        // An Feiertagen und Urlaubstagen können abweichende Tage gelten
        $holidayIndicatorID = $this->ReadPropertyInteger('HolidayIndicatorID');
        if (($holidayIndicatorID !== 0) && ($this->ReadPropertyInteger('DayUsedWhenHoliday') !== 0)
            && GetValueBoolean(
                $this->ReadPropertyInteger('HolidayIndicatorID')
            )) {
            $weekDay = $this->ReadPropertyInteger('DayUsedWhenHoliday');
        } else {
            $weekDay = (int) date('N');
        }

        if (!$this->getUpDownTime($weekDay, $heute_auf, $heute_ab)) {
            return false;
        }

        //Ermitteln, welche Zeiten gestern galten
        if (!$this->getUpDownTime((int) date('N', strtotime('-1 day')), $gestern_auf, $gestern_ab)) {
            return false;
        }

        return true;
    }

    //-----------------------------------------------
    private function getUpDownTime(int $weekDay, ?string &$auf, ?string &$ab): bool
    {
        global $idAufstehzeit;

        $weeklyTimeTableEventId = $this->ReadPropertyInteger('WeeklyTimeTableEventID');
        if (!$event = @IPS_GetEvent($weeklyTimeTableEventId)) {
            trigger_error(sprintf('falsche Event ID #%s', $weeklyTimeTableEventId));
            return false;
        }

        if ($event['EventType'] !== EVENTTYPE_SCHEDULE) {
            trigger_error('falscher Eventtype');
            return false;
        }

        if ($idAufstehzeit > 0) {
            $auf = date('H:i', strtotime(GetValueFormatted($idAufstehzeit)) + AUFSTEHZEIT_VORLAUF * 60);
        } else {
            $auf = $this->getUpTimeOfDay($weekDay, $event['ScheduleGroups']);
        }

        $ab = $this->getDownTimeOfDay($weekDay, $event['ScheduleGroups']);

        return true;
    }


    /**
     * @param int   $weekDay
     * @param array $groups
     *
     * @return string|null
     */
    private function getUpTimeOfDay(int $weekDay, array $groups): ?string
    {

        $weekDay = 2 ** ($weekDay - 1);

        foreach ($groups as $group) {
            if ($group['Days'] & $weekDay) {
                foreach ($group['Points'] as $point) {
                    if ($point['ActionID'] === 1) {
                        return sprintf("%'.02s:%'.02s", $point['Start']['Hour'], $point['Start']['Minute']);
                    }
                }
            }
        }
        return null;
    }

    /**
     * @param int   $weekDay
     * @param array $groups
     *
     * @return string|null
     */
    private function getDownTimeOfDay(int $weekDay, array $groups): ?string
    {

        $weekDay = 2 ** ($weekDay - 1);

        $count = 0;
        foreach ($groups as $group) {
            if ($group['Days'] & $weekDay) {
                foreach ($group['Points'] as $point) {
                    if ($point['ActionID'] === 2) {
                        $count++;
                        if ($count === 2) {
                            return sprintf("%'.02s:%'.02s", $point['Start']['Hour'], $point['Start']['Minute']);
                        }
                    }
                }
            }
        }
        return null;
    }

    private function GetProfileInformation(): ?array
    {

        $variable = IPS_GetVariable($this->ReadPropertyInteger('BlindLevelID'));

        if ($variable['VariableCustomProfile'] !== '') {
            $profileName = $variable['VariableCustomProfile'];
        } else {
            $profileName = $variable['VariableProfile'];
        }

        if ($profileName === null) {
            return null;
        }

        $profile          = IPS_GetVariableProfile($profileName);
        $profileNameParts = explode('.', $profileName);

        $reversed = strcasecmp('reversed', end($profileNameParts)) === 0;
        return [
            'Name'        => $profileName,
            'ProfileType' => $profile['ProfileType'],
            'MinValue'    => $profile['MinValue'],
            'MaxValue'    => $profile['MaxValue'],
            'Reversed'    => $reversed,
            'LevelOpened' => $reversed ? $profile['MaxValue'] : $profile['MinValue'],
            'LevelClosed' => $reversed ? $profile['MinValue'] : $profile['MaxValue']];


    }

    //-----------------------------------------------
    private function GetBlindLastTimeStampAndCheckAutomatic(int $id_Level): int
    {
        $tsBlindLevelChanged = IPS_GetVariable($id_Level)['VariableChanged'];

        //prüfen, ob Automatik nach der letzten Rollladenbewegung eingestellt wurde.
        $tsAutomatikVariable  = IPS_GetVariable(IPS_GetObjectIDByIdent('ACTIVATED', $this->InstanceID))['VariableChanged'];
        $tsAutomaticAttribute = $this->ReadAttributeInteger('AttrTimeStampAutomatic');
        if ($this->GetValue('ACTIVATED') && ($tsAutomatikVariable > $tsBlindLevelChanged) && ($tsAutomaticAttribute !== $tsBlindLevelChanged)) {
            // .. dann Timestamp Automatik mit Timestamp des Rollladens gleichsetzen
            $this->WriteAttributeInteger('AttrTimeStampAutomatic', $tsBlindLevelChanged);
            $this->Logger_Inf('Rollladenautomatik wurde eingestellt');
        }
        return $tsBlindLevelChanged;
    }

    private function FormatTimeStamp(int $ts): string
    {
        return date('Y-m-d H:i:s', $ts);
    }


    private function Logger_Inf(string $message): void
    {
        $this->SendDebug('LOG_INFO', $message, 0);
        if ($this->ReadPropertyBoolean('WriteLogInformationToIPSLogger') && function_exists('IPSLogger_Inf')){
            IPSLogger_Inf(basename(__FILE__, '.php'), $message);
        } else {
            $this->LogMessage($message, KL_MESSAGE);
        }

        $this->SetValue('LAST_MESSAGE', $message);
    }

    private function Logger_Dbg(string $message, string $data): void
    {
        $this->SendDebug($message, $data, 0);
        if ($this->ReadPropertyBoolean('WriteDebugInformationToIPSLogger') && function_exists('IPSLogger_Dbg')){
            IPSLogger_Dbg(basename(__FILE__, '.php'), $data);
        }
    }

}