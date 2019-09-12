<?php

declare(strict_types=1);

if (function_exists('IPSUtils_Include')) {
    IPSUtils_Include('IPSLogger.inc.php', 'IPSLibrary::app::core::IPSLogger');
}

/** @noinspection AutoloadingIssuesInspection */
class BlindController extends IPSModule
{
    //Status
    private const STATUS_INST_TIMETABLE_ID_IS_INVALID                                = 201;
    private const STATUS_INST_HOLYDAY_INDICATOR_ID_IS_INVALID                        = 202;
    private const STATUS_INST_BLIND_LEVEL_ID_IS_INVALID                              = 203;
    private const STATUS_INST_BRIGHTNESS_ID_IS_INVALID                               = 204;
    private const STATUS_INST_BRIGHTNESS_THRESHOLD_ID_IS_INVALID                     = 205;
    private const STATUS_INST_ISDAY_INDICATOR_ID_IS_INVALID                          = 206;
    private const STATUS_INST_DEACTIVATION_TIME_MANUAL_IS_INVALID                    = 207;
    private const STATUS_INST_DEACTIVATION_TIME_AUTOMATIC_IS_INVALID                 = 208;
    private const STATUS_INST_TIMETABLE_IS_INVALID                                   = 209;
    private const STATUS_INST_CONTACT1_ID_IS_INVALID                                 = 210;
    private const STATUS_INST_CONTACT2_ID_IS_INVALID                                 = 211;
    private const STATUS_INST_EMERGENCY_CONTACT_ID_IS_INVALID                        = 212;
    private const STATUS_INST_WAKEUPTIME_ID_IS_INVALID                               = 213;
    private const STATUS_INST_SLEEPTIME_ID_IS_INVALID                                = 214;
    private const STATUS_INST_DAYSTART_ID_IS_INVALID                                 = 215;
    private const STATUS_INST_DAYEND_ID_IS_INVALID                                   = 216;
    private const STATUS_INST_BLIND_LEVEL_IS_EMULATED                                = 217;
    private const STATUS_INST_SLATS_LEVEL_IS_EMULATED                                = 218;
    private const STATUS_INST_ACTIVATORIDSHADOWINGBYSUNPOSITION_IS_INVALID           = 220;
    private const STATUS_INST_AZIMUTHID_IS_INVALID                                   = 221;
    private const STATUS_INST_ALTITUDEID_IS_INVALID                                  = 222;
    private const STATUS_INST_BRIGTHNESSIDSHADOWINGBYSUNPOSITION_IS_INVALID          = 223;
    private const STATUS_INST_BRIGHTNESSTHRESHOLDIDSHADOWINGBYSUNPOSITION_IS_INVALID = 224;
    private const STATUS_INST_ROOMTEMPERATUREID_IS_INVALID                           = 225;
    private const STATUS_INST_ACTIVATORIDSHADOWINGBRIGHTNESS_IS_INVALID              = 230;
    private const STATUS_INST_BRIGHTNESSIDSHADOWINGBRIGHTNESS_IS_INVALID             = 231;
    private const STATUS_INST_THRESHOLDIDHIGHBRIGHTNESS_IS_INVALID                   = 232;
    private const STATUS_INST_THRESHOLDIDLESSRIGHTNESS_IS_INVALID                    = 233;
    private const STATUS_INST_BLINDLEVEL_IS_OUT_OF_RANGE                             = 234;
    private const STATUS_INST_SLATSLEVEL_IS_OUT_OF_RANGE                             = 235;
    private const STATUS_INST_SLATSLEVEL_ID_IS_INVALID                               = 236;
    private const STATUS_INST_BLINDLEVEL_ID_PROFILE_NOT_SET                          = 237;
    private const STATUS_INST_BLINDLEVEL_ID_PROFILE_MIN_MAX_INVALID                  = 238;
    private const STATUS_INST_SLATSLEVEL_ID_PROFILE_MIN_MAX_INVALID                  = 239;
    private const STATUS_INST_SLATSLEVEL_ID_PROFILE_NOT_SET                          = 240;

    //property names
    private const PROP_BLINDLEVELID                                = 'BlindLevelID';
    private const PROP_SLATSLEVELID                                = 'SlatsLevelID';
    private const PROP_CONTACTCLOSE1ID                             = 'ContactClose1ID';
    private const PROP_CONTACTCLOSE2ID                             = 'ContactClose2ID';
    private const PROP_CONTACTCLOSELEVEL1                          = 'ContactCloseLevel1';
    private const PROP_CONTACTCLOSELEVEL2                          = 'ContactCloseLevel2';
    private const PROP_CONTACTCLOSESLATSLEVEL1                     = 'ContactCloseSlatsLevel1';
    private const PROP_CONTACTCLOSESLATSLEVEL2                     = 'ContactCloseSlatsLevel2';
    private const PROP_CONTACTOPEN1ID                              = 'ContactOpen1ID';
    private const PROP_CONTACTOPEN2ID                              = 'ContactOpen2ID';
    private const PROP_CONTACTOPENLEVEL1                           = 'ContactOpenLevel1';
    private const PROP_CONTACTOPENLEVEL2                           = 'ContactOpenLevel2';
    private const PROP_CONTACTOPENSLATSLEVEL1                      = 'ContactOpenSlatsLevel1';
    private const PROP_CONTACTOPENSLATSLEVEL2                      = 'ContactOpenSlatsLevel2';
    private const PROP_EMERGENCYCONTACTID                          = 'EmergencyContactID';
    private const PROP_CONTACTSTOCLOSEHAVEHIGHERPRIORITY           = 'ContactsToCloseHaveHigherPriority';
    private const PROP_ACTIVATORIDSHADOWINGBYSUNPOSITION           = 'ActivatorIDShadowingBySunPosition';
    private const PROP_AZIMUTHID                                   = 'AzimuthID';
    private const PROP_ALTITUDEID                                  = 'AltitudeID';
    private const PROP_AZIMUTHFROM                                 = 'AzimuthFrom';
    private const PROP_AZIMUTHTO                                   = 'AzimuthTo';
    private const PROP_ALTITUDEFROM                                = 'AltitudeFrom';
    private const PROP_ALTITUDETO                                  = 'AltitudeTo';
    private const PROP_BRIGHTNESSIDSHADOWINGBYSUNPOSITION          = 'BrightnessIDShadowingBySunPosition';
    private const PROP_BRIGHTNESSTHRESHOLDIDSHADOWINGBYSUNPOSITION = 'BrightnessThresholdIDShadowingBySunPosition';
    private const PROP_LOWSUNPOSITIONBLINDLEVEL                    = 'LowSunPositionBlindLevel';
    private const PROP_HIGHSUNPOSITIONBLINDLEVEL                   = 'HighSunPositionBlindLevel';
    private const PROP_LOWSUNPOSITIONSLATSLEVEL                    = 'LowSunPositionSlatsLevel';
    private const PROP_HIGHSUNPOSITIONSLATSLEVEL                   = 'HighSunPositionSlatsLevel';
    private const PROP_ACTIVATEDINDIVIDUALDAYLEVELS                = 'ActivatedIndividualDayLevels';
    private const PROP_DAYBLINDLEVEL                               = 'DayBlindLevel';
    private const PROP_DAYSLATSLEVEL                               = 'DaySlatsLevel';
    private const PROP_ACTIVATEDINDIVIDUALNIGHTLEVELS              = 'ActivatedIndividualNightLevels';
    private const PROP_NIGHTBLINDLEVEL                             = 'NightBlindLevel';
    private const PROP_NIGHTSLATSLEVEL                             = 'NightSlatsLevel';
    private const PROP_BLINDLEVELLESSBRIGHTNESSSHADOWINGBRIGHTNESS = 'BlindLevelLessBrightnessShadowingBrightness';
    private const PROP_SLATSLEVELLESSBRIGHTNESSSHADOWINGBRIGHTNESS = 'SlatsLevelLessBrightnessShadowingBrightness';
    private const PROP_BLINDLEVELHIGHBRIGHTNESSSHADOWINGBRIGHTNESS = 'BlindLevelHighBrightnessShadowingBrightness';
    private const PROP_SLATSLEVELHIGHBRIGHTNESSSHADOWINGBRIGHTNESS = 'SlatsLevelHighBrightnessShadowingBrightness';

    //attribute names
    private const ATTR_MANUALMOVEMENT      = 'manualMovement';
    private const ATTR_LASTMOVE            = 'lastMovement';
    private const ATTR_TIMESTAMP_AUTOMATIC = 'TimeStampAutomatic';
    private const ATTR_CONTACT_OPEN        = 'AttrContactOpen';

    //variable names
    private const VAR_IDENT_LAST_MESSAGE = 'LAST_MESSAGE';
    private const VAR_IDENT_ACTIVATED    = 'ACTIVATED';

    private $objectName;

    private $profileBlindLevel;

    private $profileSlatsLevel;

    // Überschreibt die interne IPS_Create($id) Funktion
    public function Create()
    {
        // Diese Zeile nicht löschen.
        parent::Create();

        $this->RegisterProperties();
        $this->Logger_Dbg(
            __FUNCTION__, 'RegisterAttributes'
        );

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

        $this->Logger_Dbg(__FUNCTION__, sprintf('Ident: %s, Value: %s', $Ident, $Value));

        /** @noinspection DegradedSwitchInspection */
        switch ($Ident) {
            case self::VAR_IDENT_ACTIVATED:
                if ($Value) {
                    //reset manual movement
                    $this->resetManualMovement();

                } else {
                    $this->Logger_Inf(sprintf('\'%s\' wurde deaktiviert.', IPS_GetObject($this->InstanceID)['ObjectName']));
                }
                break;

            default:
                trigger_error(sprintf('Instance %s: Unknown Ident %s', $this->InstanceID, $Ident));
                return false;
        }

        if (is_bool($Value)) {
            $this->Logger_Dbg(__FUNCTION__, sprintf('Ident: %s, Value: %s', $Ident, (int) $Value));
        } else {
            $this->Logger_Dbg(__FUNCTION__, sprintf('Ident: %s, Value: %s', $Ident, $Value));
        }

        if ($this->SetValue($Ident, $Value)) {
            $this->SetInstanceStatusAndTimerEvent();
            $this->ControlBlind(false);
            return true;
        }

        return false;
    }

