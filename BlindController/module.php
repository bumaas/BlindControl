<?PHP
declare(strict_types=1);

if (function_exists('IPSUtils_Include')) {
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
    private const STATUS_INST_TIMETABLE_IS_INVALID = 209;
    private const STATUS_INST_CONTACT1_ID_IS_INVALID = 210;
    private const STATUS_INST_CONTACT2_ID_IS_INVALID = 211;
    private const STATUS_INST_WAKEUPTIME_ID_IS_INVALID = 212;
    private const STATUS_INST_SLEEPTIME_ID_IS_INVALID = 213;
    private const STATUS_INST_ACTIVATORIDSHADOWINGBRIGHTNESS_IS_INVALID = 214;
    private const STATUS_INST_BRIGHTNESSIDSHADOWINGBRIGHTNESS_IS_INVALID = 215;
    private const STATUS_INST_THRESHOLDIDHIGHBRIGHTNESS_IS_INVALID = 216;
    private const STATUS_INST_THRESHOLDIDLESSRIGHTNESS_IS_INVALID = 217;


    // Überschreibt die interne IPS_Create($id) Funktion
    public function Create()
    {
        // Diese Zeile nicht löschen.
        parent::Create();

        $this->RegisterProperties();
        $this->RegisterAttributes();

        $this->RegisterTimer('Update', 0, 'BLC_ControlBlind(' . $this->InstanceID . ', true);');
    }

    public function ApplyChanges()
    {
        //Never delete this line!
        parent::ApplyChanges();

        if (function_exists('IPSLogger_Inf')) {
            IPSLogger_Inf(__FILE__, __FUNCTION__);
        }

        $this->RegisterReferences();
        $this->RegisterMessages();
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
        if (strtoupper($Ident) === 'ACTIVATED') {
            if ($Value) {
                $this->SetTimerInterval('Update', $this->ReadPropertyInteger('UpdateInterval') * 60 * 1000);
            } else {
                $this->SetTimerInterval('Update', 0);
            }
        } else {
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

    public function MessageSink($TimeStamp, $SenderID, $Message, $Data)
    {
        if (json_decode($this->GetBuffer('LastMessage')) === [$SenderID, $Message, $Data]) {
            $this->Logger_Dbg(
                __FUNCTION__, sprintf(
                                'Duplicate Message: Timestamp: %s, SenderID: %s, Message: %s, Data: %s', $TimeStamp, $SenderID, $Message,
                                json_encode($Data)
                            )
            );
            return;
        }

        $this->SetBuffer('LastMessage', json_encode([$SenderID, $Message, $Data]));

        $this->Logger_Dbg(
            __FUNCTION__, sprintf(
                            'Timestamp: %s, SenderID: %s[%s], Message: %s, Data: %s', $TimeStamp, IPS_GetObject($SenderID)['ObjectName'], $SenderID,
                            $Message, json_encode($Data)
                        )
        );

        $this->SetInstanceStatus();

        if ($this->GetValue('ACTIVATED')) {
            if (in_array($SenderID, [$this->ReadPropertyInteger('Contact1ID'), $this->ReadPropertyInteger('Contact2ID')], true)) {
                $this->ControlBlind(false);
            } else {
                $this->ControlBlind(true);
            }
        }
    }

    /**
     * Die folgenden Funktionen stehen automatisch zur Verfügung, wenn das Modul über die "Module Control" eingefügt wurden.
     * Die Funktionen werden, mit dem selbst eingerichteten Prefix, in PHP und JSON-RPC zur Verfügung gestellt:
     *
     * @param bool $considerDeactivationTimes
     *
     * @return bool
     */
    public function ControlBlind(bool $considerDeactivationTimes): bool
    {

        if (IPS_GetInstance($this->InstanceID)['InstanceStatus'] !== IS_ACTIVE) {
            return false;
        }
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

        if ($considerDeactivationTimes) {
            $deactivationTimeAuto = $this->ReadPropertyInteger('DeactivationAutomaticMovement') * 60;
        } else {
            $deactivationTimeAuto = 0;
        }

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
        $levelAct = (float) GetValue($idLevel);

        $profile = $this->GetProfileInformation();

        // Das neue Solllevel für den Rollladen festlegen
        // wurde der Rollladen manuell bewegt?
        $bNoMove = $this->isMovementLocked(
            $levelAct, $tsBlindLastMovement, $tsAutomatik, $gestern_ab, $heute_auf, $heute_ab, $profile['LevelClosed'], $profile['LevelOpened']
        );

        // 'tagsüber' ermitteln
        // Wochenplan auswerten
        $isDay = (time() >= strtotime($heute_auf)) && (time() <= strtotime($heute_ab));

        // DayIndikator auswerten
        if ($this->ReadPropertyInteger('IsDayIndicatorID') > 0) {
            $isDay = $isDay && GetValueBoolean($this->ReadPropertyInteger('IsDayIndicatorID'));
        } elseif (isset($brightness, $brightnessThreshold)) {
            $isDay = $isDay && ($brightness > $brightnessThreshold);
        }

        if ($bNoMove) {
            $levelNew = $levelAct;
        } else if ($isDay) {
            $levelNew = $profile['LevelOpened'];
        } else {
            $levelNew = $profile['LevelClosed'];
        }


        $isContactOpen    = $this->isContactOpen();
        $contactOpenLevel = $this->ReadPropertyFloat('ContactOpenLevel');

        $this->Logger_Dbg(
            __FUNCTION__, sprintf(
                            'gestern_ab: %s, heute_auf: %s, heute_ab: %s, TSAutomatik: %s, levelAct: %s, TSBlind: %s, isContactOpen: %s'
                            . ', contactOpenLevel: %s, bNoMove: %s, isDay: %s, brightness: %s, brightnessThreshold: %s', $gestern_ab, $heute_auf,
                            $heute_ab, $this->FormatTimeStamp($tsAutomatik), $levelAct, $this->FormatTimeStamp($tsBlindLastMovement),
                            (isset($isContactOpen) ? (int) $isContactOpen : 'null'), $contactOpenLevel, (int) $bNoMove,
                            (isset($isDay) ? (int) $isDay : 'null'), $brightness ?? 'null', $brightnessThreshold ?? 'null'
                        )
        );

        // am Tag wird überprüft, ob das Fenster beschattet werden soll
        if ($isDay) {

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

            // prüfen, ob Beschattung bei Helligkeit notwendig
            $levelShadowingBrightness = $this->getLevelOfShadowingByBrightness($levelNew);
            if ($profile['Reversed']) {
                $levelNew = min($levelNew, $levelShadowingBrightness);
            } else {
                $levelNew = max($levelNew, $levelShadowingBrightness);
            }

        }

        /*

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
        if (!$isDay && !$closeBladeClockDependentOnly) {
            $deactivationTimeAuto = 0;
            if ($isContactOpen) {
                $levelNew = $contactOpenLevel;
                //$Hinweis  = 'Fenster geöffnet';
            } else {
                $levelNew = $profile['LevelClosed'];
            }
        } elseif ($levelAct === $contactOpenLevel) {
            // es ist hell
            // wenn die Rolladenposition noch auf Lüftungsposition steht
            $deactivationTimeAuto = 0;
        }

        // wenn  das Fenster geöffnet ist und der Rollladen unter dem ContactOpen Level steht, dann
        // wird die Bewegungssperre aufgehoben und das Level auf das Mindestlevel bei geöffnetem Fenster/Tür gesetzt
        if (($levelNew <= $contactOpenLevel) && $isContactOpen) {

            $deactivationTimeAuto = 0;
            $bNoMove              = false;
            $levelNew             = $contactOpenLevel;
            $this->Logger_Dbg(__FUNCTION__, "Fenster geöffnet (levelAct: $levelAct, levelNew: $levelNew)");
        }

        /*        if (($contactOpenLevel < $levelNew) && $isContactOpen && GetValueBoolean(ID_REGEN_UND_WIND)
                    && ((time() - GetTimeStampVariableChanged(
                                ID_REGEN_UND_WIND
                            )) >= 180)) {
                    $levelNew = $contactOpenLevel;
                    $Hinweis   = 'Regen';
                    $bNoMove   = false;
                    //Bewegung erzwingen
                    $deactivationTimeAuto = 0;
                }*/

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
        $this->RegisterPropertyInteger('WakeUpTimeOffset', 0);
        $this->RegisterPropertyInteger('BedTimeID', 0);
        $this->RegisterPropertyInteger('BedTimeOffset', 0);
        $this->RegisterPropertyInteger('UpdateInterval', 1);
        $this->RegisterPropertyInteger('HolidayIndicatorID', 0);
        $this->RegisterPropertyInteger('BrightnessID', 0);
        $this->RegisterPropertyInteger('BrightnessThresholdID', 0);
        $this->RegisterPropertyInteger('IsDayIndicatorID', 0);
        $this->RegisterPropertyInteger('DayUsedWhenHoliday', 0);
        $this->RegisterPropertyInteger('DeactivationAutomaticMovement', 20);
        $this->RegisterPropertyInteger('DeactivationManualMovement', 120);
        $this->RegisterPropertyInteger('Contact1ID', 0);
        $this->RegisterPropertyInteger('Contact2ID', 0);
        $this->RegisterPropertyFloat('ContactOpenLevel', 0);
        $this->RegisterPropertyInteger('ActivatorIDShadowingBrightness', 0);
        $this->RegisterPropertyInteger('BrightnessIDShadowingBrightness', 0);
        $this->RegisterPropertyInteger('ThresholdIDHighBrightness', 0);
        $this->RegisterPropertyInteger('ThresholdIDLessBrightness', 0);
        $this->RegisterPropertyFloat('LevelHighBrightnessShadowingBrightness', 0);
        $this->RegisterPropertyFloat('LevelLessBrightnessShadowingBrightness', 0);

        $this->RegisterPropertyBoolean('WriteDebugInformationToIPSLogger', false);
        $this->RegisterPropertyBoolean('WriteLogInformationToIPSLogger', false);
    }

    private function RegisterReferences(): void
    {
        $objectIDs = [
            $this->ReadPropertyInteger('WeeklyTimeTableEventID'),
            $this->ReadPropertyInteger('BlindLevelID'),
            $this->ReadPropertyInteger('WakeUpTimeID'),
            $this->ReadPropertyInteger('BedTimeID'),
            $this->ReadPropertyInteger('HolidayIndicatorID'),
            $this->ReadPropertyInteger('BrightnessID'),
            $this->ReadPropertyInteger('BrightnessThresholdID'),
            $this->ReadPropertyInteger('IsDayIndicatorID'),
            $this->ReadPropertyInteger('Contact1ID'),
            $this->ReadPropertyInteger('Contact2ID'),
            $this->ReadPropertyInteger('ActivatorIDShadowingBrightness'),
            $this->ReadPropertyInteger('BrightnessIDShadowingBrightness'),
            $this->ReadPropertyInteger('ThresholdIDHighBrightness'),
            $this->ReadPropertyInteger('ThresholdIDLessBrightness'),];

        foreach ($this->GetReferenceList() as $ref) {
            $this->UnregisterReference($ref);
        }

        foreach ($objectIDs as $id) {
            if ($id !== 0) {
                $this->RegisterReference($id);
            }
        }
    }

    private function RegisterMessages(): void
    {
        $objectIDs = [
            $this->ReadPropertyInteger('WeeklyTimeTableEventID'),
            $this->ReadPropertyInteger('HolidayIndicatorID'),
            $this->ReadPropertyInteger('BrightnessID'),
            $this->ReadPropertyInteger('BrightnessThresholdID'),
            $this->ReadPropertyInteger('IsDayIndicatorID'),
            $this->ReadPropertyInteger('Contact1ID'),
            $this->ReadPropertyInteger('Contact2ID'),
            $this->ReadPropertyInteger('ActivatorIDShadowingBrightness'),
            $this->ReadPropertyInteger('BrightnessIDShadowingBrightness'),
            $this->ReadPropertyInteger('ThresholdIDHighBrightness'),
            $this->ReadPropertyInteger('ThresholdIDLessBrightness'),];

        foreach ($this->GetMessageList() as $senderId => $msgs) {
            foreach ($msgs as $msg) {
                $this->UnregisterMessage($senderId, $msg);
            }
        }

        foreach ($objectIDs as $id) {
            if ($id !== 0) {
                switch (IPS_GetObject($id)['ObjectType']) {
                    case OBJECTTYPE_EVENT:
                        $this->RegisterMessage($id, EM_UPDATE);
                        break;
                    case OBJECTTYPE_VARIABLE:
                        $this->RegisterMessage($id, VM_UPDATE);
                        break;
                    default:
                        trigger_error(sprintf('Unknown ObjectType %s of id %s', IPS_GetObject($id)['ObjectType'], $id));
                }
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

        if ($ret = $this->checkVariableId('WakeUpTimeID', true, [VARIABLETYPE_STRING], self::STATUS_INST_WAKEUPTIME_ID_IS_INVALID)) {
            $this->SetStatus($ret);
            return;
        }

        if ($ret = $this->checkVariableId('BedTimeID', true, [VARIABLETYPE_STRING], self::STATUS_INST_SLEEPTIME_ID_IS_INVALID)) {
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

        if ($ret = $this->checkVariableId(
            'Contact1ID', true, [VARIABLETYPE_BOOLEAN, VARIABLETYPE_INTEGER, VARIABLETYPE_FLOAT], self::STATUS_INST_CONTACT1_ID_IS_INVALID
        )) {
            $this->SetStatus($ret);
            return;
        }

        if ($ret = $this->checkVariableId(
            'Contact2ID', true, [VARIABLETYPE_BOOLEAN, VARIABLETYPE_INTEGER, VARIABLETYPE_FLOAT], self::STATUS_INST_CONTACT2_ID_IS_INVALID
        )) {
            $this->SetStatus($ret);
            return;
        }

        if ($ret = $this->checkVariableId(
            'ActivatorIDShadowingBrightness', true, [VARIABLETYPE_BOOLEAN, VARIABLETYPE_INTEGER, VARIABLETYPE_FLOAT],
            self::STATUS_INST_ACTIVATORIDSHADOWINGBRIGHTNESS_IS_INVALID
        )) {
            $this->SetStatus($ret);
            return;
        }

        if ($ret = $this->checkVariableId(
            'BrightnessIDShadowingBrightness', true, [VARIABLETYPE_INTEGER, VARIABLETYPE_FLOAT],
            self::STATUS_INST_BRIGHTNESSIDSHADOWINGBRIGHTNESS_IS_INVALID
        )) {
            $this->SetStatus($ret);
            return;
        }

        if ($ret = $this->checkVariableId(
            'ThresholdIDHighBrightness', true, [VARIABLETYPE_INTEGER, VARIABLETYPE_FLOAT], self::STATUS_INST_THRESHOLDIDHIGHBRIGHTNESS_IS_INVALID
        )) {
            $this->SetStatus($ret);
            return;
        }

        if ($ret = $this->checkVariableId(
            'ThresholdIDLessBrightness', true, [VARIABLETYPE_INTEGER, VARIABLETYPE_FLOAT], self::STATUS_INST_THRESHOLDIDLESSRIGHTNESS_IS_INVALID
        )) {
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

        if ($ret = $this->checkTimeTable()) {
            $this->SetStatus($ret);
            return;
        }
        $this->SetStatus(IS_ACTIVE);

    }

    private function checkVariableId(string $propName, bool $optional, array $variableTypes, int $errStatus): int
    {
        $variableID = $this->ReadPropertyInteger($propName);

        if (!$optional && $variableID === 0) {
            $this->Logger_Err(sprintf('ID nicht gesetzt: %s', $propName));
            return $errStatus;
        }

        if ($variableID !== 0) {

            if (!$variable = @IPS_GetVariable($variableID)) {
                $this->Logger_Err(sprintf('falsche Variablen ID #%s', $propName));
                return $errStatus;
            }

            if (!in_array($variable['VariableType'], $variableTypes, true)) {
                $this->Logger_Err(
                    sprintf('falscher Variablentyp - nur %s erlaubt', implode(', ', $variableTypes))
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
            $this->Logger_Err(sprintf('ID nicht gesetzt: %s', $propName));
            return $errStatus;
        }

        if ($eventID !== 0) {

            if (!$variable = @IPS_GetEvent($eventID)) {
                $this->Logger_Err(sprintf('falsche Event ID #%s', $propName));
                return $errStatus;
            }

            if ($variable['EventType'] !== $eventType) {
                $this->Logger_Err(sprintf('falscher Eventtyp - nur %s erlaubt', $eventType));
                return $errStatus;
            }
        }

        return 0;

    }

    private function checkRangeInteger(string $propName, int $min, int $max, int $errStatus): int
    {
        $value = $this->ReadPropertyInteger($propName);

        if ($value < $min || $value > $max) {
            $this->Logger_Err(sprintf('%s: Wert nicht im gültigen Bereich (%s - %s)', $propName, $min, $max));
            return $errStatus;
        }

        return 0;
    }

    private function isContactOpen(): ?bool
    {
        $contacts = [];
        if ($this->ReadPropertyInteger('Contact1ID') !== 0) {
            $contacts[] = $this->ReadPropertyInteger('Contact1ID');
        }
        if ($this->ReadPropertyInteger('Contact2ID') !== 0) {
            $contacts[] = $this->ReadPropertyInteger('Contact2ID');
        }

        // alle Kontakte prüfen ...
        $contactOpen = null;
        foreach ($contacts as $contact) {
            $contactOpen = $contactOpen || GetValue($contact) > 0;
        }

        return $contactOpen;
    }

    private function getLevelOfShadowingByBrightness(float $level): float
    {
        $activatorIDShadowingBrightness = $this->ReadPropertyInteger('ActivatorIDShadowingBrightness');
        if ($activatorIDShadowingBrightness !== 0) {
            $brightnessIDShadowingBrightness = $this->ReadPropertyInteger('BrightnessIDShadowingBrightness');
            if ($brightnessIDShadowingBrightness === 0) {
                trigger_error('BrightnessIDShadowingBrightness === 0', E_ERROR);
            }
            $thresholdIDHighBrightness = $this->ReadPropertyInteger('ThresholdIDHighBrightness');
            $thresholdIDLessBrightness = $this->ReadPropertyInteger('ThresholdIDLessBrightness');
            if ($thresholdIDHighBrightness === 0 && $thresholdIDLessBrightness === 0) {
                trigger_error('ThresholdIDHighBrightness === 0 and ThresholdIDLowBrightness === 0', E_ERROR);
            }


            if (GetValue($activatorIDShadowingBrightness)) {
                if (($thresholdIDHighBrightness > 0) && (GetValue($brightnessIDShadowingBrightness) > GetValue($thresholdIDHighBrightness))) {
                    return $this->ReadPropertyFloat('LevelHighBrightnessShadowingBrightness');
                }

                if (($thresholdIDLessBrightness > 0) && (GetValue($brightnessIDShadowingBrightness) > GetValue($thresholdIDLessBrightness))) {
                    return $this->ReadPropertyFloat('LevelLessBrightnessShadowingBrightness');
                }

            }
        }
        return $level;
    }

    private function isMovementLocked(float $levelAct, int $tsBlindLastMovement, int $tsAutomatik, string $gestern_ab, string $heute_auf,
                                      string $heute_ab, float $blindLevelClosed, float $blindLevelOpened): bool
    {
        //zuerst prüfen, ob der Rollladen nach der letzten aut. Bewegung (+60sec) manuell bewegt wurde
        if ($tsBlindLastMovement <= strtotime('+1 minute', $tsAutomatik)) {
            return false;
        }

        $deactivationTimeManu = $this->ReadPropertyInteger('DeactivationManualMovement') * 60;
        $objectName           = IPS_GetObject($this->InstanceID)['ObjectName'];

        //Zeitpunkt festhalten, sofern noch nicht geschehen
        if ($tsBlindLastMovement !== $this->ReadAttributeInteger('AttrTimeStampManual')) {
            $this->WriteAttributeInteger('AttrTimeStampManual', $tsBlindLastMovement);

            $this->Logger_Dbg(
                __FUNCTION__, 'Rollladenlevel wurde manuell gesetzt - Value: ' . $levelAct . ', tsBlindLastMovement: ' . $this->FormatTimeStamp(
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
                    'Der Rollladen wurde manuell auf ' . sprintf('%.0f', 100 * $levelAct) . '% gefahren.'
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
                    __FUNCTION__, sprintf(
                                    'Rollladen wurde manuell bewegt (Tag). DeactivationTimeManu: %s/%s', time() - $tsBlindLastMovement,
                                    $deactivationTimeManu
                                )
                );
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
    public function CloseBlind(int $percentClose, int $deactivationTimeAuto): bool
    {

        if (IPS_GetInstance($this->InstanceID)['InstanceStatus'] !== IS_ACTIVE) {
            return false;
        }

        $this->Logger_Dbg(__FUNCTION__, sprintf('Parameter percentClose: %s, deactivationTimeAuto: %s', $percentClose, $deactivationTimeAuto));

        $objectName = IPS_GetObject($this->InstanceID)['ObjectName'];

        $profile = $this->GetProfileInformation();
        $this->Logger_Dbg(__FUNCTION__, sprintf('Profile: %s', json_encode($profile)));

        if ($profile === false) {
            return false;
        }

        $levelNew = $profile['MinValue'] + ($percentClose / 100) * ($profile['MaxValue'] - $profile['MinValue']);

        if ($profile['Reversed']) {
            $levelNew = $profile['MaxValue'] - $levelNew;
        }

        $levelID             = $this->ReadPropertyInteger('BlindLevelID');
        $levelAct            = GetValue($levelID); //integer and float are supported
        $tsBlindLastMovement = IPS_GetVariable($levelID)['VariableChanged'];
        $LeveldiffPercentage = abs($levelNew - $levelAct) / ($profile['MaxValue'] - $profile['MinValue']);
        $timediff            = time() - $tsBlindLastMovement;

        $this->Logger_Dbg(__FUNCTION__, sprintf('levelAct: %s, levelNew: %s', $levelAct, $levelNew));

        $ret = true;

        // Wenn sich das aktuelle Level um mehr als 5% von neuem Level unterscheidet
        if (($LeveldiffPercentage > 0.05) && ($timediff >= $deactivationTimeAuto)) {

            // Level setzen
            //Wert übertragen
            if (@RequestAction($levelID, $levelNew)) {
                // Timestamp der Automatik merken (sonst wird die Bewegung später als manuelle Bewegung erkannt)
                $this->WriteAttributeInteger('AttrTimeStampAutomatic', time());
                $this->Logger_Dbg(
                    __FUNCTION__, "$objectName: TimestampAutomatik: " . $this->ReadAttributeInteger('AttrTimeStampAutomatic')
                );

                $this->WriteInfo($levelNew, $profile['LevelClosed'], $profile['LevelOpened']);
            } else {
                $this->Logger_Dbg(
                    __FUNCTION__, 'Fehler beim Setzen der Werte. (id = ' . $levelID . ', Value = ' . $percentClose . ')'
                );
                $ret = false;
            }
            $this->Logger_Dbg(__FUNCTION__, $objectName . ': ' . $levelAct . ' to ' . $levelNew);

            // kleine Pause, um Kommunikationsstörungen zu vermeiden
            sleep(5);

        } else {
            $this->Logger_Dbg(
                __FUNCTION__, "DeactivationTimeAuto: $timediff" . '/' . $deactivationTimeAuto . ', LeveldiffPercentage: ' . $LeveldiffPercentage
            );
        }

        return $ret;
    }

    private function WriteInfo($rLevelneu, $blindLevelClosed, $blindLevelOpened): void
    {
        $objectName = IPS_GetObject($this->InstanceID)['ObjectName'];

        if ($this->ReadPropertyInteger('BrightnessID') > 0) {
            $brightness = sprintf(' (%s)', GetValueFormatted($this->ReadPropertyInteger('BrightnessID')));
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

    private function checkTimeTable(): int
    {
        $eventScheduleGroups = IPS_GetEvent($this->ReadPropertyInteger('WeeklyTimeTableEventID'))['ScheduleGroups'];

        foreach ($eventScheduleGroups as $scheduleGroup) {
            $countID1 = $this->CountNumberOfPointsWithActionId($scheduleGroup['Points'], 1);
            $countID2 = $this->CountNumberOfPointsWithActionId($scheduleGroup['Points'], 2);
            if ($countID1 > 1) {
                $this->Logger_Dbg(
                    __FUNCTION__, sprintf(
                                    'Invalid TimeTable: More (%s) than one Point with ActionID 1. (ScheduleGroup: %s)', $countID1,
                                    json_encode($scheduleGroup)
                                )
                );
                return self::STATUS_INST_TIMETABLE_IS_INVALID;
            }
            if (($countID1 + $countID2) === 0) {
                $this->Logger_Dbg(
                    __FUNCTION__, sprintf(
                                    'Invalid TimeTable: No Points with ActionID 1 or 2 found. (ScheduleGroup: %s)', json_encode($scheduleGroup)
                                )
                );
                return self::STATUS_INST_TIMETABLE_IS_INVALID;
            }
        }

        return 0;

    }

    Private function CountNumberOfPointsWithActionId(array $points, int $actionID): int
    {
        $count = 0;
        foreach ($points as $point) {
            if ($point['ActionID'] === $actionID) {
                $count++;
            }
        }
        return $count;
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
        $weeklyTimeTableEventId = $this->ReadPropertyInteger('WeeklyTimeTableEventID');
        if (!$event = @IPS_GetEvent($weeklyTimeTableEventId)) {
            trigger_error(sprintf('falsche Event ID #%s', $weeklyTimeTableEventId));
            return false;
        }

        if ($event['EventType'] !== EVENTTYPE_SCHEDULE) {
            trigger_error('falscher Eventtype');
            return false;
        }

        $idWakeUpTime = $this->ReadPropertyInteger('WakeUpTimeID');
        if ($idWakeUpTime > 0) {
            $auf = date('H:i', strtotime(GetValueString($idWakeUpTime)) + $this->ReadPropertyInteger('WakeUpTimeOffset') * 60);
        } else {
            $auf = $this->getUpTimeOfDay($weekDay, $event['ScheduleGroups']);
        }

        $idBedTime = $this->ReadPropertyInteger('BedTimeID');
        if ($idBedTime > 0) {
            $ab = date('H:i', strtotime(GetValueString($idBedTime)) + $this->ReadPropertyInteger('BedTimeOffset') * 60);
        } else {
            $ab = $this->getDownTimeOfDay($weekDay, $event['ScheduleGroups']);
        }

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
            'LevelOpened' => $reversed ? (float) $profile['MaxValue'] : (float) $profile['MinValue'],
            'LevelClosed' => $reversed ? (float) $profile['MinValue'] : (float) $profile['MaxValue']];


    }

    //-----------------------------------------------
    private function GetBlindLastTimeStampAndCheckAutomatic(int $id_Level): int
    {
        $tsBlindLevelChanged = IPS_GetVariable($id_Level)['VariableChanged'];

        //prüfen, ob Automatik nach der letzten Rollladenbewegung eingestellt wurde.
        $tsAutomaticVariable  = IPS_GetVariable(IPS_GetObjectIDByIdent('ACTIVATED', $this->InstanceID))['VariableChanged'];
        $tsAutomaticAttribute = $this->ReadAttributeInteger('AttrTimeStampAutomatic');
        if (($tsAutomaticVariable > $tsBlindLevelChanged) && ($tsAutomaticAttribute !== $tsBlindLevelChanged) && $this->GetValue('ACTIVATED')) {
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


    private function Logger_Err(string $message): void
    {
        $this->SendDebug('LOG_ERR', $message, 0);
        if (function_exists('IPSLogger_Err') && $this->ReadPropertyBoolean('WriteLogInformationToIPSLogger')) {
            IPSLogger_Err(__CLASS__, $message);
        } else {
            $this->LogMessage($message, KL_ERROR);
        }

        $this->SetValue('LAST_MESSAGE', $message);
    }

    private function Logger_Inf(string $message): void
    {
        $this->SendDebug('LOG_INFO', $message, 0);
        if (function_exists('IPSLogger_Inf') && $this->ReadPropertyBoolean('WriteLogInformationToIPSLogger')) {
            IPSLogger_Inf(__CLASS__, $message);
        } else {
            $this->LogMessage($message, KL_MESSAGE);
        }

        $this->SetValue('LAST_MESSAGE', $message);
    }

    private function Logger_Dbg(string $message, string $data): void
    {
        $this->SendDebug($message, $data, 0);
        if (function_exists('IPSLogger_Dbg') && $this->ReadPropertyBoolean('WriteDebugInformationToIPSLogger')) {
            IPSLogger_Dbg(__CLASS__ . '.' . IPS_GetObject($this->InstanceID)['ObjectName'], $data);
        }
    }


}
