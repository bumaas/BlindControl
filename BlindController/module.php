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
    private const STATUS_INST_EMERGENCY_CONTACT_ID_IS_INVALID = 212;
    private const STATUS_INST_WAKEUPTIME_ID_IS_INVALID = 213;
    private const STATUS_INST_SLEEPTIME_ID_IS_INVALID = 214;
    private const STATUS_INST_DAYSTART_ID_IS_INVALID = 215;
    private const STATUS_INST_DAYEND_ID_IS_INVALID = 216;
    private const STATUS_INST_ACTIVATORIDSHADOWINGBYSUNPOSITION_IS_INVALID = 220;
    private const STATUS_INST_AZIMUTHID_IS_INVALID = 221;
    private const STATUS_INST_ALTITUDEID_IS_INVALID = 222;
    private const STATUS_INST_BRIGTHNESSIDSHADOWINGBYSUNPOSITION_IS_INVALID = 223;
    private const STATUS_INST_BRIGHTNESSTHRESHOLDIDSHADOWINGBYSUNPOSITION_IS_INVALID = 224;
    private const STATUS_INST_ROOMTEMPERATUREID_IS_INVALID = 225;
    private const STATUS_INST_ACTIVATORIDSHADOWINGBRIGHTNESS_IS_INVALID = 230;
    private const STATUS_INST_BRIGHTNESSIDSHADOWINGBRIGHTNESS_IS_INVALID = 231;
    private const STATUS_INST_THRESHOLDIDHIGHBRIGHTNESS_IS_INVALID = 232;
    private const STATUS_INST_THRESHOLDIDLESSRIGHTNESS_IS_INVALID = 233;

    private $objectName;

    private $profile;


    // Überschreibt die interne IPS_Create($id) Funktion
    public function Create()
    {
        // Diese Zeile nicht löschen.
        parent::Create();

        $this->RegisterProperties();
        //$this->Write
        $this->RegisterAttributes();

        $this->RegisterTimer('Update', 0, 'BLC_ControlBlind(' . $this->InstanceID . ', true);');
    }

    public function ApplyChanges()
    {
        //Never delete this line!
        parent::ApplyChanges();

        $this->RegisterReferences();
        $this->RegisterMessages();
        $this->RegisterVariables();

        $this->SetInstanceStatusAndTimerEvent();
    }

    public function RequestAction($Ident, $Value): bool
    {
        if (strtoupper($Ident) !== 'ACTIVATED') {
            trigger_error('Unknown Ident: ' . $Ident);
            return false;
        }
        if (is_bool($Value)) {
            $this->Logger_Dbg(__FUNCTION__, sprintf('Ident: %s, Value: %s', $Ident, (int) $Value));
        } else {
            $this->Logger_Dbg(__FUNCTION__, sprintf('Ident: %s, Value: %s', $Ident, $Value));
        }
        if ($this->SetValue($Ident, $Value)) {
            $this->SetInstanceStatusAndTimerEvent();
            return true;
        }

        return false;
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

        $this->SetInstanceStatusAndTimerEvent();

        if ($this->GetValue('ACTIVATED')) {
            // controlBlind mit Prüfung, ob der Rollladen sofort bewegt werden soll
            $this->ControlBlind(
                !in_array(
                    $SenderID, [
                    $this->ReadPropertyInteger('ContactOpen1ID'),
                    $this->ReadPropertyInteger('ContactOpen2ID'),
                    $this->ReadPropertyInteger('EmergencyContactID'),
                    $this->ReadPropertyInteger('ActivatorIDShadowingBrightness'),
                    $this->ReadPropertyInteger('ActivatorIDShadowingBySunPosition')], true
                )
            );
        }
    }

    /**
     * Die folgenden Funktionen stehen automatisch zur Verfügung, wenn das Modul über die "Module Control" eingefügt wurden.
     * Die Funktionen werden, mit dem selbst eingerichteten Prefix, in PHP und JSON-RPC zur Verfügung gestellt:
     *
     * @param bool $considerDeactivationTimeAuto
     *
     * @return bool
     */
    public function ControlBlind(bool $considerDeactivationTimeAuto): bool
    {

        if (IPS_GetInstance($this->InstanceID)['InstanceStatus'] !== IS_ACTIVE) {
            return false;
        }

        if (!IPS_SemaphoreEnter($this->InstanceID . '- Blind', 9 * 1000)) {
            return false;
        }

        // globale Instanzvariablen setzen
        $this->objectName = IPS_GetObject($this->InstanceID)['ObjectName'];
        $this->profile    = $this->GetProfileInformation();

        // $deactivationTimeAuto: Zeitraum, in dem das automatisch gesetzte Level
        // erhalten bleibt bevor es überschrieben wird.

        if ($considerDeactivationTimeAuto) {
            $deactivationTimeAuto = $this->ReadPropertyInteger('DeactivationAutomaticMovement') * 60;
        } else {
            $deactivationTimeAuto = 0;
        }

        $Hinweis = '';


        // 'tagsüber' nach Wochenplan ermitteln
        $isDayByTimeSchedule = $this->getIsDayByTimeSchedule();

        // optionale Tageserkennung auswerten
        $brightness          = null;
        $isDayByDayDetection = $this->getIsDayByDayDetection($brightness);

        if ($isDayByDayDetection === null) {
            $isDay = $isDayByTimeSchedule;
        } else {
            $isDay = $isDayByTimeSchedule && $isDayByDayDetection;
        }

        // übersteuernde Tageszeiten auswerten
        $dayStart = null;
        $dayEnd   = null;

        if ($this->ReadPropertyInteger('DayStartID') > 0) {
            $dayStart = GetValueString($this->ReadPropertyInteger('DayStartID'));
        }

        if ($this->ReadPropertyInteger('DayEndID') > 0) {
            $dayEnd = GetValueString($this->ReadPropertyInteger('DayEndID'));
        }

        if (isset($dayStart, $dayEnd)) {
            $isDay = (time() > strtotime($dayStart)) && (time() < strtotime($dayEnd));
        } elseif (isset($dayStart) && (time() < strtotime('12:00'))) {
            $isDay = time() > strtotime($dayStart);
        } elseif (isset($dayEnd) && (time() > strtotime('12:00'))) {
            $isDay = time() < strtotime($dayEnd);
        }


        //Level ID ermitteln
        $idLevel = $this->ReadPropertyInteger('BlindLevelID');

        //Zeitpunkt der letzten Rollladenbewegung
        $tsBlindLastMovement = $this->GetBlindLastTimeStampAndCheckAutomatic($idLevel);

        // Attribut TimestampAutomatik auslesen
        $tsAutomatik = $this->ReadAttributeInteger('AttrTimeStampAutomatic');

        if ($this->checkIsDayChange($isDay)) {
            $deactivationTimeAuto = 0;
            $bNoMove              = false;
        } else {
            // prüfen, ob der Rollladen manuell bewegt wurde und somit eine Bewegungssperre besteht
            $bNoMove = $this->isMovementLocked(
                $idLevel, $tsBlindLastMovement, $isDay, $this->ReadAttributeInteger('AttrTimeStampIsDayChange'), $tsAutomatik,
                $this->profile['LevelClosed'], $this->profile['LevelOpened']
            );
        }

        // Das aktuelle Level im Jalousieaktor auslesen
        $levelAct = (float) GetValue($idLevel);

        if ($bNoMove) {
            $levelNew = $levelAct;
        } else if ($isDay) {
            $levelNew = $this->profile['LevelOpened'];
            $Hinweis  = 'Tag';
            if (isset($isDayByDayDetection, $brightness)) {
                $Hinweis .= ', ' . GetValueFormatted($this->ReadPropertyInteger('BrightnessID'));
            }
        } else {
            $levelNew = $this->profile['LevelClosed'];
            $Hinweis  = 'Nacht';
            if (isset($isDayByDayDetection, $brightness)) {
                $Hinweis .= ', ' . GetValueFormatted($this->ReadPropertyInteger('BrightnessID'));
            }
        }


        $this->Logger_Dbg(
            __FUNCTION__, sprintf(
                            'tsAutomatik: %s, tsBlind: %s, levelAct: %s, bNoMove: %s, isDay: %s (isDayByTimeSchedule: %s, isDayByDayDetection: %s, dayStart: %s, dayEnd: %s), considerDeactivationTimeAuto: %s',
                            $this->FormatTimeStamp($tsAutomatik), $this->FormatTimeStamp($tsBlindLastMovement), $levelAct, (int) $bNoMove,
                            (int) $isDay, (int) $isDayByTimeSchedule, (isset($isDayByDayDetection) ? (int) $isDayByDayDetection : 'null'),
                            $dayStart ?? 'null', $dayEnd ?? 'null', (int) $considerDeactivationTimeAuto
                        )
        );

        // am Tag wird überprüft, ob das Fenster beschattet werden soll
        if ($isDay) {

            // prüfen, ob Beschattung nach Sonnenstand gewünscht und notwendig
            $levelShadowingBySunPosition = $this->getLevelOfShadowingBySunPosition($levelAct);
            if ($levelShadowingBySunPosition !== null) {

                if ($this->profile['Reversed']) {
                    $levelNew = min($levelNew, $levelShadowingBySunPosition);
                } else {
                    $levelNew = max($levelNew, $levelShadowingBySunPosition);
                }
                $Hinweis = 'Beschattung nach Sonnenstand, ' . GetValueFormatted($this->ReadPropertyInteger('BrightnessIDShadowingBySunPosition'));
            }

            // prüfen, ob Beschattung bei Helligkeit gewünscht und notwendig
            $levelShadowingBrightness = $this->getLevelOfShadowingByBrightness();
            if ($levelShadowingBrightness !== null) {

                if ($this->profile['Reversed']) {
                    if ($levelShadowingBrightness < $levelNew) {
                        $levelNew = $levelShadowingBrightness;
                        $Hinweis  =
                            'Beschattung nach Helligkeit, ' . GetValueFormatted($this->ReadPropertyInteger('BrightnessIDShadowingBrightness'));
                    }
                } elseif ($levelShadowingBrightness > $levelNew) {
                    $levelNew = $levelShadowingBrightness;
                    $Hinweis  = 'Beschattung nach Helligkeit, ' . GetValueFormatted($this->ReadPropertyInteger('BrightnessIDShadowingBrightness'));
                }
            }

        } else {
            // nachts gilt keine deactivation Time
            $deactivationTimeAuto = 0;
        }

        // prüfen, ob ein Kontakt offen ist
        $levelContactOpenBlind  = $this->getLevelOpenBlindContact();
        $levelContactCloseBlind = $this->getLevelCloseBlindContact();
        $levelContactEmergency  = $this->getLevelEmergencyContact();

        if ($levelContactEmergency !== null) {
            // wenn  der Emergency Kontakt geöffnet ist dann
            // wird die Bewegungssperre aufgehoben und der Rollladen geöffnet
            $deactivationTimeAuto = 0;
            $bNoMove              = false;
            $levelNew             = $levelContactEmergency;
            $Hinweis              = 'Notfallkontakt offen';

            //im Notfall wird die Automatik deaktiviert
            $bEmergency = true;

            $this->WriteAttributeBoolean('AttrContactOpen', true);
            $this->Logger_Dbg(__FUNCTION__, "NOTFALL: Kontakt geöffnet (levelAct: $levelAct, levelNew: $levelNew)");


        } elseif ($levelContactOpenBlind !== null) {
            // wenn  ein Kontakt geöffnet ist und der Rollladen unter dem ContactOpen Level steht, dann
            // wird die Bewegungssperre aufgehoben und das Level auf das Mindestlevel bei geöffnetem Kontakt gesetzt
            $deactivationTimeAuto = 0;
            $bNoMove              = false;
            if ($this->profile['Reversed']) {
                if ($levelContactOpenBlind > $levelNew) {
                    $levelNew = $levelContactOpenBlind;
                    $Hinweis  = 'Kontakt offen';
                }
            } elseif ($levelContactOpenBlind < $levelNew) {
                $levelNew = $levelContactOpenBlind;
                $Hinweis  = 'Kontakt offen';
            }

            $this->WriteAttributeBoolean('AttrContactOpen', true);
            $this->Logger_Dbg(__FUNCTION__, "Kontakt geöffnet (levelAct: $levelAct, levelNew: $levelNew)");

        } elseif ($levelContactCloseBlind !== null) {
            // wenn  ein Kontakt geöffnet ist und der Rollladen oberhalb dem ContactClose Level steht, dann
            // wird die Bewegungssperre aufgehoben und das Level auf das Mindestlevel bei geöffnetem Kontakt gesetzt
            $deactivationTimeAuto = 0;
            $bNoMove              = false;
            if ($this->profile['Reversed']) {
                if ($levelContactCloseBlind < $levelNew) {
                    $levelNew = $levelContactCloseBlind;
                    $Hinweis  = 'Kontakt offen';
                }
            } elseif ($levelContactCloseBlind > $levelNew) {
                $levelNew = $levelContactCloseBlind;
                $Hinweis  = 'Kontakt offen';
            }

            $this->WriteAttributeBoolean('AttrContactOpen', true);
            $this->Logger_Dbg(__FUNCTION__, "Kontakt geöffnet (levelAct: $levelAct, levelNew: $levelNew)");

        } elseif ($this->ReadAttributeBoolean('AttrContactOpen')) {
            // wenn die Rollladenposition noch auf Kontakt offen Position steht
            $deactivationTimeAuto = 0;
            $this->WriteAttributeBoolean('AttrContactOpen', false);
        }

        if (!$bNoMove) {
            $level = $levelNew / ($this->profile['MaxValue'] - $this->profile['MinValue']);
            if ($this->profile['Reversed']) {
                $level = 1 - $level;
            }
            $this->MoveBlind((int) ($level * 100), $deactivationTimeAuto, $Hinweis);
        }

        //im Notfall wird die Automatik deaktiviert
        if (isset($bEmergency)) {
            $this->SetValue('ACTIVATED', false);
            $this->SetInstanceStatusAndTimerEvent();
        }


        IPS_SemaphoreLeave($this->InstanceID . '- Blind');

        return true;

    }


    private function RegisterProperties(): void
    {
        $this->RegisterPropertyInteger('BlindLevelID', 0);
        $this->RegisterPropertyInteger('WeeklyTimeTableEventID', 0);
        $this->RegisterPropertyInteger('HolidayIndicatorID', 0);
        $this->RegisterPropertyInteger('DayUsedWhenHoliday', 0);
        $this->RegisterPropertyInteger('WakeUpTimeID', 0);
        $this->RegisterPropertyInteger('WakeUpTimeOffset', 0);
        $this->RegisterPropertyInteger('BedTimeID', 0);
        $this->RegisterPropertyInteger('BedTimeOffset', 0);

        //day detection
        $this->RegisterPropertyInteger('IsDayIndicatorID', 0);
        $this->RegisterPropertyInteger('BrightnessID', 0);
        $this->RegisterPropertyInteger('BrightnessThresholdID', 0);

        //overruling day times
        $this->RegisterPropertyInteger('DayStartID', 0);
        $this->RegisterPropertyInteger('DayEndID', 0);

        //contacts open
        $this->RegisterPropertyInteger('ContactOpen1ID', 0);
        $this->RegisterPropertyInteger('ContactOpen2ID', 0);
        $this->RegisterPropertyFloat('ContactOpenLevel1', 0);
        $this->RegisterPropertyFloat('ContactOpenLevel2', 0);
        $this->RegisterPropertyInteger('EmergencyContactID', 0);

        //contacts close
        $this->RegisterPropertyInteger('ContactClose1ID', 0);
        $this->RegisterPropertyInteger('ContactClose2ID', 0);
        $this->RegisterPropertyFloat('ContactCloseLevel1', 0);
        $this->RegisterPropertyFloat('ContactCloseLevel2', 0);

        //shadowing according to sun position
        $this->RegisterPropertyInteger('ActivatorIDShadowingBySunPosition', 0);
        $this->RegisterPropertyInteger('AzimuthID', 0);
        $this->RegisterPropertyInteger('AltitudeID', 0);
        $this->RegisterPropertyFloat('AzimuthFrom', 0);
        $this->RegisterPropertyFloat('AzimuthTo', 0);
        $this->RegisterPropertyInteger('BrightnessIDShadowingBySunPosition', 0);
        $this->RegisterPropertyInteger('BrightnessThresholdIDShadowingBySunPosition', 0);
        $this->RegisterPropertyInteger('TemperatureIDShadowingBySunPosition', 0);
        $this->RegisterPropertyFloat('LowSunPositionAltitude', 0);
        $this->RegisterPropertyFloat('HighSunPositionAltitude', 0);
        $this->RegisterPropertyFloat('LowSunPositionBlindLevel', 0);
        $this->RegisterPropertyFloat('HighSunPositionBlindLevel', 0);

        //shadowing according to brightness
        $this->RegisterPropertyInteger('ActivatorIDShadowingBrightness', 0);
        $this->RegisterPropertyInteger('BrightnessIDShadowingBrightness', 0);
        $this->RegisterPropertyInteger('ThresholdIDLessBrightness', 0);
        $this->RegisterPropertyFloat('LevelLessBrightnessShadowingBrightness', 0);
        $this->RegisterPropertyInteger('ThresholdIDHighBrightness', 0);
        $this->RegisterPropertyFloat('LevelHighBrightnessShadowingBrightness', 0);

        $this->RegisterPropertyInteger('UpdateInterval', 1);
        $this->RegisterPropertyInteger('DeactivationAutomaticMovement', 20);
        $this->RegisterPropertyInteger('DeactivationManualMovement', 120);
        $this->RegisterPropertyBoolean('WriteLogInformationToIPSLogger', false);
        $this->RegisterPropertyBoolean('WriteDebugInformationToLogfile', false);
        $this->RegisterPropertyBoolean('WriteDebugInformationToIPSLogger', false);
    }

    private function RegisterReferences(): void
    {
        $objectIDs = [
            $this->ReadPropertyInteger('BlindLevelID'),
            $this->ReadPropertyInteger('WeeklyTimeTableEventID'),
            $this->ReadPropertyInteger('HolidayIndicatorID'),
            $this->ReadPropertyInteger('HolidayIndicatorID'),
            $this->ReadPropertyInteger('WakeUpTimeID'),
            $this->ReadPropertyInteger('BedTimeID'),

            $this->ReadPropertyInteger('IsDayIndicatorID'),
            $this->ReadPropertyInteger('BrightnessID'),
            $this->ReadPropertyInteger('BrightnessThresholdID'),

            $this->ReadPropertyInteger('DayStartID'),
            $this->ReadPropertyInteger('DayStartID'),

            $this->ReadPropertyInteger('ContactOpen1ID'),
            $this->ReadPropertyInteger('ContactOpen2ID'),
            $this->ReadPropertyInteger('EmergencyContactID'),

            $this->ReadPropertyInteger('ContactClose1ID'),
            $this->ReadPropertyInteger('ContactClose2ID'),

            $this->ReadPropertyInteger('ActivatorIDShadowingBySunPosition'),
            $this->ReadPropertyInteger('AzimuthID'),
            $this->ReadPropertyInteger('AltitudeID'),
            $this->ReadPropertyInteger('BrightnessIDShadowingBySunPosition'),
            $this->ReadPropertyInteger('BrightnessThresholdIDShadowingBySunPosition'),
            $this->ReadPropertyInteger('TemperatureIDShadowingBySunPosition'),

            $this->ReadPropertyInteger('ActivatorIDShadowingBrightness'),
            $this->ReadPropertyInteger('BrightnessIDShadowingBrightness'),
            $this->ReadPropertyInteger('ThresholdIDLessBrightness'),
            $this->ReadPropertyInteger('ThresholdIDHighBrightness')];

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
            $this->ReadPropertyInteger('ContactOpen1ID'),
            $this->ReadPropertyInteger('ContactOpen2ID'),
            $this->ReadPropertyInteger('EmergencyContactID'),
            $this->ReadPropertyInteger('ActivatorIDShadowingBySunPosition'),
            $this->ReadPropertyInteger('AzimuthID'),
            $this->ReadPropertyInteger('AltitudeID'),
            $this->ReadPropertyInteger('BrightnessIDShadowingBySunPosition'),
            $this->ReadPropertyInteger('BrightnessThresholdIDShadowingBySunPosition'),
            $this->ReadPropertyInteger('TemperatureIDShadowingBySunPosition'),
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
        $this->RegisterAttributeInteger('AttrTimeStampIsDayChange', 0);
        $this->RegisterAttributeBoolean('AttrIsDay', false);
        $this->RegisterAttributeBoolean('AttrContactOpen', false);
        $this->RegisterAttributeString('lastBlindMove', '');
    }

    private function RegisterVariables(): void
    {
        $this->RegisterVariableBoolean('ACTIVATED', 'Activated', '~Switch');
        $this->RegisterVariableString('LAST_MESSAGE', 'Last Message');

        $this->EnableAction('ACTIVATED');
    }

    private function SetInstanceStatusAndTimerEvent(): void
    {

        if ($ret =
            $this->checkVariableId('BlindLevelID', false, [VARIABLETYPE_INTEGER, VARIABLETYPE_FLOAT], self::STATUS_INST_BLIND_LEVEL_ID_IS_INVALID)) {
            $this->SetStatus($ret);
            return;
        }

        if (!$this->checkBlindLevelId()) {
            $this->SetStatus(self::STATUS_INST_BLIND_LEVEL_ID_IS_INVALID);
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
            'DayStartID', true, [VARIABLETYPE_STRING], self::STATUS_INST_DAYSTART_ID_IS_INVALID
        )) {
            $this->SetStatus($ret);
            return;
        }

        if ($ret = $this->checkVariableId(
            'DayEndID', true, [VARIABLETYPE_STRING], self::STATUS_INST_DAYEND_ID_IS_INVALID
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
            'ContactOpen1ID', true, [VARIABLETYPE_BOOLEAN, VARIABLETYPE_INTEGER, VARIABLETYPE_FLOAT], self::STATUS_INST_CONTACT1_ID_IS_INVALID
        )) {
            $this->SetStatus($ret);
            return;
        }

        if ($ret = $this->checkVariableId(
            'ContactOpen2ID', true, [VARIABLETYPE_BOOLEAN, VARIABLETYPE_INTEGER, VARIABLETYPE_FLOAT], self::STATUS_INST_CONTACT2_ID_IS_INVALID
        )) {
            $this->SetStatus($ret);
            return;
        }

        if ($ret = $this->checkVariableId(
            'EmergencyContactID', true, [VARIABLETYPE_BOOLEAN, VARIABLETYPE_INTEGER, VARIABLETYPE_FLOAT],
            self::STATUS_INST_EMERGENCY_CONTACT_ID_IS_INVALID
        )) {
            $this->SetStatus($ret);
            return;
        }

        if ($ret = $this->checkVariableId(
            'ActivatorIDShadowingBySunPosition', true, [VARIABLETYPE_BOOLEAN, VARIABLETYPE_INTEGER, VARIABLETYPE_FLOAT],
            self::STATUS_INST_ACTIVATORIDSHADOWINGBYSUNPOSITION_IS_INVALID
        )) {
            $this->SetStatus($ret);
            return;
        }

        if ($ret = $this->checkVariableId(
            'AzimuthID', $this->ReadPropertyInteger('ActivatorIDShadowingBySunPosition') === 0, [VARIABLETYPE_FLOAT],
            self::STATUS_INST_AZIMUTHID_IS_INVALID
        )) {
            $this->SetStatus($ret);
            return;
        }

        if ($ret = $this->checkVariableId(
            'AltitudeID', $this->ReadPropertyInteger('ActivatorIDShadowingBySunPosition') === 0, [VARIABLETYPE_FLOAT],
            self::STATUS_INST_ALTITUDEID_IS_INVALID
        )) {
            $this->SetStatus($ret);
            return;
        }

        if ($ret = $this->checkVariableId(
            'BrightnessIDShadowingBySunPosition', $this->ReadPropertyInteger('ActivatorIDShadowingBySunPosition') === 0,
            [VARIABLETYPE_INTEGER, VARIABLETYPE_FLOAT], self::STATUS_INST_BRIGTHNESSIDSHADOWINGBYSUNPOSITION_IS_INVALID
        )) {
            $this->SetStatus($ret);
            return;
        }

        if ($ret = $this->checkVariableId(
            'BrightnessThresholdIDShadowingBySunPosition', $this->ReadPropertyInteger('ActivatorIDShadowingBySunPosition') === 0,
            [VARIABLETYPE_INTEGER, VARIABLETYPE_FLOAT], self::STATUS_INST_BRIGHTNESSTHRESHOLDIDSHADOWINGBYSUNPOSITION_IS_INVALID
        )) {
            $this->SetStatus($ret);
            return;
        }

        if ($ret = $this->checkVariableId(
            'TemperatureIDShadowingBySunPosition', true, [VARIABLETYPE_INTEGER, VARIABLETYPE_FLOAT], self::STATUS_INST_ROOMTEMPERATUREID_IS_INVALID
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

        if ($this->GetValue('ACTIVATED')) {
            $this->SetTimerInterval('Update', $this->ReadPropertyInteger('UpdateInterval') * 60 * 1000);
        } else {
            $this->SetTimerInterval('Update', 0);
            $this->SetStatus(IS_INACTIVE);
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
                $this->Logger_Err(sprintf('falsche Variablen ID (#%s) für "%s"', $variableID, $propName));
                return $errStatus;
            }

            if (!in_array($variable['VariableType'], $variableTypes, true)) {
                $this->Logger_Err(
                    sprintf('falscher Variablentyp für "%s" - nur %s erlaubt', $propName, implode(', ', $variableTypes))
                );
                return $errStatus;
            }
        }

        return 0;

    }

    private function checkBlindLevelId(): bool
    {
        $var = IPS_GetVariable($this->ReadPropertyInteger('BlindLevelID'));

        return !(!$var['VariableAction'] && !$var['VariableCustomAction']) || (!$var['VariableCustomProfile'] && !$var['VariableProfile']);

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

    private function checkIsDayChange(bool $isDay): bool
    {
        if ($this->ReadAttributeBoolean('AttrIsDay') !== $isDay) {
            $this->WriteAttributeBoolean('AttrIsDay', $isDay);
            $this->WriteAttributeInteger('AttrTimeStampIsDayChange', time());
            $this->Logger_Dbg(__FUNCTION__, 'DayChange!');
            return true;
        }

        return false;
    }

    private function getLevelOpenBlindContact(): ?float
    {
        $contacts = [];

        if ($this->ReadPropertyInteger('ContactOpen1ID') !== 0) {
            $contacts[] = [
                'id'    => $this->ReadPropertyInteger('ContactOpen1ID'),
                'level' => $this->ReadPropertyFloat('ContactOpenLevel1')];
        }
        if ($this->ReadPropertyInteger('ContactOpen2ID') !== 0) {
            $contacts[] = [
                'id'    => $this->ReadPropertyInteger('ContactOpen2ID'),
                'level' => $this->ReadPropertyFloat('ContactOpenLevel2')];
        }

        // alle Kontakte prüfen ...
        $contactOpen = null;
        $level       = null;
        foreach ($contacts as $contact) {
            if (GetValue($contact['id'])) {
                $contactOpen = true;
                if (isset($level)) {
                    if ($this->profile['Reversed']) {
                        $level = max($level, $contact['level']);
                    } else {
                        $level = min($level, $contact['level']);
                    }
                } else {
                    $level = $contact['level'];
                }
                $this->Logger_Dbg(
                    __FUNCTION__, sprintf(
                                    'contact is open: #%s, value: %s, level: %s', $contact['id'], GetValueFormatted($contact['id']), $contact['level']
                                )
                );
            }
        }

        if ($contactOpen) {
            return $level;
        }

        return null;
    }

    private function getLevelCloseBlindContact(): ?float
    {
        $contacts = [];

        if ($this->ReadPropertyInteger('ContactClose1ID') !== 0) {
            $contacts[] = [
                'id'    => $this->ReadPropertyInteger('ContactClose1ID'),
                'level' => $this->ReadPropertyFloat('ContactCloseLevel1')];
        }
        if ($this->ReadPropertyInteger('ContactClose2ID') !== 0) {
            $contacts[] = [
                'id'    => $this->ReadPropertyInteger('ContactClose2ID'),
                'level' => $this->ReadPropertyFloat('ContactCloseLevel2')];
        }

        // alle Kontakte prüfen ...
        $contactOpen = null;
        $level       = null;
        foreach ($contacts as $contact) {
            if (GetValue($contact['id'])) {
                $contactOpen = true;
                if (isset($level)) {
                    if ($this->profile['Reversed']) {
                        $level = min($level, $contact['level']);
                    } else {
                        $level = max($level, $contact['level']);
                    }
                } else {
                    $level = $contact['level'];
                }

                $this->Logger_Dbg(
                    __FUNCTION__, sprintf('contact is open: #%s, value: %s, level: %s', $contact['id'], $this->GetFormattedValue($contact['id']), $contact['level'])
                );
            }
        }

        if ($contactOpen) {
            return $level;
        }

        return null;
    }

    private function getLevelEmergencyContact(): ?float
    {
        $contacts = [];

        if ($this->ReadPropertyInteger('EmergencyContactID') !== 0) {
            $contacts[] = [
                'id'    => $this->ReadPropertyInteger('EmergencyContactID'),
                'level' => $this->profile['LevelOpened']];
        }

        // alle Kontakte prüfen ...
        $contactOpen = null;
        $level       = null;
        foreach ($contacts as $contact) {
            if (GetValue($contact['id'])) {
                $contactOpen = true;
                if (isset($level)) {
                    if ($this->profile['Reversed']) {
                        $level = max($level, $contact['level']);
                    } else {
                        $level = min($level, $contact['level']);
                    }
                } else {
                    $level = $contact['level'];
                }

                $this->Logger_Dbg(
                    __FUNCTION__, sprintf(
                                    'emergency contact is open: #%s, value: %s, level: %s', $contact['id'], $this->GetFormattedValue($contact['id']),
                                    $contact['level']
                                )
                );
            }
        }

        if ($contactOpen) {
            return $level;
        }

        return null;
    }


    private function getLevelOfShadowingBySunPosition(float $levelAct): ?float
    {

        $activatorID = $this->ReadPropertyInteger('ActivatorIDShadowingBySunPosition');

        if (($activatorID === 0) || !GetValue($activatorID)) {
            // keine Beschattung nach Sonnenstand gewünscht bzw. nicht notwendig
            return null;
        }

        $temperatureID = $this->ReadPropertyInteger('TemperatureIDShadowingBySunPosition');
        if ($temperatureID === 0) {
            $temperature = null;
        } else {
            $temperature = (float) GetValue($temperatureID);
        }


        $level               = null;
        $brightness          = GetValue($this->ReadPropertyInteger('BrightnessIDShadowingBySunPosition'));
        $thresholdBrightness =
            $this->getBrightnessThreshold($this->ReadPropertyInteger('BrightnessThresholdIDShadowingBySunPosition'), $levelAct, $temperature);

        $this->Logger_Dbg(
            __FUNCTION__, sprintf(
                            'active: %d, brightness: %.1f/%.1f, levelAct: %.2f, temperature: %s', (int) GetValue($activatorID), $brightness,
                            $thresholdBrightness, $levelAct, $temperature ?? 'null'
                        )
        );

        if ($brightness >= $thresholdBrightness) {

            $level = $this->getLevelFromSunPosition(
                GetValueFloat($this->ReadPropertyInteger('AzimuthID')), GetValueFloat($this->ReadPropertyInteger('AltitudeID'))
            );
            if ($level === null) {
                return null;
            }
            $this->Logger_Dbg(__FUNCTION__, sprintf('level: %.2f', $level));


            //wenn Wärmeschutz notwenig oder bereits eingeschaltet und Hysterese nicht unterschritten
            $levelCorrectionHeat = round(0.15 * ($this->profile['LevelOpened'] - $this->profile['LevelClosed']), 2);

            if (($temperature > 27.0) || ((round($levelAct, 2) === round($level, 2) + $levelCorrectionHeat) && ($temperature > (27.0 - 0.5)))) {
                $level += $levelCorrectionHeat;
                $this->Logger_Dbg(__FUNCTION__, sprintf('Temp gt 27°, levelAct: %.2f, level: %.2f', $levelAct, $level));
            }

            //wenn Hitzeschutz notwenig oder bereits eingeschaltet und Hysterese nicht unterschritten
            if ($this->profile['Reversed']) {
                $levelPositionHeat = round(0.10 * ($this->profile['LevelOpened'] - $this->profile['LevelClosed']), 2);
            } else {
                $levelPositionHeat = round(0.90 * ($this->profile['LevelOpened'] - $this->profile['LevelClosed']), 2);
            }
            if (($temperature > 30.0) || (($levelAct === $levelPositionHeat) && ($temperature > (30.0 - 0.5)))) {
                $level = $levelPositionHeat;
                $this->Logger_Dbg(__FUNCTION__, sprintf('Temp gt 30°, levelAct: %.2f, level: %.2f', $levelAct, $level));
            }

        }

        return $level;

    }

    private function getBrightnessThreshold(int $thresholdIDBrightness, float $levelAct, float $temperature = null): float
    {

        $thresholdBrightness = (float) GetValue($thresholdIDBrightness);

        $iBrightnessHysteresis = 0.1 * $thresholdBrightness;

        if ($temperature !== null) {
            //bei Temperaturen über 24 Grad soll der Rollladen auch bei geringerer Helligkeit heruntergefahren werden (10% je Grad Temperaturdifferenz zu 24°C)
            if ($temperature > 24) {
                $thresholdBrightness -= ($temperature - 24) * 0.10 * $thresholdBrightness;
            } //bei Temperaturen unter 10 Grad soll der Rollladen auch bei höherer Helligkeit nicht heruntergefahren werden
            else if ($temperature < 10) {
                $thresholdBrightness += (10 - $temperature) * 0.10 * $thresholdBrightness;
            }
        }

        //Hysterese berücksichtigen
        //der Rollladen ist (teilweise) herabgefahren
        if ($this->profile['Reversed']) {
            if ($levelAct < $this->profile['LevelOpened']) {
                $thresholdBrightness -= $iBrightnessHysteresis;
            } else {
                $thresholdBrightness += $iBrightnessHysteresis;
            }
        } elseif ($levelAct > $this->profile['LevelOpened']) {
            $thresholdBrightness -= $iBrightnessHysteresis;
        } else {
            $thresholdBrightness += $iBrightnessHysteresis;
        }

        return $thresholdBrightness;
    }

    private function getLevelFromSunPosition(float $rSunAzimuth, float $rSunAltitude): ?float
    {

        $rLevelSunPosition = null;
        if (($rSunAzimuth >= $this->ReadPropertyFloat('AzimuthFrom')) && ($rSunAzimuth <= $this->ReadPropertyFloat('AzimuthTo'))) {
            $AltitudeLow      = $this->ReadPropertyFloat('LowSunPositionAltitude');
            $AltitudeHigh     = $this->ReadPropertyFloat('HighSunPositionAltitude');
            $rAltitudeTanLow  = tan($AltitudeLow * M_PI / 180);
            $rAltitudeTanHigh = tan($AltitudeHigh * M_PI / 180);
            $rAltitudeTanAct  = tan($rSunAltitude * M_PI / 180);

            $blindLevelLow  = $this->ReadPropertyFloat('LowSunPositionBlindLevel');
            $blindLevelHigh = $this->ReadPropertyFloat('HighSunPositionBlindLevel');

            $rLevelSunPosition =
                $blindLevelLow + ($blindLevelHigh - $blindLevelLow) * ($rAltitudeTanAct - $rAltitudeTanLow) / ($rAltitudeTanHigh - $rAltitudeTanLow);

            if ($this->profile['Reversed']) {
                $rLevelSunPosition = min($rLevelSunPosition, $this->profile['LevelOpened']);
                $rLevelSunPosition = max($rLevelSunPosition, $this->profile['LevelClosed']);
            } else {
                $rLevelSunPosition = max($rLevelSunPosition, $this->profile['LevelOpened']);
                $rLevelSunPosition = min($rLevelSunPosition, $this->profile['LevelClosed']);
            }
        }

        return $rLevelSunPosition;

    }


    private function getLevelOfShadowingByBrightness(): ?float
    {
        $activatorID = $this->ReadPropertyInteger('ActivatorIDShadowingBrightness');

        if (($activatorID === 0) || !GetValue($activatorID)) {
            // keine Beschattung bei Helligkeit gewünscht bzw. nicht notwendig
            return null;
        }


        $brightnessID = $this->ReadPropertyInteger('BrightnessIDShadowingBrightness');
        if ($brightnessID === 0) {
            trigger_error('BrightnessIDShadowingBrightness === 0');
            return null;
        }

        $thresholdIDHighBrightness = $this->ReadPropertyInteger('ThresholdIDHighBrightness');
        $thresholdIDLessBrightness = $this->ReadPropertyInteger('ThresholdIDLessBrightness');
        if ($thresholdIDHighBrightness === 0 && $thresholdIDLessBrightness === 0) {
            trigger_error('ThresholdIDHighBrightness === 0 and ThresholdIDLowBrightness === 0');
            return null;
        }


        $brightness = GetValue($brightnessID);

        $thresholdBrightness = GetValue($thresholdIDHighBrightness);
        if (($thresholdIDHighBrightness > 0) && ($brightness > $thresholdBrightness)) {
            $level = $this->ReadPropertyFloat('LevelHighBrightnessShadowingBrightness');
            $this->Logger_Dbg(
                __FUNCTION__, sprintf('Beschattung bei hoher Helligkeit (%s/%s): level: %s', $brightness, $thresholdBrightness, $level)
            );
            return $level;
        }

        $thresholdBrightness = GetValue($thresholdIDLessBrightness);
        if (($thresholdIDLessBrightness > 0) && ($brightness > $thresholdBrightness)) {
            $level = $this->ReadPropertyFloat('LevelLessBrightnessShadowingBrightness');
            $this->Logger_Dbg(
                __FUNCTION__, sprintf('Beschattung bei niedriger Helligkeit (%s/%s): level: %s', $brightness, $thresholdBrightness, $level)
            );
            return $level;
        }

        return null;
    }

    private function isMovementLocked(int $idLevel, int $tsBlindLastMovement, bool $isDay, int $tsIsDayChanged, int $tsAutomatik,
                                      float $blindLevelClosed, float $blindLevelOpened): bool
    {
        //zuerst prüfen, ob der Rollladen nach der letzten aut. Bewegung (+60sec) manuell bewegt wurde
        if ($tsBlindLastMovement <= strtotime('+1 minute', $tsAutomatik)) {
            return false;
        }

        $deactivationTimeManu = $this->ReadPropertyInteger('DeactivationManualMovement') * 60;

        // Das aktuelle Level im Jalousieaktor auslesen
        $levelAct = (float) GetValue($idLevel);

        //Zeitpunkt festhalten, sofern noch nicht geschehen
        if ($tsBlindLastMovement !== $this->ReadAttributeInteger('AttrTimeStampManual')) {
            $this->WriteAttributeInteger('AttrTimeStampManual', $tsBlindLastMovement);

            $this->Logger_Dbg(
                __FUNCTION__, sprintf(
                                'Rollladenlevel wurde manuell gesetzt: %s (%.2f), tsBlindLastMovement: %s, TimestampAutomatic: %s, TimestampManual: %s, deactivationTimeManu: %s/%s',
                                GetValueFormatted($idLevel), $levelAct, $this->FormatTimeStamp($tsBlindLastMovement),
                                $this->FormatTimeStamp($tsAutomatik), $this->FormatTimeStamp($this->ReadAttributeInteger('AttrTimeStampManual')),
                                time() - $tsBlindLastMovement, $deactivationTimeManu
                            )
            );

            if ($levelAct === $blindLevelClosed) {
                $this->Logger_Inf(sprintf('Der Rollladen \'%s\' wurde manuell geschlossen.', $this->objectName));
            } else if ($levelAct === $blindLevelOpened) {
                $this->Logger_Inf(sprintf('Der Rollladen \'%s\' wurde manuell geöffnet.', $this->objectName));
            } else {
                $levelPercent = ($levelAct - $this->profile['MinValue'])/($this->profile['MaxValue'] - $this->profile['MinValue']);

                $this->Logger_Inf(sprintf('Der Rollladen \'%s\' wurde manuell auf %.0f%% gefahren.', $this->objectName, 100 * $levelPercent));
            }

        }

        $bNoMove          = false;
        $tsManualMovement = $this->ReadAttributeInteger('AttrTimeStampManual');

        if ($isDay && ($tsManualMovement > $tsIsDayChanged)) {
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
                                    'Rollladen wurde manuell bewegt (Tag: %s). DeactivationTimeManu: %s/%s', date('H:i:s', $tsManualMovement),
                                    time() - $tsBlindLastMovement, $deactivationTimeManu
                                )
                );
            }

        } elseif (!$isDay && ($tsManualMovement > $tsIsDayChanged)) {
            //nachts gilt:
            //wenn die Bewegung nachts passiert ist
            $bNoMove = true;
            $this->Logger_Dbg(__FUNCTION__, sprintf('Rollladen wurde manuell bewegt (Nacht: %s)', date('H:i:s', $tsManualMovement)));
        }

        return $bNoMove;

    }

    //-----------------------------------------------
    public function MoveBlind(int $percentClose, int $deactivationTimeAuto, string $hint): bool
    {

        if (IPS_GetInstance($this->InstanceID)['InstanceStatus'] !== IS_ACTIVE) {
            return false;
        }

        $this->Logger_Dbg(
            __FUNCTION__, sprintf('percentClose: %s, deactivationTimeAuto: %s, hint: %s', $percentClose, $deactivationTimeAuto, $hint)
        );

        // globale Instanzvariablen setzen
        $this->objectName = IPS_GetObject($this->InstanceID)['ObjectName'];
        $this->profile    = $this->GetProfileInformation();

        if ($this->profile === false) {
            return false;
        }


        $lastBlindMove = $this->ReadAttributeString('lastBlindMove');
        if ($lastBlindMove !== '') {
            $lastBlindMove = json_decode($lastBlindMove, true);
            if (($lastBlindMove['movement']['percentClose'] === $percentClose) && ($lastBlindMove['movement']['percentClose'] === $percentClose)
                && ((time() - $lastBlindMove['timeStamp']) < 30)) {
                //dieselbe Bewegung in den letzten 30 Sekunden
                $this->Logger_Dbg(
                    __FUNCTION__, sprintf('Move ignored! Same movement just %s s before', time() - $lastBlindMove['timeStamp'])
                );
                return false;
            }

        }

        $levelNew = $this->profile['MinValue'] + ($percentClose / 100) * ($this->profile['MaxValue'] - $this->profile['MinValue']);

        if ($this->profile['Reversed']) {
            $levelNew = $this->profile['MaxValue'] - $levelNew;
        }

        $levelID             = $this->ReadPropertyInteger('BlindLevelID');
        $levelAct            = GetValue($levelID); //integer and float are supported
        $levelDiffPercentage = abs($levelNew - $levelAct) / ($this->profile['MaxValue'] - $this->profile['MinValue']);
        $timeDiffAuto        = time() - $this->ReadAttributeInteger('AttrTimeStampAutomatic');

        $this->Logger_Dbg(
            __FUNCTION__, sprintf(
                            'levelAct: %s, levelNew: %s, levelDiffPercentage: %.2f/0,05, timeDiffAuto: %s/%s', $levelAct, $levelNew,
                            $levelDiffPercentage, $timeDiffAuto, $deactivationTimeAuto
                        )
        );

        $ret = true;

        // Wenn sich das aktuelle Level um mehr als 5% von neuem Level unterscheidet
        if (($levelDiffPercentage > 0.05) && ($timeDiffAuto >= $deactivationTimeAuto)) {

            // Level setzen
            //Wert übertragen
            if (@RequestAction($levelID, $levelNew)) {
                // Timestamp der Automatik merken (sonst wird die Bewegung später als manuelle Bewegung erkannt)
                $this->WriteAttributeInteger('AttrTimeStampAutomatic', time());
                $this->WriteAttributeString(
                    'lastBlindMove', json_encode(['timeStamp' => time(), 'movement' => ['percentClose' => $percentClose, 'hint' => $hint]])
                );
                $this->Logger_Dbg(
                    __FUNCTION__,
                    "$this->objectName: TimestampAutomatik: " . $this->FormatTimeStamp($this->ReadAttributeInteger('AttrTimeStampAutomatic'))
                );

                $this->WriteInfo($levelNew, $hint);
            } else {
                $this->Logger_Err(
                    'Fehler beim Setzen der Werte. (id = ' . $levelID . ', Value = ' . $percentClose . ')'
                );
                $ret = false;
            }
            $this->Logger_Dbg(__FUNCTION__, $this->objectName . ': ' . $levelAct . ' to ' . $levelNew);

            // kleine Pause, um Kommunikationsstörungen zu vermeiden
            sleep(5);

        }

        return $ret;
    }

    private function WriteInfo(float $rLevelneu, string $hint): void
    {
        if ($rLevelneu === (float) $this->profile['LevelClosed']) {
            $logMessage = sprintf('Der Rollladen \'%s\' wurde geschlossen.', $this->objectName);
        } else if ($rLevelneu === (float) $this->profile['LevelOpened']) {
            $logMessage = sprintf('Der Rollladen \'%s\' wurde geöffnet.', $this->objectName);
        } else {
            $levelPercent = ($rLevelneu - $this->profile['MinValue'])/($this->profile['MaxValue'] - $this->profile['MinValue']);
            $logMessage = sprintf('Der Rollladen \'%s\' wurde auf %.0f%% gefahren.', $this->objectName, 100 * $levelPercent);
        }

        if ($hint === '') {
            $this->Logger_Inf($logMessage);
        } else {
            $this->Logger_Inf(substr($logMessage, 0, -1) . ' (' . $hint . ')');
        }
    }

    private function checkTimeTable(): int
    {
        $eventScheduleGroups = IPS_GetEvent($this->ReadPropertyInteger('WeeklyTimeTableEventID'))['ScheduleGroups'];

        foreach ($eventScheduleGroups as $scheduleGroup) {
            $countID1 = $this->CountNumberOfPointsWithActionId($scheduleGroup['Points'], 1); //down
            $countID2 = $this->CountNumberOfPointsWithActionId($scheduleGroup['Points'], 2); //up

            if (($countID1 + $countID2) === 0) {
                $this->Logger_Dbg(
                    __FUNCTION__, sprintf(
                                    'Invalid TimeTable: No Points with ActionID 1 or 2 found. (ScheduleGroup: %s)', json_encode($scheduleGroup)
                                )
                );
                return self::STATUS_INST_TIMETABLE_IS_INVALID;
            }

            if ($countID2 > 1) {
                $this->Logger_Dbg(
                    __FUNCTION__, sprintf(
                                    'Invalid TimeTable: More (%s) than one Point with ActionID 2. (ScheduleGroup: %s)', $countID2,
                                    json_encode($scheduleGroup)
                                )
                );
                return self::STATUS_INST_TIMETABLE_IS_INVALID;
            }
        }

        return 0;

    }

    private function CountNumberOfPointsWithActionId(array $points, int $actionID): int
    {
        $count = 0;
        foreach ($points as $point) {
            if ($point['ActionID'] === $actionID) {
                $count++;
            }
        }
        return $count;
    }

    private function getIsDayByDayDetection(&$brightness): ?bool
    {
        $isDayDayDetection = null;

        if (($this->ReadPropertyInteger('IsDayIndicatorID') === 0)
            && (($this->ReadPropertyInteger('BrightnessID') === 0) || $this->ReadPropertyInteger('BrightnessThresholdID') === 0)) {
            return null;
        }

        $isDayIndicatorID = $this->ReadPropertyInteger('IsDayIndicatorID');
        if ($isDayIndicatorID > 0) {
            $isDayIndicator = GetValueBoolean($isDayIndicatorID);
            $this->Logger_Dbg(__FUNCTION__, sprintf('DayIndicator (#%s): %d', $isDayIndicatorID, $isDayIndicator));
            return $isDayIndicator;
        }

        //optional Values
        $brightnessID = $this->ReadPropertyInteger('BrightnessID');
        if ($brightnessID) {
            $brightness = GetValue($brightnessID);
        }

        $brightnessThresholdID = $this->ReadPropertyInteger('BrightnessThresholdID');
        if ($brightnessThresholdID) {
            $brightnessThreshold = GetValue($brightnessThresholdID);
        }

        if (isset($brightness, $brightnessThreshold)) {
            $this->Logger_Dbg(__FUNCTION__, sprintf('Brightness: %.1f, Threshold: %.1f', $brightness, $brightnessThreshold));
            return $brightness > $brightnessThreshold;
        }

        return null;

    }

    private function getIsDayByTimeSchedule(): ?bool
    {
        //Ermitteln, welche Zeiten heute und gestern gelten

        $heute_auf = null;
        $heute_ab  = null;

        if (!$this->getUpAndDownPoints($heute_auf, $heute_ab)) {
            return null;
        }

        $this->Logger_Dbg(__FUNCTION__, sprintf('heute_auf: %s, heute_ab: %s', $heute_auf, $heute_ab ?? 'null'));

        return (time() >= strtotime($heute_auf)) && ($heute_ab === null || (time() <= strtotime($heute_ab)));

    }

    //-------------------------------------
    private function getUpAndDownPoints(?string &$heute_auf, ?string &$heute_ab): bool
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

        //Ermitteln, welche Zeiten heute laut Wochenplan gelten
        if (!$this->getUpDownTime($weekDay, $heute_auf, $heute_ab)) {
            return false;
        }

        //gibt es übersteuernde Zeiten?
        $idWakeUpTime = $this->ReadPropertyInteger('WakeUpTimeID');
        if (($idWakeUpTime > 0) && (GetValueString($idWakeUpTime) !== '')) {
            $heute_auf = date('H:i', strtotime(GetValueString($idWakeUpTime)) + $this->ReadPropertyInteger('WakeUpTimeOffset') * 60);
            if ($heute_auf === false) {
                return false;
            }
            $this->Logger_Dbg(__FUNCTION__, sprintf('WakeUpTime found: %s', $heute_auf));
        }

        $idBedTime = $this->ReadPropertyInteger('BedTimeID');
        if (($idBedTime > 0) && (GetValueString($idBedTime) !== '')) {
            $heute_ab = date('H:i', strtotime(GetValueString($idBedTime)) + $this->ReadPropertyInteger('BedTimeOffset') * 60);
            if ($heute_ab === false) {
                return false;
            }
            $this->Logger_Dbg(__FUNCTION__, sprintf('BedTime: %s', $heute_ab));
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

        $auf = $this->getUpTimeOfDay($weekDay, $event['ScheduleGroups']);
        $ab  = $this->getDownTimeOfDay($weekDay, $event['ScheduleGroups']);

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
                    if ($point['ActionID'] === 2) {
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
                    if ($point['ActionID'] === 1) {
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
        if ($tsAutomaticAttribute === 0) {
            $tsAutomaticAttribute = $tsBlindLevelChanged;
            $this->WriteAttributeInteger('AttrTimeStampAutomatic', $tsAutomaticAttribute);
        }

        if (($tsAutomaticVariable > $tsBlindLevelChanged) && ($tsAutomaticAttribute !== $tsBlindLevelChanged) && $this->GetValue('ACTIVATED')) {
            // .. dann Timestamp Automatik mit Timestamp des Rollladens gleichsetzen
            $this->WriteAttributeInteger('AttrTimeStampAutomatic', $tsBlindLevelChanged);
            $this->Logger_Inf(sprintf('Der Rollladen \'%s\' bewegt sich nun wieder automatisch.', $this->objectName));
        }
        return $tsBlindLevelChanged;
    }

    private function FormatTimeStamp(int $ts): string
    {
        return date('Y-m-d H:i:s', $ts);
    }

    private function GetFormattedValue($variableID): string
    {
        if ((IPS_GetVariable($variableID)['VariableCustomProfile'] !== '') || (IPS_GetVariable($variableID)['VariableProfile'] !== '')) {
            return GetValueFormatted($variableID);
        }

        $val = GetValue($variableID);
        $ret = '';

        if (is_string($val)) {
            $ret = $val;
        } else if (is_bool($val)) {
            if ($val) {
                $ret = 'true';
            } else {
                $ret = 'false';
            }
        } else if (is_float($val) || is_int($val)) {
            $ret = (string) $val;
        } else if (is_array($val)) {
            $ret = json_encode($val);
        } else if (is_object($val) || is_scalar($val)) {
            $ret = serialize($val);
        } else if ($val === null) {
            $ret = 'null';
        }

        return $ret;
    }

    private function Logger_Err(string $message): void
    {
        $this->SendDebug('LOG_ERR', $message, 0);
        if (function_exists('IPSLogger_Err') && $this->ReadPropertyBoolean('WriteLogInformationToIPSLogger')) {
            IPSLogger_Err(__CLASS__, $message);
        }

        $this->LogMessage($message, KL_ERROR);

        $this->SetValue('LAST_MESSAGE', $message);
    }

    private function Logger_Inf(string $message): void
    {
        $this->SendDebug('LOG_INFO', $message, 0);
        if (function_exists('IPSLogger_Inf') && $this->ReadPropertyBoolean('WriteLogInformationToIPSLogger')) {
            IPSLogger_Inf(__CLASS__, $message);
        } else {
            $this->LogMessage($message, KL_NOTIFY);
        }

        $this->SetValue('LAST_MESSAGE', $message);
    }

    private function Logger_Dbg(string $message, string $data): void
    {
        $this->SendDebug($message, $data, 0);
        if (function_exists('IPSLogger_Dbg') && $this->ReadPropertyBoolean('WriteDebugInformationToIPSLogger')) {
            IPSLogger_Dbg(__CLASS__ . '.' . IPS_GetObject($this->InstanceID)['ObjectName'] . '.' . $message, $data);
        }
        if ($this->ReadPropertyBoolean('WriteDebugInformationToLogfile')) {
            $this->LogMessage(sprintf('%s: %s', $message, $data), KL_DEBUG);
        }
    }


}