    public function MessageSink($TimeStamp, $SenderID, $Message, $Data)
    {

        parent::MessageSink($TimeStamp, $SenderID, $Message, $Data);

        if (json_decode($this->GetBuffer('LastMessage'), true) === [$SenderID, $Message, $Data]) {
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

        if ($this->GetValue(self::VAR_IDENT_ACTIVATED)) {
            // controlBlind mit Prüfung, ob der Rollladen sofort bewegt werden soll
            $this->ControlBlind(
                !in_array(
                    $SenderID, [
                    $this->ReadPropertyInteger(self::PROP_CONTACTOPEN1ID),
                    $this->ReadPropertyInteger(self::PROP_CONTACTOPEN2ID),
                    $this->ReadPropertyInteger(self::PROP_CONTACTCLOSE1ID),
                    $this->ReadPropertyInteger(self::PROP_CONTACTCLOSE2ID),
                    $this->ReadPropertyInteger(self::PROP_EMERGENCYCONTACTID),
                    $this->ReadPropertyInteger('ActivatorIDShadowingBrightness'),
                    $this->ReadPropertyInteger(self::PROP_ACTIVATORIDSHADOWINGBYSUNPOSITION)], true
                )
            );
        }
    }

    /**
     * Die folgenden Funktionen stehen automatisch zur Verfügung, wenn das Modul über die "Module Control" eingefügt wurden.
     * Die Funktionen werden, mit dem selbst eingerichteten Prefix, in PHP und JSON-RPC zur Verfügung gestellt.
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
        $this->objectName        = IPS_GetObject($this->InstanceID)['ObjectName'];
        $this->profileBlindLevel = $this->GetProfileInformation(self::PROP_BLINDLEVELID);

        // $deactivationTimeAuto: Zeitraum, in dem das automatisch gesetzte Level
        // erhalten bleibt bevor es überschrieben wird.

        if ($considerDeactivationTimeAuto) {
            $deactivationTimeAuto = $this->ReadPropertyInteger('DeactivationAutomaticMovement') * 60;
        } else {
            $deactivationTimeAuto = 0;
        }

        $Hinweis = '';

        //Blind Level ID ermitteln
        $blindLevelId = $this->ReadPropertyInteger(self::PROP_BLINDLEVELID);

        // Die aktuellen Positionen im Jalousieaktor auslesen
        $positionsAct['BlindLevel'] = (float) GetValue($blindLevelId);

        //Slats Level ID ermitteln
        $slatsLevelId = $this->ReadPropertyInteger(self::PROP_SLATSLEVELID);
        if ($slatsLevelId !== 0) {
            $this->profileSlatsLevel    = $this->GetProfileInformation(self::PROP_SLATSLEVELID);
            $positionsAct['SlatsLevel'] = (float) GetValue($slatsLevelId);
        } else {
            $positionsAct['SlatsLevel'] = null;
        }

        // 'tagsüber' nach Wochenplan ermitteln
        $isDayByTimeSchedule = $this->getIsDayByTimeSchedule();

        // optionale Tageserkennung auswerten
        $brightness          = null;
        $isDayByDayDetection = $this->getIsDayByDayDetection($brightness, $positionsAct['BlindLevel']);

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

        //Zeitpunkt der letzten Rollladenbewegung (Höhe oder Lamellen)
        $tsBlindLastMovement = $this->GetBlindLastTimeStamp($blindLevelId, $slatsLevelId);

        // Attribut TimestampAutomatik auslesen
        $tsAutomatik = $this->ReadAttributeInteger(self::ATTR_TIMESTAMP_AUTOMATIC);

        if ($this->checkIsDayChange($isDay)) {
            //beim Tageswechsel ...

            //wird die anstehende Bewegung sofort ausgeführt
            $deactivationTimeAuto = 0;

            //wird eine manuelle Bewegung zurückgesetzt
            $this->WriteAttributeString(self::ATTR_MANUALMOVEMENT, json_encode(['timeStamp' => null, 'blindLevel' => null, 'slatsLevel' => null]));

            //wird keine Bewegungssperre gesetzt
            $bNoMove = false;

        } else {
            // prüfen, ob der Rollladen manuell bewegt wurde und somit eine Bewegungssperre besteht
            $bNoMove = $this->isMovementLocked(
                $positionsAct['BlindLevel'], $positionsAct['SlatsLevel'], $tsBlindLastMovement, $isDay,
                $this->ReadAttributeInteger('AttrTimeStampIsDayChange'), $tsAutomatik, $this->profileBlindLevel['LevelClosed'],
                $this->profileBlindLevel['LevelOpened'], $this->profileSlatsLevel['LevelClosed'], $this->profileSlatsLevel['LevelOpened']
            );
        }

        $this->Logger_Dbg(
            __FUNCTION__, sprintf(
                            'tsAutomatik: %s, tsBlind: %s, posActBlindLevel: %.2f,  posActSlatsLevel: %s, bNoMove: %s, isDay: %s (isDayByTimeSchedule: %s, isDayByDayDetection: %s, dayStart: %s, dayEnd: %s), considerDeactivationTimeAuto: %s',
                            $this->FormatTimeStamp($tsAutomatik), $this->FormatTimeStamp($tsBlindLastMovement), $positionsAct['BlindLevel'],
                            $positionsAct['SlatsLevel'] ?? 'null', (int) $bNoMove, (int) $isDay, (int) $isDayByTimeSchedule,
                            (isset($isDayByDayDetection) ? (int) $isDayByDayDetection : 'null'), $dayStart ?? 'null', $dayEnd ?? 'null',
                            (int) $considerDeactivationTimeAuto
                        )
        );

        if ($bNoMove) {
            $positionsNew = $positionsAct;
        } elseif ($isDay) {
            $lastManualMovement         = json_decode($this->ReadAttributeString(self::ATTR_MANUALMOVEMENT), true);
            $deactivationManualMovement = $this->ReadPropertyInteger('DeactivationManualMovement');
            if (isset($lastManualMovement['timeStamp'])
                && (($deactivationManualMovement === 0)
                    || strtotime(
                           '+ ' . $deactivationManualMovement . ' minutes', $lastManualMovement['timeStamp']
                       ) > time())) {
                $positionsNew['BlindLevel'] = $lastManualMovement['blindLevel'];
                $positionsNew['SlatsLevel'] = $lastManualMovement['slatsLevel'];
            } else {
                /** @noinspection NestedPositiveIfStatementsInspection */
                if ($this->ReadPropertyBoolean(self::PROP_ACTIVATEDINDIVIDUALDAYLEVELS)) {
                    $positionsNew['BlindLevel'] = $this->ReadPropertyFloat(self::PROP_DAYBLINDLEVEL);
                } else {
                    $positionsNew['BlindLevel'] = $this->profileBlindLevel['LevelOpened'];
                }
            }
            if ($this->ReadPropertyBoolean(self::PROP_ACTIVATEDINDIVIDUALDAYLEVELS)) {
                $positionsNew['SlatsLevel'] = $this->ReadPropertyFloat(self::PROP_DAYSLATSLEVEL);
            } else {
                $positionsNew['SlatsLevel'] = $this->profileSlatsLevel['LevelOpened'];
            }
            $Hinweis = 'Tag';
        } else { //it is night
            /** @noinspection NestedPositiveIfStatementsInspection */
            if ($this->ReadPropertyBoolean(self::PROP_ACTIVATEDINDIVIDUALNIGHTLEVELS)) {
                $positionsNew['BlindLevel'] = $this->ReadPropertyFloat(self::PROP_NIGHTBLINDLEVEL);
                $positionsNew['SlatsLevel'] = $this->ReadPropertyFloat(self::PROP_NIGHTSLATSLEVEL);
                $Hinweis                    = 'Nachtposition';
            } else {
                $positionsNew['BlindLevel'] = $this->profileBlindLevel['LevelClosed'];
                $positionsNew['SlatsLevel'] = $this->profileSlatsLevel['LevelClosed'];
                $Hinweis                    = 'Nacht';
            }
        }

        if (isset($isDayByDayDetection, $brightness)) {
            $Hinweis .= ', ' . $this->GetFormattedValue($this->ReadPropertyInteger('BrightnessID'));
        }

        // am Tag wird überprüft, ob das Fenster beschattet werden soll
        if ($isDay && !$bNoMove) {

            // prüfen, ob Beschattung nach Sonnenstand gewünscht und notwendig
            $positionsShadowingBySunPosition = $this->getPositionsOfShadowingBySunPosition($positionsAct['BlindLevel']);
            if ($positionsShadowingBySunPosition !== null) {

                if ($this->profileBlindLevel['Reversed']) {
                    $positionsNew['BlindLevel'] = min($positionsNew['BlindLevel'], $positionsShadowingBySunPosition['BlindLevel']);
                } else {
                    $positionsNew['BlindLevel'] = max($positionsNew['BlindLevel'], $positionsShadowingBySunPosition['BlindLevel']);
                }

                if ($this->ReadPropertyInteger(self::PROP_SLATSLEVELID) !== 0) {
                    if ($this->profileSlatsLevel['Reversed']) {
                        $positionsNew['SlatsLevel'] = min($positionsNew['SlatsLevel'], $positionsShadowingBySunPosition['SlatsLevel']);
                    } else {
                        $positionsNew['SlatsLevel'] = max($positionsNew['SlatsLevel'], $positionsShadowingBySunPosition['SlatsLevel']);
                    }
                }

                if ($this->ReadPropertyInteger('BrightnessIDShadowingBrightness') > 0) {
                    $Hinweis =
                        'Beschattung nach Sonnenstand,' . $this->GetFormattedValue($this->ReadPropertyInteger('BrightnessIDShadowingBrightness'));
                } else {
                    $Hinweis = 'Beschattung nach Sonnenstand';
                }
            }

            // prüfen, ob Beschattung bei Helligkeit gewünscht und notwendig
            $positionsShadowingBrightness = $this->getPositionsOfShadowingByBrightness($positionsAct['BlindLevel']);
            if ($positionsShadowingBrightness !== null) {

                if ($this->profileBlindLevel['Reversed']) {
                    $positionsNew['BlindLevel'] = min($positionsNew['BlindLevel'], $positionsShadowingBrightness['BlindLevel']);
                } else {
                    $positionsNew['BlindLevel'] = max($positionsNew['BlindLevel'], $positionsShadowingBrightness['BlindLevel']);
                }

                if ($this->ReadPropertyInteger(self::PROP_SLATSLEVELID) !== 0) {
                    if ($this->profileSlatsLevel['Reversed']) {
                        $positionsNew['SlatsLevel'] = min($positionsNew['SlatsLevel'], $positionsShadowingBrightness['SlatsLevel']);
                    } else {
                        $positionsNew['SlatsLevel'] = max($positionsNew['SlatsLevel'], $positionsShadowingBrightness['SlatsLevel']);
                    }
                }

                $Hinweis = 'Beschattung nach Helligkeit, ' . $this->GetFormattedValue($this->ReadPropertyInteger('BrightnessIDShadowingBrightness'));
            }

        } else {
            // nachts gilt keine deactivation Time
            $deactivationTimeAuto = 0;
        }

        // prüfen, ob der Emergency Kontakt offen ist
        $levelContactEmergency = $this->getLevelEmergencyContact();

        // prüfen, ob ein Kontakt zum Öffnen oder Schließen offen ist
        $positionsContactOpenBlind  = $this->getPositionsOfOpenBlindContact();
        $positionsContactCloseBlind = $this->getPositionsOfCloseBlindContact();

        if (($positionsContactOpenBlind !== null) && ($positionsContactCloseBlind !== null)) {
            //check the priority
            if ($this->ReadPropertyBoolean(self::PROP_CONTACTSTOCLOSEHAVEHIGHERPRIORITY)) {
                $positionsContactOpenBlind = null;
            } else {
                $positionsContactCloseBlind = null;
            }
        }

        if ($levelContactEmergency !== null) {
            // wenn  der Emergency Kontakt geöffnet ist dann
            // wird die Bewegungssperre aufgehoben und der Rollladen geöffnet
            $deactivationTimeAuto       = 0;
            $bNoMove                    = false;
            $positionsNew['BlindLevel'] = $levelContactEmergency;
            $Hinweis                    = 'Notfallkontakt offen';

            //im Notfall wird die Automatik deaktiviert
            $bEmergency = true;

            $this->WriteAttributeBoolean(self::ATTR_CONTACT_OPEN, true);
            $this->Logger_Dbg(
                __FUNCTION__, sprintf(
                                'NOTFALL: Kontakt geöffnet (posActBlindLevel: %s, posNewBlindLevel: %s)', $positionsAct['BlindLevel'],
                                $positionsNew['BlindLevel']
                            )
            );

        } elseif ($positionsContactOpenBlind !== null) {
            // wenn  ein Kontakt geöffnet ist und der Rollladen bzw die Lamellen unter dem ContactOpen Level steht, dann
            // wird die Bewegungssperre aufgehoben und das Level auf das Mindestlevel bei geöffnetem Kontakt gesetzt
            $deactivationTimeAuto = 0;
            $bNoMove              = false;
            if ($this->profileBlindLevel['Reversed']) {
                if ($positionsContactOpenBlind['BlindLevel'] > $positionsNew['BlindLevel']) {
                    $positionsNew['BlindLevel'] = $positionsContactOpenBlind['BlindLevel'];
                    $Hinweis                    = 'Kontakt offen';
                }
            } elseif ($positionsContactOpenBlind['BlindLevel'] < $positionsNew['BlindLevel']) {
                $positionsNew['BlindLevel'] = $positionsContactOpenBlind['BlindLevel'];
                $Hinweis                    = 'Kontakt offen';
            }

            if ($this->profileSlatsLevel['Reversed']) {
                if ($positionsContactOpenBlind['SlatsLevel'] > $positionsNew['SlatsLevel']) {
                    $positionsNew['SlatsLevel'] = $positionsContactOpenBlind['SlatsLevel'];
                    $Hinweis                    = 'Kontakt offen';
                }
            } elseif ($positionsContactOpenBlind['SlatsLevel'] < $positionsNew['SlatsLevel']) {
                $positionsNew['SlatsLevel'] = $positionsContactOpenBlind['SlatsLevel'];
                $Hinweis                    = 'Kontakt offen';
            }

            $this->WriteAttributeBoolean(self::ATTR_CONTACT_OPEN, true);
            $this->Logger_Dbg(
                __FUNCTION__, sprintf(
                                'Kontakt geöffnet (posActBlindLevel: %s, posNewBlindLevel: %s, posActSlatsLevel: %s, posNewSlatsLevel: %s)',
                                $positionsAct['BlindLevel'], $positionsNew['BlindLevel'], $positionsAct['SlatsLevel'] ?? 'null',
                                $positionsNew['SlatsLevel'] ?? 'null'
                            )
            );

        } elseif ($positionsContactCloseBlind !== null) {
            // wenn  ein Kontakt geöffnet ist und der Rollladen bzw. die Lamellen oberhalb dem ContactClose Level steht, dann
            // wird die Bewegungssperre aufgehoben und das Level auf das Mindestlevel bei geöffnetem Kontakt gesetzt
            $deactivationTimeAuto = 0;
            $bNoMove              = false;
            if ($this->profileBlindLevel['Reversed']) {
                if ($positionsContactCloseBlind['BlindLevel'] < $positionsNew['BlindLevel']) {
                    $positionsNew['BlindLevel'] = $positionsContactCloseBlind['BlindLevel'];
                    $Hinweis                    = 'Kontakt offen';
                }
            } elseif ($positionsContactCloseBlind['BlindLevel'] > $positionsNew['BlindLevel']) {
                $positionsNew['BlindLevel'] = $positionsContactCloseBlind['BlindLevel'];
                $Hinweis                    = 'Kontakt offen';
            }

            if ($this->profileSlatsLevel['Reversed']) {
                if ($positionsContactCloseBlind['SlatsLevel'] < $positionsNew['SlatsLevel']) {
                    $positionsNew['SlatsLevel'] = $positionsContactCloseBlind['SlatsLevel'];
                    $Hinweis                    = 'Kontakt offen';
                }
            } elseif ($positionsContactCloseBlind['SlatsLevel'] > $positionsNew['SlatsLevel']) {
                $positionsNew['SlatsLevel'] = $positionsContactCloseBlind['SlatsLevel'];
                $Hinweis                    = 'Kontakt offen';
            }

            $this->WriteAttributeBoolean(self::ATTR_CONTACT_OPEN, true);
            $this->Logger_Dbg(
                __FUNCTION__, sprintf(
                                'Kontakt geöffnet (posActBlindLevel: %s, posNewBlindLevel: %s, posActSlatsLevel: %s, posNewSlatsLevel: %s)',
                                $positionsAct['BlindLevel'], $positionsNew['BlindLevel'], $positionsAct['SlatsLevel'] ?? 'null',
                                $positionsNew['SlatsLevel'] ?? 'null'
                            )
            );

        } elseif ($this->ReadAttributeBoolean(self::ATTR_CONTACT_OPEN)) {
            // wenn die Rollladenposition noch auf Kontakt offen Position steht
            $deactivationTimeAuto = 0;
            $this->WriteAttributeBoolean(self::ATTR_CONTACT_OPEN, false);
        }

        if (!$bNoMove) {

            $blindLevel = $positionsNew['BlindLevel'] / ($this->profileBlindLevel['MaxValue'] - $this->profileBlindLevel['MinValue']);
            if ($this->profileBlindLevel['Reversed']) {
                $blindLevel = 1 - $blindLevel;
            }

            if ($this->ReadPropertyInteger(self::PROP_SLATSLEVELID) !== 0) {
                $slatsLevel = $positionsNew['SlatsLevel'] / ($this->profileSlatsLevel['MaxValue'] - $this->profileSlatsLevel['MinValue']);
                if ($this->profileSlatsLevel['Reversed']) {
                    $slatsLevel = 1 - $positionsNew['SlatsLevel'];
                }
                $this->MoveBlind((int) ($blindLevel * 100), (int) ($slatsLevel * 100), $deactivationTimeAuto, $Hinweis);
            } else {
                $this->MoveBlind((int) ($blindLevel * 100), null, $deactivationTimeAuto, $Hinweis);
            }
        }

        //im Notfall wird die Automatik deaktiviert
        if (isset($bEmergency)) {
            $this->SetValue(self::VAR_IDENT_ACTIVATED, false);
            $this->SetInstanceStatusAndTimerEvent();
        }

        IPS_SemaphoreLeave($this->InstanceID . '- Blind');

        return true;
    }

    private function resetManualMovement(): void
    {
        $this->WriteAttributeString(
            self::ATTR_MANUALMOVEMENT, json_encode(['timeStamp' => null, 'blindLevel' => null, 'slatsLevel' => null])
        );

        // Timestamp der Automatik merken (sonst wird die Bewegung später als manuelle Bewegung erkannt)
        $this->WriteAttributeInteger(self::ATTR_TIMESTAMP_AUTOMATIC, time());

        $this->Logger_Inf(sprintf('\'%s\' bewegt sich nun wieder automatisch.', IPS_GetObject($this->InstanceID)['ObjectName']));
    }

    private function RegisterProperties(): void
    {
        $this->RegisterPropertyInteger(self::PROP_BLINDLEVELID, 0);
        $this->RegisterPropertyInteger(self::PROP_SLATSLEVELID, 0);

        //week plan
        $this->RegisterPropertyInteger('WeeklyTimeTableEventID', 0);
        $this->RegisterPropertyInteger('HolidayIndicatorID', 0);
        $this->RegisterPropertyInteger('DayUsedWhenHoliday', 0);
        $this->RegisterPropertyInteger('WakeUpTimeID', 0);
        $this->RegisterPropertyInteger('WakeUpTimeOffset', 0);
        $this->RegisterPropertyInteger('BedTimeID', 0);
        $this->RegisterPropertyInteger('BedTimeOffset', 0);
        $this->RegisterPropertyBoolean(self::PROP_ACTIVATEDINDIVIDUALDAYLEVELS, false);
        $this->RegisterPropertyFloat(self::PROP_DAYBLINDLEVEL, 0);
        $this->RegisterPropertyFloat(self::PROP_DAYSLATSLEVEL, 0);
        $this->RegisterPropertyBoolean(self::PROP_ACTIVATEDINDIVIDUALNIGHTLEVELS, false);
        $this->RegisterPropertyFloat(self::PROP_NIGHTBLINDLEVEL, 0);
        $this->RegisterPropertyFloat(self::PROP_NIGHTSLATSLEVEL, 0);

        //day detection
        $this->RegisterPropertyInteger('IsDayIndicatorID', 0);
        $this->RegisterPropertyInteger('BrightnessID', 0);
        $this->RegisterPropertyInteger('BrightnessAvgMinutes', 0);
        $this->RegisterPropertyInteger('BrightnessThresholdID', 0);

        //overruling day times
        $this->RegisterPropertyInteger('DayStartID', 0);
        $this->RegisterPropertyInteger('DayEndID', 0);

        //contacts open
        $this->RegisterPropertyInteger(self::PROP_CONTACTOPEN1ID, 0);
        $this->RegisterPropertyInteger(self::PROP_CONTACTOPEN2ID, 0);
        $this->RegisterPropertyFloat(self::PROP_CONTACTOPENLEVEL1, 0);
        $this->RegisterPropertyFloat(self::PROP_CONTACTOPENLEVEL2, 0);
        $this->RegisterPropertyFloat(self::PROP_CONTACTOPENSLATSLEVEL1, 0);
        $this->RegisterPropertyFloat(self::PROP_CONTACTOPENSLATSLEVEL2, 0);

        //contacts close
        $this->RegisterPropertyInteger(self::PROP_CONTACTCLOSE1ID, 0);
        $this->RegisterPropertyInteger(self::PROP_CONTACTCLOSE2ID, 0);
        $this->RegisterPropertyFloat(self::PROP_CONTACTCLOSELEVEL1, 0);
        $this->RegisterPropertyFloat(self::PROP_CONTACTCLOSELEVEL2, 0);
        $this->RegisterPropertyFloat(self::PROP_CONTACTCLOSESLATSLEVEL1, 0);
        $this->RegisterPropertyFloat(self::PROP_CONTACTCLOSESLATSLEVEL2, 0);
        $this->RegisterPropertyBoolean(self::PROP_CONTACTSTOCLOSEHAVEHIGHERPRIORITY, false);

        //emergency contact
        $this->RegisterPropertyInteger(self::PROP_EMERGENCYCONTACTID, 0);

        //shadowing according to sun position
        $this->RegisterPropertyInteger(self::PROP_ACTIVATORIDSHADOWINGBYSUNPOSITION, 0);
        $this->RegisterPropertyInteger(self::PROP_AZIMUTHID, 0);
        $this->RegisterPropertyInteger(self::PROP_ALTITUDEID, 0);
        $this->RegisterPropertyFloat(self::PROP_AZIMUTHFROM, 0);
        $this->RegisterPropertyFloat(self::PROP_AZIMUTHTO, 360);
        $this->RegisterPropertyFloat(self::PROP_ALTITUDEFROM, 0);
        $this->RegisterPropertyFloat(self::PROP_ALTITUDETO, 90);
        $this->RegisterPropertyInteger(self::PROP_BRIGHTNESSIDSHADOWINGBYSUNPOSITION, 0);
        $this->RegisterPropertyInteger('BrightnessAvgMinutesShadowingBySunPosition', 0);
        $this->RegisterPropertyInteger(self::PROP_BRIGHTNESSTHRESHOLDIDSHADOWINGBYSUNPOSITION, 0);
        $this->RegisterPropertyInteger('TemperatureIDShadowingBySunPosition', 0);
        $this->RegisterPropertyFloat('LowSunPositionAltitude', 0);
        $this->RegisterPropertyFloat('HighSunPositionAltitude', 0);
        $this->RegisterPropertyFloat(self::PROP_LOWSUNPOSITIONBLINDLEVEL, 0);
        $this->RegisterPropertyFloat(self::PROP_HIGHSUNPOSITIONBLINDLEVEL, 0);
        $this->RegisterPropertyFloat(self::PROP_LOWSUNPOSITIONSLATSLEVEL, 0);
        $this->RegisterPropertyFloat(self::PROP_HIGHSUNPOSITIONSLATSLEVEL, 0);

        //shadowing according to brightness
        $this->RegisterPropertyInteger('ActivatorIDShadowingBrightness', 0);
        $this->RegisterPropertyInteger('BrightnessIDShadowingBrightness', 0);
        $this->RegisterPropertyInteger('BrightnessAvgMinutesShadowingBrightness', 0);
        $this->RegisterPropertyInteger('ThresholdIDLessBrightness', 0);
        $this->RegisterPropertyFloat(self::PROP_BLINDLEVELLESSBRIGHTNESSSHADOWINGBRIGHTNESS, 0);
        $this->RegisterPropertyFloat(self::PROP_SLATSLEVELLESSBRIGHTNESSSHADOWINGBRIGHTNESS, 0);
        $this->RegisterPropertyInteger('ThresholdIDHighBrightness', 0);
        $this->RegisterPropertyFloat(self::PROP_BLINDLEVELHIGHBRIGHTNESSSHADOWINGBRIGHTNESS, 0);
        $this->RegisterPropertyFloat(self::PROP_SLATSLEVELHIGHBRIGHTNESSSHADOWINGBRIGHTNESS, 0);

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
            $this->ReadPropertyInteger(self::PROP_BLINDLEVELID),
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

            $this->ReadPropertyInteger(self::PROP_CONTACTOPEN1ID),
            $this->ReadPropertyInteger(self::PROP_CONTACTOPEN2ID),
            $this->ReadPropertyInteger(self::PROP_EMERGENCYCONTACTID),

            $this->ReadPropertyInteger(self::PROP_CONTACTCLOSE1ID),
            $this->ReadPropertyInteger(self::PROP_CONTACTCLOSE2ID),

            $this->ReadPropertyInteger(self::PROP_ACTIVATORIDSHADOWINGBYSUNPOSITION),
            $this->ReadPropertyInteger(self::PROP_AZIMUTHID),
            $this->ReadPropertyInteger(self::PROP_ALTITUDEID),
            $this->ReadPropertyInteger(self::PROP_BRIGHTNESSIDSHADOWINGBYSUNPOSITION),
            $this->ReadPropertyInteger(self::PROP_BRIGHTNESSTHRESHOLDIDSHADOWINGBYSUNPOSITION),
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
            'WeeklyTimeTableEventID'                               => $this->ReadPropertyInteger('WeeklyTimeTableEventID'),
            'HolidayIndicatorID'                                   => $this->ReadPropertyInteger('HolidayIndicatorID'),
            'BrightnessID'                                         => $this->ReadPropertyInteger('BrightnessID'),
            'BrightnessThresholdID'                                => $this->ReadPropertyInteger('BrightnessThresholdID'),
            'IsDayIndicatorID'                                     => $this->ReadPropertyInteger('IsDayIndicatorID'),
            self::PROP_CONTACTCLOSE1ID                             => $this->ReadPropertyInteger(self::PROP_CONTACTCLOSE1ID),
            self::PROP_CONTACTCLOSE2ID                             => $this->ReadPropertyInteger(self::PROP_CONTACTCLOSE2ID),
            self::PROP_CONTACTOPEN1ID                              => $this->ReadPropertyInteger(self::PROP_CONTACTOPEN1ID),
            self::PROP_CONTACTOPEN2ID                              => $this->ReadPropertyInteger(self::PROP_CONTACTOPEN2ID),
            self::PROP_EMERGENCYCONTACTID                          => $this->ReadPropertyInteger(self::PROP_EMERGENCYCONTACTID),
            self::PROP_ACTIVATORIDSHADOWINGBYSUNPOSITION           => $this->ReadPropertyInteger(self::PROP_ACTIVATORIDSHADOWINGBYSUNPOSITION),
            self::PROP_AZIMUTHID                                   => $this->ReadPropertyInteger(self::PROP_AZIMUTHID),
            self::PROP_ALTITUDEID                                  => $this->ReadPropertyInteger(self::PROP_ALTITUDEID),
            self::PROP_BRIGHTNESSIDSHADOWINGBYSUNPOSITION          => $this->ReadPropertyInteger(self::PROP_BRIGHTNESSIDSHADOWINGBYSUNPOSITION),
            self::PROP_BRIGHTNESSTHRESHOLDIDSHADOWINGBYSUNPOSITION => $this->ReadPropertyInteger(
                self::PROP_BRIGHTNESSTHRESHOLDIDSHADOWINGBYSUNPOSITION
            ),
            'TemperatureIDShadowingBySunPosition'                  => $this->ReadPropertyInteger('TemperatureIDShadowingBySunPosition'),
            'ActivatorIDShadowingBrightness'                       => $this->ReadPropertyInteger('ActivatorIDShadowingBrightness'),
            'BrightnessIDShadowingBrightness'                      => $this->ReadPropertyInteger('BrightnessIDShadowingBrightness'),
            'ThresholdIDHighBrightness'                            => $this->ReadPropertyInteger('ThresholdIDHighBrightness'),
            'ThresholdIDLessBrightness'                            => $this->ReadPropertyInteger('ThresholdIDLessBrightness')];

        foreach ($this->GetMessageList() as $senderId => $msgs) {
            foreach ($msgs as $msg) {
                $this->UnregisterMessage($senderId, $msg);
            }
        }

        foreach ($objectIDs as $propertyName => $id) {
            if ($id !== 0) {
                $objectType = IPS_GetObject($id)['ObjectType'];
                switch ($objectType) {
                    case OBJECTTYPE_EVENT:
                        $this->RegisterMessage($id, EM_UPDATE);
                        break;
                    case OBJECTTYPE_VARIABLE:
                        $this->RegisterMessage($id, VM_UPDATE);
                        break;
                    default:
                        trigger_error(
                            sprintf('Instance %s, Property %s: unknown ObjectType %s of id %s', $this->InstanceID, $propertyName, $objectType, $id)
                        );
                }
            }
        }
    }

    private function RegisterAttributes(): void
    {
        $this->RegisterAttributeInteger(self::ATTR_TIMESTAMP_AUTOMATIC, 0);
        $this->RegisterAttributeString(self::ATTR_MANUALMOVEMENT, json_encode(['timeStamp' => null, 'blindLevel' => null, 'slatsLevel' => null]));
        $this->RegisterAttributeInteger('AttrTimeStampIsDayChange', 0);
        $this->RegisterAttributeBoolean('AttrIsDay', false);
        $this->RegisterAttributeBoolean(self::ATTR_CONTACT_OPEN, false);
        $this->RegisterAttributeString(
            self::ATTR_LASTMOVE . self::PROP_BLINDLEVELID, json_encode(['timeStamp' => null, 'percentClose' => null, 'hint' => null])
        );

        $this->RegisterAttributeString(
            self::ATTR_LASTMOVE . self::PROP_SLATSLEVELID, json_encode(['timeStamp' => null, 'percentClose' => null, 'hint' => null])
        );
    }

    private function RegisterVariables(): void
    {
        $this->RegisterVariableBoolean(self::VAR_IDENT_ACTIVATED, 'Activated', '~Switch');
        $this->RegisterVariableString(self::VAR_IDENT_LAST_MESSAGE, 'Last Message');

        $this->EnableAction(self::VAR_IDENT_ACTIVATED);
    }

    private function SetInstanceStatusAndTimerEvent(): void
    {

        if ($ret = $this->checkVariableId(
            self::PROP_BLINDLEVELID, false, [VARIABLETYPE_INTEGER, VARIABLETYPE_FLOAT], self::STATUS_INST_BLIND_LEVEL_ID_IS_INVALID
        )) {
            $this->SetStatus($ret);
            return;
        }

        if (!$this->checkActionOfStatusVariable(self::PROP_BLINDLEVELID)) {
            $this->SetStatus(self::STATUS_INST_BLIND_LEVEL_ID_IS_INVALID);
            return;
        }

        if (!$this->checkEmulateStatusOfVariableAction(self::PROP_BLINDLEVELID)) {
            $this->SetStatus(self::STATUS_INST_BLIND_LEVEL_IS_EMULATED);
            return;
        }

        if ($ret = $this->checkVariableId(
            self::PROP_SLATSLEVELID, true, [VARIABLETYPE_INTEGER, VARIABLETYPE_FLOAT], self::STATUS_INST_SLATSLEVEL_ID_IS_INVALID
        )) {
            $this->SetStatus($ret);
            return;
        }

        if ($this->ReadPropertyInteger(self::PROP_SLATSLEVELID) !== 0) {
            if (!$this->checkActionOfStatusVariable(self::PROP_SLATSLEVELID)) {
                $this->SetStatus(self::STATUS_INST_SLATSLEVEL_ID_IS_INVALID);
                return;
            }
            if (!$this->checkEmulateStatusOfVariableAction(self::PROP_SLATSLEVELID)) {
                $this->SetStatus(self::STATUS_INST_SLATS_LEVEL_IS_EMULATED);
                return;
            }
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
            self::PROP_CONTACTOPEN1ID, true, [VARIABLETYPE_BOOLEAN, VARIABLETYPE_INTEGER, VARIABLETYPE_FLOAT],
            self::STATUS_INST_CONTACT1_ID_IS_INVALID
        )) {
            $this->SetStatus($ret);
            return;
        }

        if ($ret = $this->checkVariableId(
            self::PROP_CONTACTOPEN2ID, true, [VARIABLETYPE_BOOLEAN, VARIABLETYPE_INTEGER, VARIABLETYPE_FLOAT],
            self::STATUS_INST_CONTACT2_ID_IS_INVALID
        )) {
            $this->SetStatus($ret);
            return;
        }

        if ($ret = $this->checkVariableId(
            self::PROP_EMERGENCYCONTACTID, true, [VARIABLETYPE_BOOLEAN, VARIABLETYPE_INTEGER, VARIABLETYPE_FLOAT],
            self::STATUS_INST_EMERGENCY_CONTACT_ID_IS_INVALID
        )) {
            $this->SetStatus($ret);
            return;
        }

        if ($ret = $this->checkVariableId(
            self::PROP_ACTIVATORIDSHADOWINGBYSUNPOSITION, true, [VARIABLETYPE_BOOLEAN, VARIABLETYPE_INTEGER, VARIABLETYPE_FLOAT],
            self::STATUS_INST_ACTIVATORIDSHADOWINGBYSUNPOSITION_IS_INVALID
        )) {
            $this->SetStatus($ret);
            return;
        }

        if ($ret = $this->checkVariableId(
            self::PROP_AZIMUTHID, $this->ReadPropertyInteger(self::PROP_ACTIVATORIDSHADOWINGBYSUNPOSITION) === 0, [VARIABLETYPE_FLOAT],
            self::STATUS_INST_AZIMUTHID_IS_INVALID
        )) {
            $this->SetStatus($ret);
            return;
        }

        if ($ret = $this->checkVariableId(
            self::PROP_ALTITUDEID, $this->ReadPropertyInteger(self::PROP_ACTIVATORIDSHADOWINGBYSUNPOSITION) === 0, [VARIABLETYPE_FLOAT],
            self::STATUS_INST_ALTITUDEID_IS_INVALID
        )) {
            $this->SetStatus($ret);
            return;
        }

        if ($ret = $this->checkVariableId(
            self::PROP_BRIGHTNESSIDSHADOWINGBYSUNPOSITION, true, [VARIABLETYPE_INTEGER, VARIABLETYPE_FLOAT],
            self::STATUS_INST_BRIGTHNESSIDSHADOWINGBYSUNPOSITION_IS_INVALID
        )) {
            $this->SetStatus($ret);
            return;
        }

        if ($ret = $this->checkVariableId(
            self::PROP_BRIGHTNESSTHRESHOLDIDSHADOWINGBYSUNPOSITION, true, [VARIABLETYPE_INTEGER, VARIABLETYPE_FLOAT],
            self::STATUS_INST_BRIGHTNESSTHRESHOLDIDSHADOWINGBYSUNPOSITION_IS_INVALID
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

        $this->profileBlindLevel = $this->GetProfileInformation(self::PROP_BLINDLEVELID);
        if ($this->profileBlindLevel !== null) {
            $propertyBlindLevels = [
                self::PROP_DAYBLINDLEVEL,
                self::PROP_CONTACTOPENLEVEL1,
                self::PROP_CONTACTOPENLEVEL2,
                self::PROP_CONTACTCLOSELEVEL1,
                self::PROP_CONTACTCLOSELEVEL2,
                self::PROP_LOWSUNPOSITIONBLINDLEVEL,
                self::PROP_HIGHSUNPOSITIONBLINDLEVEL,
                self::PROP_BLINDLEVELLESSBRIGHTNESSSHADOWINGBRIGHTNESS,
                self::PROP_BLINDLEVELHIGHBRIGHTNESSSHADOWINGBRIGHTNESS,
                self::PROP_SLATSLEVELLESSBRIGHTNESSSHADOWINGBRIGHTNESS,
                self::PROP_SLATSLEVELHIGHBRIGHTNESSSHADOWINGBRIGHTNESS];

            if ($this->ReadPropertyBoolean(self::PROP_ACTIVATEDINDIVIDUALDAYLEVELS)) {
                $propertyBlindLevels[] = self::PROP_DAYBLINDLEVEL;
            }

            if ($this->ReadPropertyBoolean(self::PROP_ACTIVATEDINDIVIDUALNIGHTLEVELS)) {
                $propertyBlindLevels[] = self::PROP_NIGHTBLINDLEVEL;
            }

            foreach ($propertyBlindLevels as $propertyBlindLevel) {

                if ($ret = $this->checkRangeFloat(
                    $propertyBlindLevel, $this->profileBlindLevel['MinValue'], $this->profileBlindLevel['MaxValue'],
                    self::STATUS_INST_BLINDLEVEL_IS_OUT_OF_RANGE
                )) {
                    $this->SetStatus($ret);
                    return;
                }

            }
            if ($this->profileBlindLevel['MinValue'] === $this->profileBlindLevel['MaxValue']) {
                $this->SetStatus(self::STATUS_INST_BLINDLEVEL_ID_PROFILE_MIN_MAX_INVALID);
                return;
            }
        } else {
            $this->SetStatus(self::STATUS_INST_BLINDLEVEL_ID_PROFILE_NOT_SET);
            return;
        }

        if ($this->ReadPropertyInteger(self::PROP_SLATSLEVELID) !== 0) {
            $this->profileSlatsLevel = $this->GetProfileInformation(self::PROP_SLATSLEVELID);
            if ($this->profileSlatsLevel !== null) {
                $propertySlatsLevels = [
                    self::PROP_LOWSUNPOSITIONSLATSLEVEL,
                    self::PROP_HIGHSUNPOSITIONSLATSLEVEL,
                    self::PROP_CONTACTCLOSESLATSLEVEL1,
                    self::PROP_CONTACTCLOSESLATSLEVEL2,
                    self::PROP_CONTACTOPENSLATSLEVEL1,
                    self::PROP_CONTACTOPENSLATSLEVEL2];

                if ($this->ReadPropertyBoolean(self::PROP_ACTIVATEDINDIVIDUALDAYLEVELS)) {
                    $propertySlatsLevels[] = self::PROP_DAYSLATSLEVEL;
                }

                if ($this->ReadPropertyBoolean(self::PROP_ACTIVATEDINDIVIDUALNIGHTLEVELS)) {
                    $propertySlatsLevels[] = self::PROP_NIGHTSLATSLEVEL;
                }

                foreach ($propertySlatsLevels as $propertySlatsLevel) {

                    if ($ret = $this->checkRangeFloat(
                        $propertySlatsLevel, $this->profileSlatsLevel['MinValue'], $this->profileSlatsLevel['MaxValue'],
                        self::STATUS_INST_SLATSLEVEL_IS_OUT_OF_RANGE
                    )) {
                        $this->SetStatus($ret);
                        return;
                    }
                }
                if ($this->profileSlatsLevel['MinValue'] === $this->profileSlatsLevel['MaxValue']) {
                    $this->SetStatus(self::STATUS_INST_SLATSLEVEL_ID_PROFILE_MIN_MAX_INVALID);
                    return;
                }
            } else {
                $this->SetStatus(self::STATUS_INST_SLATSLEVEL_ID_PROFILE_NOT_SET);
                return;
            }
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

        if ($this->GetValue(self::VAR_IDENT_ACTIVATED)) {
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

    private function checkActionOfStatusVariable(string $proName): bool
    {
        $var = IPS_GetVariable($this->ReadPropertyInteger($proName));

        return ($var['VariableAction'] || $var['VariableCustomAction']) && ($var['VariableCustomProfile'] || $var['VariableProfile']);

    }

    private function checkEmulateStatusOfVariableAction(string $proName): bool
    {
        $var = IPS_GetVariable($this->ReadPropertyInteger($proName));

        if ($var['VariableAction'] !== 0) {
            $configuration = IPS_GetConfiguration($var['VariableAction']);
            if ($configuration !== false) {
                $arrConfiguration = json_decode($configuration, true);
                if (isset($arrConfiguration['EmulateStatus'])) {
                    return !$arrConfiguration['EmulateStatus'];
                }
            }
        }

        return true;
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

    private function checkRangeFloat(string $propName, float $min, float $max, int $errStatus): int
    {
        $value = $this->ReadPropertyFloat($propName);

        if ($value === 0) {
            return 0;
        }

        if ($value < $min || $value > $max) {
            $this->Logger_Err(sprintf('%s: Wert (%.2f) nicht im gültigen Bereich (%.2f - %.2f)', $propName, $value, $min, $max));
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

    private function getPositionsOfOpenBlindContact(): ?array
    {
        $contacts = [];

        if ($this->ReadPropertyInteger(self::PROP_CONTACTOPEN1ID) !== 0) {
            $contacts[self::PROP_CONTACTOPEN1ID] = [
                'id'         => $this->ReadPropertyInteger(self::PROP_CONTACTOPEN1ID),
                'blindlevel' => $this->ReadPropertyFloat(self::PROP_CONTACTOPENLEVEL1),
                'slatslevel' => $this->ReadPropertyFloat(self::PROP_CONTACTOPENSLATSLEVEL1)];
        }
        if ($this->ReadPropertyInteger(self::PROP_CONTACTOPEN2ID) !== 0) {
            $contacts[self::PROP_CONTACTOPEN2ID] = [
                'id'         => $this->ReadPropertyInteger(self::PROP_CONTACTOPEN2ID),
                'blindlevel' => $this->ReadPropertyFloat(self::PROP_CONTACTOPENLEVEL2),
                'slatslevel' => $this->ReadPropertyFloat(self::PROP_CONTACTOPENSLATSLEVEL2)];
        }

        // alle Kontakte prüfen ...
        $contactOpen    = null;
        $blindPositions = null;

        foreach ($contacts as $propName => $contact) {
            if ($this->isContactOpen($propName)) {
                $contactOpen = true;
                if (isset($blindPositions)) {
                    if ($this->profileBlindLevel['Reversed']) {
                        $blindPositions['BlindLevel'] = max($blindPositions['BlindLevel'], $contact['blindlevel']);
                    } else {
                        $blindPositions['BlindLevel'] = min($blindPositions['BlindLevel'], $contact['blindlevel']);
                    }
                    if ($this->profileSlatsLevel['Reversed']) {
                        $blindPositions['SlatsLevel'] = max($blindPositions['SlatsLevel'], $contact['slatslevel']);
                    } else {
                        $blindPositions['SlatsLevel'] = min($blindPositions['SlatsLevel'], $contact['slatslevel']);
                    }
                } else {
                    $blindPositions['BlindLevel'] = $contact['blindlevel'];
                    $blindPositions['SlatsLevel'] = $contact['slatslevel'];
                }

                $this->Logger_Dbg(
                    __FUNCTION__, sprintf(
                                    'contact is open: #%s, value: %s, blindlevel: %s, slatslevel: %s', $contact['id'],
                                    $this->GetFormattedValue($contact['id']), $contact['blindlevel'], $contact['slatslevel']
                                )
                );
            }
        }

        if ($contactOpen) {
            return $blindPositions;
        }

        return null;
    }

    private function getPositionsOfCloseBlindContact(): ?array
    {
        $contacts = [];

        if ($this->ReadPropertyInteger(self::PROP_CONTACTCLOSE1ID) !== 0) {
            $contacts[self::PROP_CONTACTCLOSE1ID] = [
                'id'         => $this->ReadPropertyInteger(self::PROP_CONTACTCLOSE1ID),
                'blindlevel' => $this->ReadPropertyFloat(self::PROP_CONTACTCLOSELEVEL1),
                'slatslevel' => $this->ReadPropertyFloat(self::PROP_CONTACTCLOSESLATSLEVEL1)];
        }
        if ($this->ReadPropertyInteger(self::PROP_CONTACTCLOSE2ID) !== 0) {
            $contacts[self::PROP_CONTACTCLOSE2ID] = [
                'id'         => $this->ReadPropertyInteger(self::PROP_CONTACTCLOSE2ID),
                'blindlevel' => $this->ReadPropertyFloat(self::PROP_CONTACTCLOSELEVEL2),
                'slatslevel' => $this->ReadPropertyFloat(self::PROP_CONTACTCLOSESLATSLEVEL2)];
        }

        // alle Kontakte prüfen ...
        $contactOpen    = null;
        $blindPositions = null;

        foreach ($contacts as $propName => $contact) {
            if ($this->isContactOpen($propName)) {
                $contactOpen = true;
                if (isset($blindPositions)) {
                    if ($this->profileBlindLevel['Reversed']) {
                        $blindPositions['BlindLevel'] = min($blindPositions['BlindLevel'], $contact['blindlevel']);
                    } else {
                        $blindPositions['BlindLevel'] = max($blindPositions['BlindLevel'], $contact['blindlevel']);
                    }
                    if ($this->profileSlatsLevel['Reversed']) {
                        $blindPositions['SlatsLevel'] = min($blindPositions['SlatsLevel'], $contact['slatslevel']);
                    } else {
                        $blindPositions['SlatsLevel'] = max($blindPositions['SlatsLevel'], $contact['slatslevel']);
                    }
                } else {
                    $blindPositions['BlindLevel'] = $contact['blindlevel'];
                    $blindPositions['SlatsLevel'] = $contact['slatslevel'];
                }

                $this->Logger_Dbg(
                    __FUNCTION__, sprintf(
                                    'contact is open: #%s, value: %s, blindlevel: %s, slatslevel: %s', $contact['id'],
                                    $this->GetFormattedValue($contact['id']), $contact['blindlevel'], $contact['slatslevel']
                                )
                );
            }
        }

        if ($contactOpen) {
            return $blindPositions;
        }

        return null;
    }

    private function isContactOpen(string $propName): bool
    {
        $contactId = $this->ReadPropertyInteger($propName);
        if ($prof = $this->GetProfileInformation($propName)) {
            $reversed = $prof['Reversed'];
        } else {
            $reversed = false;
        }

        if ($contactId !== 0) {
            return GetValue($contactId) || ($reversed && !GetValue($contactId));
        }

        return false;

    }

    private function getLevelEmergencyContact(): ?float
    {
        $contacts = [];

        if ($this->ReadPropertyInteger(self::PROP_EMERGENCYCONTACTID) !== 0) {
            $contacts[self::PROP_EMERGENCYCONTACTID] = [
                'id'    => $this->ReadPropertyInteger(self::PROP_EMERGENCYCONTACTID),
                'level' => $this->profileBlindLevel['LevelOpened']];
        }

        // alle Kontakte prüfen ...
        $contactOpen = null;
        $level       = null;
        foreach ($contacts as $propName => $contact) {
            if ($this->isContactOpen($propName)) {
                $contactOpen = true;
                if (isset($level)) {
                    if ($this->profileBlindLevel['Reversed']) {
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

    private function getPositionsOfShadowingBySunPosition(float $levelAct): ?array
    {

        $activatorID = $this->ReadPropertyInteger(self::PROP_ACTIVATORIDSHADOWINGBYSUNPOSITION);

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

        $brightness =
            $this->GetBrightness(self::PROP_BRIGHTNESSIDSHADOWINGBYSUNPOSITION, 'BrightnessAvgMinutesShadowingBySunPosition', $levelAct, true);
        if ($brightness) {
            $thresholdBrightness = $this->getBrightnessThreshold(
                $this->ReadPropertyInteger(self::PROP_BRIGHTNESSTHRESHOLDIDSHADOWINGBYSUNPOSITION), $levelAct, $temperature
            );
        } else {
            $thresholdBrightness = 0;
        }

        $rSunAzimuth = GetValueFloat($this->ReadPropertyInteger(self::PROP_AZIMUTHID));
        $azimuthFrom = $this->ReadPropertyFloat(self::PROP_AZIMUTHFROM);
        $azimuthTo   = $this->ReadPropertyFloat(self::PROP_AZIMUTHTO);

        $rSunAltitude = GetValueFloat($this->ReadPropertyInteger(self::PROP_ALTITUDEID));
        $altitudeFrom = $this->ReadPropertyFloat(self::PROP_ALTITUDEFROM);
        $altitudeTo   = $this->ReadPropertyFloat(self::PROP_ALTITUDETO);

        /** @noinspection ProperNullCoalescingOperatorUsageInspection */
        $this->Logger_Dbg(
            __FUNCTION__, sprintf(
                            'active: %d, brightness: %.1f/%.1f, azimuth: %.1f (%.1f - %.1f), altitude: %.1f (%.1f - %.1f), temperature: %s',
                            (int) GetValue($activatorID), $brightness, $thresholdBrightness, $rSunAzimuth, $azimuthFrom, $azimuthTo, $rSunAltitude,
                            $altitudeFrom, $altitudeTo, $temperature ?? 'null'
                        )
        );

        $positions = null;
        if (($brightness >= $thresholdBrightness) && ($rSunAzimuth >= $azimuthFrom) && ($rSunAzimuth <= $azimuthTo)
            && ($rSunAltitude >= $altitudeFrom)
            && ($rSunAltitude <= $altitudeTo)) {

            $positions = $this->getBlindPositionsFromSunPosition();

            $this->Logger_Dbg(
                __FUNCTION__, sprintf(
                                'BlindLevelFromSunPosition: %.2f, SlatsLevelFromSunPosition: %.2f', $positions['BlindLevel'], $positions['SlatsLevel']
                            )
            );

            //wenn zusätzlicher *Hitzeschutz* notwenig oder bereits eingeschaltet und Hysterese nicht unterschritten
            if ($this->profileBlindLevel['Reversed']) {
                $levelPositionHeat = round(0.10 * ($this->profileBlindLevel['LevelOpened'] - $this->profileBlindLevel['LevelClosed']), 2);
            } else {
                $levelPositionHeat = round(0.90 * ($this->profileBlindLevel['LevelClosed'] - $this->profileBlindLevel['LevelOpened']), 2);
            }

            if (($temperature > 30.0) || ((round($levelAct, 1) === round($levelPositionHeat, 1)) && ($temperature > (30.0 - 0.5)))) {
                $positions['BlindLevel'] = $levelPositionHeat;
                $this->Logger_Dbg(__FUNCTION__, sprintf('Temp gt 30°, levelAct: %.2f, level: %.2f', $levelAct, $positions['BlindLevel']));
                return $positions;
            }

            //wenn zusätlicher *Wärmeschutz* notwendig oder bereits eingeschaltet und Hysterese nicht unterschritten
            if ($this->profileBlindLevel['Reversed']) {
                $levelCorrectionHeat = -round(0.15 * ($this->profileBlindLevel['LevelOpened'] - $this->profileBlindLevel['LevelClosed']), 2);
            } else {
                $levelCorrectionHeat = round(0.15 * ($this->profileBlindLevel['LevelClosed'] - $this->profileBlindLevel['LevelOpened']), 2);
            }

            if (($temperature > 27.0)
                || ((round($levelAct, 1) === round($positions['BlindLevel'] + $levelCorrectionHeat, 1))
                    && ($temperature > (27.0 - 0.5)))) {
                $positions['BlindLevel'] += $levelCorrectionHeat;
                $this->Logger_Dbg(
                    __FUNCTION__, sprintf(
                                    'Temp gt 27°, levelAct: %.2f, level: %.2f, levelCorrectionHeat: %.2f', $levelAct, $positions['BlindLevel'],
                                    $levelCorrectionHeat
                                )
                );
                return $positions;
            }

        }

        return $positions;
    }

    private function GetBrightness(string $propBrightnessID, string $propBrightnessAvgMinutes, float $levelAct, bool $shadowing): ?float
    {
        $brightnessID = $this->ReadPropertyInteger($propBrightnessID);
        if ($brightnessID === 0) {
            return null;
        }
        $brightnessAvgMinutes = $this->ReadPropertyInteger($propBrightnessAvgMinutes);

        $brightness = (float) GetValue($brightnessID);

        if ($brightnessAvgMinutes > 0) {
            $archiveId = IPS_GetInstanceListByModuleID('{43192F0B-135B-4CE7-A0A7-1475603F3060}')[0];
            if (AC_GetLoggingStatus($archiveId, $brightnessID)) {
                $werte = AC_GetAggregatedValues($archiveId, $brightnessID, 6, strtotime('-' . $brightnessAvgMinutes . ' minutes'), time(), 0);
                $sum   = 0;
                foreach ($werte as $wert) {
                    $sum += $wert['Avg'];
                }
                $brightnessAvg = round($sum / count($werte), 2);

                if ($shadowing) {
                    //aktuellen Helligkeitswert berücksichtigen
                    //prüfen, ob der Rollladen (teilweise) herabgefahren ist. Wenn ja, dann max, sonst min der beiden Helligkeitswerte
                    if ($this->profileBlindLevel['Reversed']) {
                        if ($levelAct < $this->profileBlindLevel['LevelOpened']) {
                            $brightnessAvg = max($brightnessAvg, $brightness);
                        } else {
                            $brightnessAvg = min($brightnessAvg, $brightness);
                        }
                    } elseif ($levelAct > $this->profileBlindLevel['LevelOpened']) {
                        $brightnessAvg = max($brightnessAvg, $brightness);
                    } else {
                        $brightnessAvg = min($brightnessAvg, $brightness);
                    }
                }

                return $brightnessAvg;
            }
        }

        return (float) GetValue($brightnessID);
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
            elseif ($temperature < 10) {
                $thresholdBrightness += (10 - $temperature) * 0.10 * $thresholdBrightness;
            }
        }

        //Hysterese berücksichtigen
        //der Rollladen ist (teilweise) herabgefahren
        if ($this->profileBlindLevel['Reversed']) {
            if ($levelAct < $this->profileBlindLevel['LevelOpened']) {
                $thresholdBrightness -= $iBrightnessHysteresis;
            }
        } elseif ($levelAct > $this->profileBlindLevel['LevelOpened']) {
            $thresholdBrightness -= $iBrightnessHysteresis;
        }

        return $thresholdBrightness;
    }

    private function getBlindPositionsFromSunPosition(): array
    {
        $blindPositions = null;

        $rSunAltitude = GetValueFloat($this->ReadPropertyInteger('AltitudeID'));

        $blindLevelLow  = $this->ReadPropertyFloat(self::PROP_LOWSUNPOSITIONBLINDLEVEL);
        $blindLevelHigh = $this->ReadPropertyFloat(self::PROP_HIGHSUNPOSITIONBLINDLEVEL);

        $blindPositions['BlindLevel'] = $this->calcPosition($blindLevelLow, $blindLevelHigh, $rSunAltitude);

        if ($this->profileBlindLevel['Reversed']) {
            $blindPositions['BlindLevel'] = min($blindPositions['BlindLevel'], $this->profileBlindLevel['LevelOpened']);
            $blindPositions['BlindLevel'] = max($blindPositions['BlindLevel'], $this->profileBlindLevel['LevelClosed']);
        } else {
            $blindPositions['BlindLevel'] = max($blindPositions['BlindLevel'], $this->profileBlindLevel['LevelOpened']);
            $blindPositions['BlindLevel'] = min($blindPositions['BlindLevel'], $this->profileBlindLevel['LevelClosed']);
        }

        $blindPositions['SlatsLevel'] = $this->calcPosition(
            $this->ReadPropertyFloat(self::PROP_LOWSUNPOSITIONSLATSLEVEL), $this->ReadPropertyFloat(self::PROP_HIGHSUNPOSITIONSLATSLEVEL),
            $rSunAltitude
        );

        return $blindPositions;
    }

    private function calcPosition(float $lowPosition, float $highPosition, float $sunAltitude): float
    {
        $AltitudeLow  = $this->ReadPropertyFloat('LowSunPositionAltitude');
        $AltitudeHigh = $this->ReadPropertyFloat('HighSunPositionAltitude');
        if ($AltitudeLow !== $AltitudeHigh) {
            $rAltitudeTanLow  = tan($AltitudeLow * M_PI / 180);
            $rAltitudeTanHigh = tan($AltitudeHigh * M_PI / 180);
            $rAltitudeTanAct  = tan($sunAltitude * M_PI / 180);

            return $lowPosition + ($highPosition - $lowPosition) * ($rAltitudeTanAct - $rAltitudeTanLow) / ($rAltitudeTanHigh - $rAltitudeTanLow);
        }
        return $lowPosition;
    }

    private function getPositionsOfShadowingByBrightness(float $levelAct): ?array
    {
        $activatorID = $this->ReadPropertyInteger('ActivatorIDShadowingBrightness');

        if (($activatorID === 0) || !GetValue($activatorID)) {
            // keine Beschattung bei Helligkeit gewünscht bzw. nicht notwendig
            return null;
        }

        $brightnessID = $this->ReadPropertyInteger('BrightnessIDShadowingBrightness');
        if ($brightnessID === 0) {
            trigger_error(sprintf('Instance %s: BrightnessIDShadowingBrightness === 0', $this->InstanceID));
            return null;
        }

        $thresholdIDHighBrightness = $this->ReadPropertyInteger('ThresholdIDHighBrightness');
        $thresholdIDLessBrightness = $this->ReadPropertyInteger('ThresholdIDLessBrightness');
        if ($thresholdIDHighBrightness === 0 && $thresholdIDLessBrightness === 0) {
            trigger_error(sprintf('Instance %s: ThresholdIDHighBrightness === 0 and ThresholdIDLowBrightness === 0', $this->InstanceID));
            return null;
        }

        $positions  = null;
        $brightness = $this->GetBrightness('BrightnessIDShadowingBrightness', 'BrightnessAvgMinutesShadowingBrightness', $levelAct, true);

        if ($thresholdIDHighBrightness > 0) {
            $thresholdLessBrightness = GetValue($thresholdIDHighBrightness);
            if ($brightness > $thresholdLessBrightness) {
                $positions['BlindLevel'] = $this->ReadPropertyFloat(self::PROP_BLINDLEVELHIGHBRIGHTNESSSHADOWINGBRIGHTNESS);
                $positions['SlatsLevel'] = $this->ReadPropertyFloat(self::PROP_SLATSLEVELHIGHBRIGHTNESSSHADOWINGBRIGHTNESS);
                $this->Logger_Dbg(
                    __FUNCTION__, sprintf(
                                    'Beschattung bei hoher Helligkeit (%s/%s): BlindLevel: %s, SlatsLevel: %s', $brightness, $thresholdLessBrightness,
                                    $positions['BlindLevel'], $positions['SlatsLevel']
                                )
                );
                return $positions;
            }
        }

        if ($thresholdIDLessBrightness > 0) {
            $thresholdBrightness = GetValue($thresholdIDLessBrightness);
            if ($brightness > $thresholdBrightness) {
                $positions['BlindLevel'] = $this->ReadPropertyFloat(self::PROP_BLINDLEVELLESSBRIGHTNESSSHADOWINGBRIGHTNESS);
                $positions['SlatsLevel'] = $this->ReadPropertyFloat(self::PROP_SLATSLEVELLESSBRIGHTNESSSHADOWINGBRIGHTNESS);
                $this->Logger_Dbg(
                    __FUNCTION__, sprintf(
                                    'Beschattung bei niedriger Helligkeit (%s/%s): BlindLevel: %s, SlatsLevel: %s', $brightness, $thresholdBrightness,
                                    $positions['BlindLevel'], $positions['SlatsLevel']
                                )
                );

                return $positions;
            }
        }
        return null;
    }

    private function isMovementLocked($blindLevelAct, $slatsLevelAct, int $tsBlindLastMovement, bool $isDay, int $tsIsDayChanged, int $tsAutomatik,
                                      float $blindLevelClosed, float $blindLevelOpened, ?float $slatsLevelClosed, ?float $slatsLevelOpened): bool
    {

        $this->Logger_Dbg(
            __FUNCTION__, sprintf(
                            'Parameter: blindLevelAct: %s, slatsLevelAct: %s, tsBlindLastMovement: %s, isDay: %s, tsIsDayChanged: %s, tsAutomatik: %s, blindLevelClosed: %s, blindLevelOpened: %s, slatsLevelClosed: %s, slatsLevelOpened: %s',
                            $blindLevelAct, $slatsLevelAct ?? 'null', $this->FormatTimeStamp($tsBlindLastMovement), (int) $isDay,
                            $this->FormatTimeStamp($tsIsDayChanged), $this->FormatTimeStamp($tsAutomatik), $blindLevelClosed, $blindLevelOpened,
                            $slatsLevelClosed ?? 'null', $slatsLevelOpened ?? 'null'
                        )
        ); //todo: sollte wieder entfernt werden

        //zuerst prüfen, ob der Rollladen nach der letzten aut. Bewegung manuell bewegt wurde
        if ($tsBlindLastMovement <= $tsAutomatik) {
            return false;
        }

        $deactivationTimeManuSecs = $this->ReadPropertyInteger('DeactivationManualMovement') * 60;

        //Zeitpunkt festhalten, sofern noch nicht geschehen
        if ($tsBlindLastMovement !== json_decode($this->ReadAttributeString(self::ATTR_MANUALMOVEMENT), true)['timeStamp']) {
            $this->WriteAttributeString(
                self::ATTR_MANUALMOVEMENT, json_encode(
                                             ['timeStamp' => $tsBlindLastMovement, 'blindLevel' => $blindLevelAct, 'slatsLevel' => $slatsLevelAct]
                                         )
            );

            if ($slatsLevelAct === null) {
                $txtSlatsLevelAct = 'null';
            } else {
                $txtSlatsLevelAct = sprintf('%.2f', $slatsLevelAct);
            }

            $this->Logger_Dbg(
                __FUNCTION__, sprintf(
                                'Rollladen wurde manuell gesetzt. blindLevelAct: %.2f, slatsLevelAct: %s, TimestampAutomatic: %s, TimestampManual: %s, deactivationTimeManuSecs: %s/%s',
                                $blindLevelAct, $txtSlatsLevelAct, $this->FormatTimeStamp($tsAutomatik),
                                $this->FormatTimeStamp(json_decode($this->ReadAttributeString(self::ATTR_MANUALMOVEMENT), true)['timeStamp']),
                                time() - $tsBlindLastMovement, $deactivationTimeManuSecs
                            )
            );

            if ($slatsLevelAct === null) {
                if ($blindLevelAct === $blindLevelClosed) {
                    $this->Logger_Inf(sprintf('\'%s\' wurde manuell geschlossen.', $this->objectName));
                } elseif ($blindLevelAct === $blindLevelOpened) {
                    $this->Logger_Inf(sprintf('\'%s\' wurde manuell geöffnet.', $this->objectName));
                } else {
                    $blindLevelPercent = ($blindLevelAct - $this->profileBlindLevel['MinValue']) / ($this->profileBlindLevel['MaxValue']
                                                                                                    - $this->profileBlindLevel['MinValue']);

                    $this->Logger_Inf(sprintf('\'%s\' wurde manuell auf %.0f%% gefahren.', $this->objectName, 100 * $blindLevelPercent));
                }
            } else {
                /** @noinspection NestedPositiveIfStatementsInspection */
                if (($blindLevelAct === $blindLevelClosed) && ($slatsLevelAct === $slatsLevelClosed)) {
                    $this->Logger_Inf(sprintf('\'%s\' wurde manuell geschlossen.', $this->objectName));
                } elseif (($blindLevelAct === $blindLevelOpened) && ($slatsLevelAct === $slatsLevelOpened)) {
                    $this->Logger_Inf(sprintf('\'%s\' wurde manuell geöffnet.', $this->objectName));
                } else {
                    $blindLevelPercent = ($blindLevelAct - $this->profileBlindLevel['MinValue']) / ($this->profileBlindLevel['MaxValue']
                                                                                                    - $this->profileBlindLevel['MinValue']);
                    $slatsLevelPercent = ($slatsLevelAct - $this->profileSlatsLevel['MinValue']) / ($this->profileSlatsLevel['MaxValue']
                                                                                                    - $this->profileSlatsLevel['MinValue']);

                    $this->Logger_Inf(
                        sprintf(
                            '\'%s\' wurde manuell auf %.0f%%(Höhe), %.0f%%(Lamellen) gefahren.', $this->objectName, 100 * $blindLevelPercent,
                            100 * $slatsLevelPercent
                        )
                    );
                }

            }

        }

        $bNoMove          = false;
        $tsManualMovement = json_decode($this->ReadAttributeString(self::ATTR_MANUALMOVEMENT), true)['timeStamp'];

        if ($isDay && ($tsManualMovement > $tsIsDayChanged)) {
            //tagsüber gilt:

            // der Rollladen ist nicht bereits manuell geschlossen worden
            if (($blindLevelAct === $blindLevelClosed) && ($slatsLevelAct === $slatsLevelClosed)) {
                $bNoMove = true;
            } else {
                $bNoMove =
                    ($deactivationTimeManuSecs === 0) || (strtotime('+ ' . $deactivationTimeManuSecs . ' seconds', $tsManualMovement) > time());
            }

            if ($bNoMove) {
                $this->Logger_Dbg(
                    __FUNCTION__, sprintf(
                                    'Rollladen wurde manuell bewegt (Tag: %s). DeactivationTimeManu: %s/%s', date('H:i:s', $tsManualMovement),
                                    time() - $tsBlindLastMovement, $deactivationTimeManuSecs
                                )
                );
            } elseif ($tsManualMovement !== null) {
                $this->resetManualMovement();
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
    public function MoveBlind(int $percentBlindClose, ?int $percentSlatsClosed, int $deactivationTimeAuto, string $hint): bool
    {

        if (IPS_GetInstance($this->InstanceID)['InstanceStatus'] !== IS_ACTIVE) {
            return false;
        }

        $this->Logger_Dbg(
            __FUNCTION__, sprintf(
                            'percentBlindClose: %s, percentSlatClose: %s, deactivationTimeAuto: %s, hint: %s', $percentBlindClose,
                            $percentSlatsClosed ?? 'null', $deactivationTimeAuto, $hint
                        )
        );

        if (($percentBlindClose < 0) || ($percentBlindClose > 100) || ($percentSlatsClosed < 0) || ($percentSlatsClosed > 100)) {
            return false;
        }

        // globale Instanzvariablen setzen
        $this->objectName        = IPS_GetObject($this->InstanceID)['ObjectName'];
        $this->profileBlindLevel = $this->GetProfileInformation(self::PROP_BLINDLEVELID);

        if ($this->profileBlindLevel === null) {
            return false;
        }

        $moveBladeOk = $this->MoveToPosition(self::PROP_BLINDLEVELID, $percentBlindClose, $deactivationTimeAuto, $hint);

        //gibt es Lamellen?
        if ($this->ReadPropertyInteger(self::PROP_SLATSLEVELID) !== 0) {

            $this->profileSlatsLevel = $this->GetProfileInformation(self::PROP_SLATSLEVELID);
            $moveSlatsOk             = $this->MoveToPosition(self::PROP_SLATSLEVELID, $percentSlatsClosed, $deactivationTimeAuto, $hint);

            return $moveBladeOk || $moveSlatsOk;
        }

        return $moveBladeOk;
    }

    private function MoveToPosition(string $propName, int $percentClose, int $deactivationTimeAuto, $hint): bool
    {

        $positionID = $this->ReadPropertyInteger($propName);
        if ($positionID === 0) {
            return false;
        }

        $profile = $this->GetProfileInformation($propName);
        if ($profile === null) {
            return false;
        }

        $lastMove = json_decode($this->ReadAttributeString(self::ATTR_LASTMOVE . $propName), true);

        if (((int) $lastMove['percentClose'] === $percentClose) && ($lastMove['timeStamp'] > strtotime('-40 secs'))) {
            //dieselbe Bewegung in den letzten 40 Sekunden
            $this->Logger_Dbg(
                __FUNCTION__, sprintf(
                                '#%s(%s): Move ignored! Same percentClose %s just %s s before', $positionID, $propName, $percentClose,
                                time() - $lastMove['timeStamp']
                            )
            );
            // Timestamp der Automatik merken (sonst wird die Bewegung später als manuelle Bewegung erkannt)
            $this->WriteAttributeInteger(self::ATTR_TIMESTAMP_AUTOMATIC, time());

            return false;
        }

        $this->Logger_Dbg(
            __FUNCTION__, sprintf('#%s(%s): percentClose %s%% after %s s', $positionID, $propName, $percentClose, time() - $lastMove['timeStamp'])
        );

        $positionNew = $profile['MinValue'] + ($percentClose / 100) * ($profile['MaxValue'] - $profile['MinValue']);

        if ($profile['Reversed']) {
            $positionNew = $profile['MaxValue'] - $positionNew;
        }

        $positionAct            = GetValue($positionID); //integer and float are supported
        $positionDiffPercentage = abs($positionNew - $positionAct) / ($profile['MaxValue'] - $profile['MinValue']);
        $timeDiffAuto           = time() - $this->ReadAttributeInteger(self::ATTR_TIMESTAMP_AUTOMATIC);

        $this->Logger_Dbg(
            __FUNCTION__, sprintf(
                            '#%s(%s): positionAct: %s, positionNew: %s, positionDiffPercentage: %.2f/0,05, timeDiffAuto: %s/%s', $positionID,
                            $propName, $positionAct, $positionNew, $positionDiffPercentage, $timeDiffAuto, $deactivationTimeAuto
                        )
        );

        $ret = false;

        // Wenn sich die aktuelle Position um mehr als 5% von neuer Position unterscheidet
        if (($positionDiffPercentage > 0.05) && ($timeDiffAuto >= $deactivationTimeAuto)) {

            //Position setzen
            //Wert übertragen
            if (@RequestAction($positionID, $positionNew)) {

                // warten, bis die Zielposition erreicht ist
                $ret = $this->waitUntilBlindLevelIsReached($propName, $positionNew);

                // Timestamp der Automatik merken (sonst wird die Bewegung später als manuelle Bewegung erkannt)
                $this->WriteAttributeInteger(self::ATTR_TIMESTAMP_AUTOMATIC, time());
                $this->WriteAttributeString(
                    self::ATTR_LASTMOVE . $propName, json_encode(['timeStamp' => time(), 'percentClose' => $percentClose, 'hint' => $hint])
                );
                $this->Logger_Dbg(
                    __FUNCTION__, "$this->objectName: TimestampAutomatik: " . $this->FormatTimeStamp(
                                    $this->ReadAttributeInteger(self::ATTR_TIMESTAMP_AUTOMATIC)
                                )
                );

            } else {
                $this->Logger_Err(sprintf('%s(%s): Fehler beim Setzen der Werte. (Value = %s)', $positionID, $propName, $percentClose));
            }
            $this->Logger_Dbg(__FUNCTION__, sprintf('#%s(%s): %s to %s', $positionID, $propName, $positionAct, $positionNew));

        } elseif (!$positionDiffPercentage) {
            $this->Logger_Dbg(__FUNCTION__, sprintf('#%s(%s): No Movement! Position %s already reached.', $positionID, $propName, $positionAct));

        } elseif ($positionDiffPercentage <= 0.05) {
            $this->Logger_Dbg(
                __FUNCTION__, sprintf('#%s(%s): No Movement! Movement less than 5 percent (%.2f).', $positionID, $propName, $positionDiffPercentage)
            );
        } else {
            $this->Logger_Dbg(
                __FUNCTION__, sprintf(
                                '#%s(%s): No Movement! DeactivationTimeAuto of %s not reached (%s).', $positionID, $propName, $deactivationTimeAuto,
                                $timeDiffAuto
                            )
            );
        }

        if ($ret) {
            $this->WriteInfo($propName, $positionNew, $hint);
        }

        return $ret;
    }

    private function waitUntilBlindLevelIsReached(string $propName, $positionNew): bool
    {
        $levelID = $this->ReadPropertyInteger($propName);

        $profile         = $this->GetProfileInformation($propName);
        $percentCloseNew = ($positionNew - $profile['MinValue']) / ($profile['MaxValue'] - $profile['MinValue']) * 100;
        if ($profile['Reversed']) {
            $percentCloseNew = 100 - $percentCloseNew;
        }

        for ($i = 0; $i < 90; $i++) { //wir warten maximal 90 Sekunden
            $currentValue = GetValue($levelID);
            $percentCloseCurrent = ($currentValue - $profile['MinValue']) / ($profile['MaxValue'] - $profile['MinValue']) * 100;

            if ($profile['Reversed']) {
                $percentCloseCurrent = 100 - $percentCloseCurrent;
            }

            if (abs($percentCloseNew - $percentCloseCurrent) > 5) {
                set_time_limit(30);
                sleep(1);
            } else {
                $this->Logger_Dbg(
                    __FUNCTION__, sprintf('#%s(%s): Position reached (Value: %s, Diff: %.2f).', $levelID, $propName, $currentValue, $percentCloseNew - $percentCloseCurrent)
                );
                return true;
            }
        }

        $percentCloseCurrent = (GetValue($levelID) - $profile['MinValue']) / ($profile['MaxValue'] - $profile['MinValue']) * 100;

        if ($profile['Reversed']) {
            $percentCloseCurrent = 100 - $percentCloseCurrent;
        }
        $this->Logger_Inf(sprintf('#%s(%s): Position not reached! (Diff: %.2f).', $levelID, $propName, $percentCloseNew - $percentCloseCurrent));

        return false;
    }

    private function WriteInfo(string $propName, float $rLevelneu, string $hint): void
    {
        if ($propName === self::PROP_BLINDLEVELID) {
            if ($rLevelneu === (float) $this->profileBlindLevel['LevelClosed']) {
                $logMessage = sprintf('\'%s\' wurde geschlossen.', $this->objectName);
            } elseif ($rLevelneu === (float) $this->profileBlindLevel['LevelOpened']) {
                $logMessage = sprintf('\'%s\' wurde geöffnet.', $this->objectName);
            } else {
                $levelPercent = ($rLevelneu - $this->profileBlindLevel['MinValue']) / ($this->profileBlindLevel['MaxValue']
                                                                                       - $this->profileBlindLevel['MinValue']);
                $logMessage   = sprintf('\'%s\' wurde auf %.0f%% gefahren.', $this->objectName, 100 * $levelPercent);
            }
        } elseif ($rLevelneu === (float) $this->profileSlatsLevel['LevelClosed']) {
            $logMessage = sprintf('Die Lamellen \'%s\' wurden geschlossen.', $this->objectName);
        } elseif ($rLevelneu === (float) $this->profileSlatsLevel['LevelOpened']) {
            $logMessage = sprintf('Die Lamellen \'%s\' wurden geöffnet.', $this->objectName);
        } else {
            $levelPercent =
                ($rLevelneu - $this->profileSlatsLevel['MinValue']) / ($this->profileSlatsLevel['MaxValue'] - $this->profileSlatsLevel['MinValue']);
            $logMessage   = sprintf('Die Lamellen \'%s\' wurden auf %.0f%% gefahren.', $this->objectName, 100 * $levelPercent);
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

    private function getIsDayByDayDetection(&$brightness, float $levelAct): ?bool
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
        if ($this->ReadPropertyInteger('BrightnessID')) {
            $brightness = $this->GetBrightness('BrightnessID', 'BrightnessAvgMinutes', $levelAct, false);
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

        return ($heute_auf !== null) && (time() >= strtotime($heute_auf)) && ($heute_ab === null || (time() <= strtotime($heute_ab)));

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
            trigger_error(sprintf('Instance %s: wrong Event ID #%s', $this->InstanceID, $weeklyTimeTableEventId));
            return false;
        }

        if ($event['EventType'] !== EVENTTYPE_SCHEDULE) {
            trigger_error(sprintf('Instance %s: wrong Eventtype %s', $this->InstanceID, $event['EventType']));
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

    private function GetProfileInformation(string $propName): ?array
    {

        if (!($variable = @IPS_GetVariable($this->ReadPropertyInteger($propName)))) {
            return null;
        }

        if ($variable['VariableCustomProfile'] !== '') {
            $profileName = $variable['VariableCustomProfile'];
        } else {
            $profileName = $variable['VariableProfile'];
        }

        if ($profileName === '') {
            return null;
        }

        if ($profile = @IPS_GetVariableProfile($profileName)) {
            $profileNameParts = explode('.', $profileName);
        } else {
            return null;
        }

        $reversed = strcasecmp('reversed', end($profileNameParts)) === 0;
        switch ($propName) {
            case self::PROP_BLINDLEVELID:
            case self::PROP_SLATSLEVELID:
                return [
                    'Name'        => $profileName,
                    'ProfileType' => $profile['ProfileType'],
                    'MinValue'    => $profile['MinValue'],
                    'MaxValue'    => $profile['MaxValue'],
                    'Reversed'    => $reversed,
                    'LevelOpened' => $reversed ? (float) $profile['MaxValue'] : (float) $profile['MinValue'],
                    'LevelClosed' => $reversed ? (float) $profile['MinValue'] : (float) $profile['MaxValue']];
            case self::PROP_CONTACTCLOSE1ID:
            case self::PROP_CONTACTCLOSE2ID:
            case self::PROP_CONTACTOPEN1ID:
            case self::PROP_CONTACTOPEN2ID:
            case self::PROP_EMERGENCYCONTACTID:
                return [
                    'Name'        => $profileName,
                    'ProfileType' => $profile['ProfileType'],
                    'MinValue'    => $profile['MinValue'],
                    'MaxValue'    => $profile['MaxValue'],
                    'Reversed'    => $reversed];
            default:
                trigger_error('Unknown propName: ' . $propName);
        }

        return null;

    }

    //-----------------------------------------------
    private function GetBlindLastTimeStamp(int $idBlindLevel, int $idSlatsLevel): int
    {
        $tsBlindChanged = IPS_GetVariable($idBlindLevel)['VariableChanged'];

        if ($idSlatsLevel !== 0) {
            $tsBlindChanged = max($tsBlindChanged, IPS_GetVariable($idSlatsLevel)['VariableChanged']);
        }
        return $tsBlindChanged;
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
        } elseif (is_bool($val)) {
            if ($val) {
                $ret = 'true';
            } else {
                $ret = 'false';
            }
        } elseif (is_float($val) || is_int($val)) {
            $ret = (string) $val;
        } elseif (is_array($val)) {
            $ret = json_encode($val);
        } elseif (is_object($val) || is_scalar($val)) {
            $ret = serialize($val);
        } elseif ($val === null) {
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

        $this->SetValue(self::VAR_IDENT_LAST_MESSAGE, $message);
    }

    private function Logger_Inf(string $message): void
    {
        $this->SendDebug('LOG_INFO', $message, 0);
        if (function_exists('IPSLogger_Inf') && $this->ReadPropertyBoolean('WriteLogInformationToIPSLogger')) {
            IPSLogger_Inf(__CLASS__, $message);
        } else {
            $this->LogMessage($message, KL_NOTIFY);
        }

        $this->SetValue(self::VAR_IDENT_LAST_MESSAGE, $message);
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
