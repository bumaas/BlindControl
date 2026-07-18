<?php

declare(strict_types=1);

if (function_exists('IPSUtils_Include')) {
    IPSUtils_Include('IPSLogger.inc.php', 'IPSLibrary::app::core::IPSLogger');
}

/** @noinspection AutoloadingIssuesInspection */
class BlindController extends IPSModuleStrict
{
    //Status
    private const int STATUS_INST_TIMETABLE_ID_IS_INVALID                                = 201;
    private const int STATUS_INST_HOLYDAY_INDICATOR_ID_IS_INVALID                        = 202;
    private const int STATUS_INST_BLIND_LEVEL_ID_IS_INVALID                              = 203;
    private const int STATUS_INST_BRIGHTNESS_ID_IS_INVALID                               = 204;
    private const int STATUS_INST_BRIGHTNESS_THRESHOLD_ID_IS_INVALID                     = 205;
    private const int STATUS_INST_ISDAY_INDICATOR_ID_IS_INVALID                          = 206;
    private const int STATUS_INST_DEACTIVATION_TIME_MANUAL_IS_INVALID                    = 207;
    private const int STATUS_INST_DEACTIVATION_TIME_AUTOMATIC_IS_INVALID                 = 208;
    private const int STATUS_INST_TIMETABLE_IS_INVALID                                   = 209;
    private const int STATUS_INST_CONTACT1_ID_IS_INVALID                                 = 210;
    private const int STATUS_INST_CONTACT2_ID_IS_INVALID                                 = 211;
    private const int STATUS_INST_EMERGENCY_CONTACT_ID_IS_INVALID                        = 212;
    private const int STATUS_INST_WAKEUPTIME_ID_IS_INVALID                               = 213;
    private const int STATUS_INST_SLEEPTIME_ID_IS_INVALID                                = 214;
    private const int STATUS_INST_DAYSTART_ID_IS_INVALID                                 = 215;
    private const int STATUS_INST_DAYEND_ID_IS_INVALID                                   = 216;
    private const int STATUS_INST_BLIND_LEVEL_IS_EMULATED                                = 217;
    private const int STATUS_INST_SLATS_LEVEL_IS_EMULATED                                = 218;
    private const int STATUS_INST_ACTIVATORIDSHADOWINGBYSUNPOSITION_IS_INVALID           = 220;
    private const int STATUS_INST_AZIMUTHID_IS_INVALID                                   = 221;
    private const int STATUS_INST_ALTITUDEID_IS_INVALID                                  = 222;
    private const int STATUS_INST_BRIGTHNESSIDSHADOWINGBYSUNPOSITION_IS_INVALID          = 223;
    private const int STATUS_INST_BRIGHTNESSTHRESHOLDIDSHADOWINGBYSUNPOSITION_IS_INVALID = 224;
    private const int STATUS_INST_ROOMTEMPERATUREID_IS_INVALID                           = 225;
    private const int STATUS_INST_ACTIVATORIDSHADOWINGBRIGHTNESS_IS_INVALID              = 230;
    private const int STATUS_INST_BRIGHTNESSIDSHADOWINGBRIGHTNESS_IS_INVALID             = 231;
    private const int STATUS_INST_THRESHOLDIDHIGHBRIGHTNESS_IS_INVALID                   = 232;
    private const int STATUS_INST_THRESHOLDIDLESSRIGHTNESS_IS_INVALID                    = 233;
    private const int STATUS_INST_BLINDLEVEL_IS_OUT_OF_RANGE                             = 234;
    private const int STATUS_INST_SLATSLEVEL_IS_OUT_OF_RANGE                             = 235;
    private const int STATUS_INST_SLATSLEVEL_ID_IS_INVALID                               = 236;
    private const int STATUS_INST_BLINDLEVEL_ID_PROFILE_NOT_SET                          = 237;
    private const int STATUS_INST_BLINDLEVEL_ID_PROFILE_MIN_MAX_INVALID                  = 238;
    private const int STATUS_INST_SLATSLEVEL_ID_PROFILE_MIN_MAX_INVALID                  = 239;
    private const int STATUS_INST_SLATSLEVEL_ID_PROFILE_NOT_SET                          = 240;

    // -- property names --
    private const string PROP_BLINDLEVELID                      = 'BlindLevelID';
    private const string PROP_SLATSLEVELID                      = 'SlatsLevelID';
    private const string PROP_WEEKLYTIMETABLEEVENTID            = 'WeeklyTimeTableEventID';
    private const string PROP_HOLIDAYINDICATORID                = 'HolidayIndicatorID';
    private const string PROP_DAYUSEDWHENHOLIDAY                = 'DayUsedWhenHoliday';
    private const string PROP_WAKEUPTIMEID                      = 'WakeUpTimeID';
    private const string PROP_WAKEUPTIMEOFFSET                  = 'WakeUpTimeOffset';
    private const string PROP_BEDTIMEID                         = 'BedTimeID';
    private const string PROP_BEDTIMEOFFSET                     = 'BedTimeOffset';
    private const string PROP_CONTACTCLOSE1ID                   = 'ContactClose1ID';
    private const string PROP_CONTACTCLOSE2ID                   = 'ContactClose2ID';
    private const string PROP_CONTACTCLOSELEVEL1                = 'ContactCloseLevel1';
    private const string PROP_CONTACTCLOSELEVEL2                = 'ContactCloseLevel2';
    private const string PROP_CONTACTCLOSESLATSLEVEL1           = 'ContactCloseSlatsLevel1';
    private const string PROP_CONTACTCLOSESLATSLEVEL2           = 'ContactCloseSlatsLevel2';
    private const string PROP_CONTACTOPEN1ID                    = 'ContactOpen1ID';
    private const string PROP_CONTACTOPEN2ID                    = 'ContactOpen2ID';
    private const string PROP_CONTACTOPENLEVEL1                 = 'ContactOpenLevel1';
    private const string PROP_CONTACTOPENLEVEL2                 = 'ContactOpenLevel2';
    private const string PROP_CONTACTOPENSLATSLEVEL1            = 'ContactOpenSlatsLevel1';
    private const string PROP_CONTACTOPENSLATSLEVEL2            = 'ContactOpenSlatsLevel2';
    private const string PROP_EMERGENCYCONTACTID                = 'EmergencyContactID';
    private const string PROP_CONTACTSTOCLOSEHAVEHIGHERPRIORITY = 'ContactsToCloseHaveHigherPriority';

    //shadowing, according to sun position
    private const string PROP_ACTIVATORIDSHADOWINGBYSUNPOSITION           = 'ActivatorIDShadowingBySunPosition';
    private const string PROP_AZIMUTHID                                   = 'AzimuthID';
    private const string PROP_ALTITUDEID                                  = 'AltitudeID';
    private const string PROP_AZIMUTHFROM                                 = 'AzimuthFrom';
    private const string PROP_AZIMUTHTO                                   = 'AzimuthTo';
    private const string PROP_ALTITUDEFROM                                = 'AltitudeFrom';
    private const string PROP_ALTITUDETO                                  = 'AltitudeTo';
    private const string PROP_BRIGHTNESSIDSHADOWINGBYSUNPOSITION          = 'BrightnessIDShadowingBySunPosition';
    private const string PROP_BRIGHTNESSAVGMINUTESSHADOWINGBYSUNPOSITION  = 'BrightnessAvgMinutesShadowingBySunPosition';
    private const string PROP_BRIGHTNESSTHRESHOLDIDSHADOWINGBYSUNPOSITION = 'BrightnessThresholdIDShadowingBySunPosition';
    private const string PROP_TEMPERATUREIDSHADOWINGBYSUNPOSITION         = 'TemperatureIDShadowingBySunPosition';
    private const string PROP_LOWSUNPOSITIONBLINDLEVEL                    = 'LowSunPositionBlindLevel';
    private const string PROP_HIGHSUNPOSITIONBLINDLEVEL                   = 'HighSunPositionBlindLevel';
    private const string PROP_LOWSUNPOSITIONSLATSLEVEL                    = 'LowSunPositionSlatsLevel';
    private const string PROP_HIGHSUNPOSITIONSLATSLEVEL                   = 'HighSunPositionSlatsLevel';
    private const string PROP_DEPTHSUNLIGHT                               = 'DepthSunLight';
    private const string PROP_WINDOWORIENTATION                           = 'WindowOrientation';
    private const string PROP_WINDOWSSLOPE                                = 'WindowsSlope';
    private const string PROP_WINDOWSHEIGHT                               = 'WindowHeight';
    private const string PROP_PARAPETHEIGHT                               = 'ParapetHeight';
    private const string PROP_MINIMUMSHADERELEVANTBLINDLEVEL              = 'MinimumShadeRelevantBlindLevel';
    private const string PROP_HALFSHADERELEVANTBLINDLEVEL                 = 'HalfShadeRelevantBlindLevel';
    private const string PROP_MAXIMUMSHADERELEVANTBLINDLEVEL              = 'MaximumShadeRelevantBlindLevel';
    private const string PROP_MINIMUMSHADERELEVANTSLATSLEVEL              = 'MinimumShadeRelevantSlatsLevel';
    private const string PROP_MAXIMUMSHADERELEVANTSLATSLEVEL              = 'MaximumShadeRelevantSlatsLevel';


    //shadowing, according to brightness
    private const string PROP_ACTIVATORIDSHADOWINGBRIGHTNESS          = 'ActivatorIDShadowingBrightness';
    private const string PROP_BRIGHTNESSIDSHADOWINGBRIGHTNESS         = 'BrightnessIDShadowingBrightness';
    private const string PROP_BRIGHTNESSAVGMINUTESSHADOWINGBRIGHTNESS = 'BrightnessAvgMinutesShadowingBrightness';
    private const string PROP_THRESHOLDIDHIGHBRIGHTNESS               = 'ThresholdIDHighBrightness';
    private const string PROP_THRESHOLDIDLESSBRIGHTNESS               = 'ThresholdIDLessBrightness';

    private const string PROP_ACTIVATEDINDIVIDUALDAYLEVELS                = 'ActivatedIndividualDayLevels';
    private const string PROP_DAYBLINDLEVEL                               = 'DayBlindLevel';
    private const string PROP_DAYSLATSLEVEL                               = 'DaySlatsLevel';
    private const string PROP_ACTIVATEDINDIVIDUALNIGHTLEVELS              = 'ActivatedIndividualNightLevels';
    private const string PROP_NIGHTBLINDLEVEL                             = 'NightBlindLevel';
    private const string PROP_NIGHTSLATSLEVEL                             = 'NightSlatsLevel';
    private const string PROP_ISDAYINDICATORID                            = 'IsDayIndicatorID';
    private const string PROP_BRIGHTNESSID                                = 'BrightnessID';
    private const string PROP_BRIGHTNESSAVGMINUTES                        = 'BrightnessAvgMinutes';
    private const string PROP_BRIGHTNESSTHRESHOLDID                       = 'BrightnessThresholdID';
    private const string PROP_BLINDLEVELLESSBRIGHTNESSSHADOWINGBRIGHTNESS = 'BlindLevelLessBrightnessShadowingBrightness';
    private const string PROP_SLATSLEVELLESSBRIGHTNESSSHADOWINGBRIGHTNESS = 'SlatsLevelLessBrightnessShadowingBrightness';
    private const string PROP_BLINDLEVELHIGHBRIGHTNESSSHADOWINGBRIGHTNESS = 'BlindLevelHighBrightnessShadowingBrightness';
    private const string PROP_SLATSLEVELHIGHBRIGHTNESSSHADOWINGBRIGHTNESS = 'SlatsLevelHighBrightnessShadowingBrightness';
    private const string PROP_DAYSTARTID                                  = 'DayStartID';
    private const string PROP_DAYENDID                                    = 'DayEndID';

    //expert
    private const string PROP_UPDATEINTERVAL                = 'UpdateInterval';
    private const string PROP_DEACTIVATIONAUTOMATICMOVEMENT = 'DeactivationAutomaticMovement';
    private const string PROP_DEACTIVATIONMANUALMOVEMENT    = 'DeactivationManualMovement';
    private const string PROP_MINMOVEMENT                   = 'MinMovement';
    private const string PROP_MINMOVEMENTATENDPOSITION      = 'MinMovementAtEndPosition';

    private const string PROP_DELAYTIMEDAYNIGHTCHANGE           = 'DelayTimeDayNightChange';
    private const string PROP_DELAYTIMEDAYNIGHTCHANGEISRANDOMLY = 'DelayTimeDayNightChangeIsRandomly';
    private const string PROP_SHOWNOTUSEDELEMENTS               = 'ShowNotUsedElements';
    private const string PROP_WRITELASTDECISION                 = 'WriteLastDecision';
    private const string PROP_WRITEDECISIONTRACE                = 'WriteDecisionTrace';

    //attribute names
    private const string ATTR_MANUALMOVEMENT           = 'manualMovement';
    private const string ATTR_LASTMOVE                 = 'lastMovement';
    private const string ATTR_TIMESTAMP_AUTOMATIC      = 'TimeStampAutomatic';
    private const string ATTR_CONTACT_OPEN             = 'AttrContactOpen';
    private const string ATTR_DAYTIME_CHANGE_TIME      = 'DaytimeChangeTime';
    private const string ATTR_LAST_ISDAYBYTIMESCHEDULE = 'LastIsDayByTimeSchedule';

    //timer names
    private const string TIMER_UPDATE           = 'Update';
    private const string TIMER_DELAYED_MOVEMENT = 'DelayedMovement';
    private const string TIMER_OPEN_CONTACT1    = 'OpenContact1';
    private const string TIMER_OPEN_CONTACT2    = 'OpenContact2';
    private const string TIMER_CLOSE_CONTACT1   = 'CloseContact1';
    private const string TIMER_CLOSE_CONTACT2   = 'CloseContact2';


    //variable names
    private const string VAR_IDENT_LAST_MESSAGE   = 'LAST_MESSAGE';
    private const string VAR_IDENT_LAST_DECISION  = 'LAST_DECISION';
    private const string VAR_IDENT_DECISION_TRACE = 'DECISION_TRACE';
    private const string VAR_IDENT_ACTIVATED      = 'ACTIVATED';

    private const int MOVEMENT_WAIT_TIME         = 90; //Wartezeit bis zur Erreichung der Zielposition in Sekunden
    private const int IGNORE_MOVEMENT_TIME       = 40; //Nach einer Bewegung wird eine erneute gleiche Bewegung innerhalb dieser Zeit ignoriert
    private const int ALLOWED_TOLERANCE_MOVEMENT = 1; //erlaubte Abweichung bei Bewegungen in Prozent

    private string $objectName;

    private ?array $profileBlindLevel;

    private ?array $profileSlatsLevel;

    // Grund, warum eine physische Fahrt unterblieben ist (gesetzt in shouldPerformMovement/isSameMovementRecently)
    private string $moveSkipReason = '';

    // Grund, warum eine aktivierte Beschattung nicht (mehr) greift (gesetzt in getPositionsOfShadowing*)
    private string $shadowingReason = '';

    // entscheidungsrelevante Helligkeit (effektiver Wert) und Schwellwert der greifenden Beschattung, für die Erklärung (gesetzt in getPositionsOfShadowing*)
    private string $shadowingBrightnessInfo = '';

    // Hinweis auf einen temperaturbedingten Hitze-/Wärmeschutz der Sonnenstand-Beschattung, für die Erklärung (gesetzt in getPositionsOfShadowingBySunPosition)
    private string $shadowingHeatInfo = '';

    // Steuerungslauf nur simulieren (Erklärung): Aktor wird nicht bewegt und keine Zustände (Attribute/Variablen/Timer) verändert
    private bool $dryRun = false;

    // Strukturierter Ablauf des letzten Steuerungslaufs (für Debug-Log und den "Erklären"-Button)
    private array $decisionTrace = [];


    // Die folgenden Funktionen überschreiben die interne IPS_() Funktionen
    public function __construct($InstanceID)
    {
        $this->objectName = IPS_GetName($InstanceID);

        parent::__construct($InstanceID);
    }

    public function Create(): void
    {
        // Diese Zeile nicht löschen.
        parent::Create();

        $this->RegisterProperties();
        $this->Logger_Dbg(
            __FUNCTION__,
            'RegisterAttributes'
        );

        $this->RegisterAttributes();

        $this->RegisterTimer(self::TIMER_UPDATE, 0, 'BLC_ControlBlind(' . $this->InstanceID . ', true);');
        $this->RegisterTimer(
            self::TIMER_DELAYED_MOVEMENT,
            0,
            'BLC_ControlBlind(' . $this->InstanceID . ', true);'
        );
        // Entprellungs-Timer feuern über RequestAction (Timer-Name = Ident)
        foreach ([self::TIMER_OPEN_CONTACT1, self::TIMER_OPEN_CONTACT2, self::TIMER_CLOSE_CONTACT1, self::TIMER_CLOSE_CONTACT2] as $timer) {
            $this->RegisterTimer($timer, 0, sprintf('IPS_RequestAction(%d, "%s", 0);', $this->InstanceID, $timer));
        }
    }

    public function ApplyChanges(): void
    {
        //we will wait until the kernel is ready
        $this->RegisterMessage(0, IPS_KERNELMESSAGE);

        //Never delete this line!
        parent::ApplyChanges();

        if (IPS_GetKernelRunlevel() !== KR_READY) {
            return;
        }

        $this->RegisterReferences();
        $this->RegisterMessages();
        $this->RegisterVariables();

        $this->SetInstanceStatusAndTimerEvent();
    }

    public function RequestAction(string $Ident, mixed $Value): void
    {
        if (is_bool($Value)) {
            $this->Logger_Dbg(__FUNCTION__, sprintf('Ident: %s, Value: %s', $Ident, (int)$Value));
        } else {
            $this->Logger_Dbg(__FUNCTION__, sprintf('Ident: %s, Value: %s', $Ident, $Value));
        }

        switch ($Ident) {
            case self::VAR_IDENT_ACTIVATED:
                $this->handleActivation($Value);
                break;

            case 'MoveBlindToShadowingPosition':
                $this->MoveBlindToShadowingPosition((int)$Value);
                break;

            case self::TIMER_OPEN_CONTACT1:
            case self::TIMER_OPEN_CONTACT2:
            case self::TIMER_CLOSE_CONTACT1:
            case self::TIMER_CLOSE_CONTACT2:
                // Ablauf der Beruhigungszeit: zuletzt gemeldeten Kontaktzustand auswerten
                $this->SetTimerInterval($Ident, 0);
                $this->ControlBlind(false);
                break;

            case self::PROP_SLATSLEVELID:
            case self::PROP_HOLIDAYINDICATORID:
            case self::PROP_WAKEUPTIMEID:
            case self::PROP_BEDTIMEID:
            case self::PROP_ACTIVATEDINDIVIDUALDAYLEVELS:
            case self::PROP_ACTIVATEDINDIVIDUALNIGHTLEVELS:
            case self::PROP_BRIGHTNESSID:
            case self::PROP_BRIGHTNESSIDSHADOWINGBYSUNPOSITION:
            case self::PROP_BRIGHTNESSIDSHADOWINGBRIGHTNESS:
                $this->updateFormVisibility($Ident, $Value);
                break;

            default:
                trigger_error(sprintf('Instance %s: Unknown Ident %s', $this->InstanceID, $Ident));
        }
    }

    private function handleActivation(mixed $Value): void
    {
        if ($Value) {
            $this->resetManualMovement();
        } else {
            $this->Logger_Inf(sprintf('\'%s\' wurde deaktiviert.', IPS_GetObject($this->InstanceID)['ObjectName']));
        }

        $this->SetValue(self::VAR_IDENT_ACTIVATED, $Value);
        $this->SetInstanceStatusAndTimerEvent();
        $this->RegisterOnceTimer('BlindControlTimer',sprintf('BLC_ControlBlind(%s, %s);', $this->InstanceID, 'false'));

    }

    /**
     * Aktualisiert die Sichtbarkeit von Konfigurationsfeldern basierend auf der Auswahl im Formular.
     *
     * @param string $Ident Die ID des betroffenen Elements/Property.
     * @param mixed  $Value Der neue Wert des Elements.
     *
     * @return void
     */
    private function updateFormVisibility(string $Ident, mixed $Value): void
    {
        $showNotUsed = $this->ReadPropertyBoolean(self::PROP_SHOWNOTUSEDELEMENTS);
        $hasValue = is_bool($Value) ? $Value : ($Value > 0);
        $isVisible = $hasValue || $showNotUsed;

        switch ($Ident) {
            case self::PROP_SLATSLEVELID:
                $fields = [
                    self::PROP_LOWSUNPOSITIONSLATSLEVEL,
                    self::PROP_HIGHSUNPOSITIONSLATSLEVEL,
                    self::PROP_MINIMUMSHADERELEVANTSLATSLEVEL,
                    self::PROP_SLATSLEVELLESSBRIGHTNESSSHADOWINGBRIGHTNESS,
                    self::PROP_SLATSLEVELHIGHBRIGHTNESSSHADOWINGBRIGHTNESS,
                    self::PROP_CONTACTCLOSESLATSLEVEL1,
                    self::PROP_CONTACTCLOSESLATSLEVEL2,
                    self::PROP_CONTACTOPENSLATSLEVEL1,
                    self::PROP_CONTACTOPENSLATSLEVEL2
                ];

                for ($i = 1; $i <= 2; $i++) {
                    for ($j = 2; $j <= 3; $j++) {
                        $fields[] = $this->openSlatsProp($i, $j);
                        $fields[] = $this->closeSlatsProp($i, $j);
                    }
                }

                foreach ($fields as $field) {
                    $this->UpdateFormField($field, 'visible', $isVisible);
                }

                // Spezialfall: Felder, die zwei Bedingungen haben
                $this->UpdateFormField(
                    self::PROP_NIGHTSLATSLEVEL,
                    'visible',
                    ($hasValue && $this->ReadPropertyBoolean(self::PROP_ACTIVATEDINDIVIDUALNIGHTLEVELS)) || $showNotUsed
                );
                $this->UpdateFormField(
                    self::PROP_DAYSLATSLEVEL,
                    'visible',
                    ($hasValue && $this->ReadPropertyBoolean(self::PROP_ACTIVATEDINDIVIDUALDAYLEVELS)) || $showNotUsed
                );
                break;

            case self::PROP_HOLIDAYINDICATORID:
                $this->UpdateFormField(self::PROP_DAYUSEDWHENHOLIDAY, 'visible', $isVisible);
                break;

            case self::PROP_WAKEUPTIMEID:
                $this->UpdateFormField(self::PROP_WAKEUPTIMEOFFSET, 'visible', $isVisible);
                break;

            case self::PROP_BEDTIMEID:
                $this->UpdateFormField(self::PROP_BEDTIMEOFFSET, 'visible', $isVisible);
                break;

            case self::PROP_ACTIVATEDINDIVIDUALDAYLEVELS:
                $this->UpdateFormField(self::PROP_DAYBLINDLEVEL, 'visible', $isVisible);
                $this->UpdateFormField(
                    self::PROP_DAYSLATSLEVEL,
                    'visible',
                    (IPS_VariableExists($this->ReadPropertyInteger(self::PROP_SLATSLEVELID)) && $Value) || $showNotUsed
                );
                break;

            case self::PROP_ACTIVATEDINDIVIDUALNIGHTLEVELS:
                $this->UpdateFormField(self::PROP_NIGHTBLINDLEVEL, 'visible', $isVisible);
                $this->UpdateFormField(
                    self::PROP_NIGHTSLATSLEVEL,
                    'visible',
                    (IPS_VariableExists($this->ReadPropertyInteger(self::PROP_SLATSLEVELID)) && $Value) || $showNotUsed
                );
                break;

            case self::PROP_BRIGHTNESSID:
                $this->UpdateFormField(self::PROP_BRIGHTNESSAVGMINUTES, 'visible', $isVisible);
                $this->UpdateFormField(self::PROP_BRIGHTNESSTHRESHOLDID, 'visible', $isVisible);
                break;

            case self::PROP_BRIGHTNESSIDSHADOWINGBYSUNPOSITION:
                $this->UpdateFormField(self::PROP_BRIGHTNESSAVGMINUTESSHADOWINGBYSUNPOSITION, 'visible', $isVisible);
                $this->UpdateFormField(self::PROP_BRIGHTNESSTHRESHOLDIDSHADOWINGBYSUNPOSITION, 'visible', $isVisible);
                break;

            case self::PROP_BRIGHTNESSIDSHADOWINGBRIGHTNESS:
                $this->UpdateFormField(self::PROP_BRIGHTNESSAVGMINUTESSHADOWINGBRIGHTNESS, 'visible', $isVisible);
                break;
        }
    }

    /**
     * Verarbeitet eingehende Nachrichten von IP-Symcon.
     */

    public function MessageSink(int $TimeStamp, int $SenderID, int $Message, array $Data): void
    {
        parent::MessageSink($TimeStamp, $SenderID, $Message, $Data);

        $this->logMessageSinkDebug($TimeStamp, $SenderID, $Message, $Data);

        switch ($Message) {
            case IPS_KERNELMESSAGE:
                if ($Data[0] === KR_READY) {
                    $this->ApplyChanges();
                }
                break;

            case EM_UPDATE:
            case VM_UPDATE:
            case VM_CHANGEPROFILEACTION:
                $this->handleUpdateMessage($SenderID, $Data);
                break;
        }
    }

    /**
     * Verarbeitet Aktualisierungen von Variablen und Ereignissen.
     */
    private function handleUpdateMessage(int $SenderID, array $Data): void
    {
        // Index 1 enthält bei VM_UPDATE/EM_UPDATE den "Changed"-Status oder Wert-Infos
        if (($Data[1] ?? null) === false) {
            return;
        }

        $this->SetInstanceStatusAndTimerEvent();

        if (!$this->GetValue(self::VAR_IDENT_ACTIVATED)) {
            return;
        }

        // Separate Entprellung je Öffnen-Kontakt: nur die zuletzt gemeldete Position auswerten
        for ($i = 1; $i <= 2; $i++) {
            if ($SenderID === $this->ReadPropertyInteger(constant("self::PROP_CONTACTOPEN{$i}ID"))) {
                $delay = $this->ReadPropertyInteger($this->openDelayProp($i));
                if ($delay > 0 && IPS_GetKernelRunlevel() === KR_READY) {
                    $timer = $i === 1 ? self::TIMER_OPEN_CONTACT1 : self::TIMER_OPEN_CONTACT2;
                    $this->SetTimerInterval($timer, 0);
                    $this->SetTimerInterval($timer, $delay * 1000);
                    $this->Logger_Dbg(__FUNCTION__, sprintf('Öffnen-Kontakt %d: Auswertung um %d Sekunde(n) verzögert', $i, $delay));
                    return;
                }
            }
        }

        // Separate Entprellung je Schließen-Kontakt: nur die zuletzt gemeldete Position auswerten
        for ($i = 1; $i <= 2; $i++) {
            if ($SenderID === $this->ReadPropertyInteger(constant("self::PROP_CONTACTCLOSE{$i}ID"))) {
                $delay = $this->ReadPropertyInteger($this->closeDelayProp($i));
                if ($delay > 0 && IPS_GetKernelRunlevel() === KR_READY) {
                    $timer = $i === 1 ? self::TIMER_CLOSE_CONTACT1 : self::TIMER_CLOSE_CONTACT2;
                    $this->SetTimerInterval($timer, 0);
                    $this->SetTimerInterval($timer, $delay * 1000);
                    $this->Logger_Dbg(__FUNCTION__, sprintf('Schließen-Kontakt %d: Auswertung um %d Sekunde(n) verzögert', $i, $delay));
                    return;
                }
            }
        }

        // Prüfen, ob die Verzögerungszeit (DeactivationTimeAuto) berücksichtigt werden soll
        $isTriggerSource = in_array(
            $SenderID,
            [
                $this->ReadPropertyInteger(self::PROP_CONTACTOPEN1ID),
                $this->ReadPropertyInteger(self::PROP_CONTACTOPEN2ID),
                $this->ReadPropertyInteger(self::PROP_CONTACTCLOSE1ID),
                $this->ReadPropertyInteger(self::PROP_CONTACTCLOSE2ID),
                $this->ReadPropertyInteger(self::PROP_EMERGENCYCONTACTID),
                $this->ReadPropertyInteger(self::PROP_ACTIVATORIDSHADOWINGBRIGHTNESS),
                $this->ReadPropertyInteger(self::PROP_ACTIVATORIDSHADOWINGBYSUNPOSITION)
            ],
            true
        );

        if (IPS_GetKernelRunlevel() === KR_READY) {
            $considerDeactivation = $isTriggerSource ? 'false' : 'true';
            $this->RegisterOnceTimer('BlindControlTimer_ControlBlind',sprintf('BLC_ControlBlind(%s, %s);', $this->InstanceID, $considerDeactivation));
        }
    }

    /**
     * Zentralisiertes Debug-Logging für eingehende Nachrichten.
     */
    private function logMessageSinkDebug(int $TimeStamp, int $SenderID, int $Message, array $Data): void
    {
        $this->Logger_Dbg(
            __FUNCTION__,
            sprintf(
                'ModuleVersion: %s, Timestamp: %s, SenderID: %s[%s], Message: %s [%s], Data: %s',
                $this->getModuleVersion(),
                $TimeStamp,
                IPS_GetObject($SenderID)['ObjectName'],
                $SenderID,
                array_search($Message, get_defined_constants(true)['IP-Symcon'], true) ?: 'UNKNOWN',
                $Message,
                json_encode($Data, JSON_THROW_ON_ERROR)
            )
        );
    }

    public function GetConfigurationForm(): string
    {
        $form = json_decode(file_get_contents(__DIR__ . '/form.json'), true, 512, JSON_THROW_ON_ERROR);

        $elements[] = [
            'type'    => 'Label',
            'caption' => 'In this instance, all parameters for controlling a single blind are stored.'
        ];

        $form['elements'] = array_merge($elements, $form['elements']);

        $this->SetVisibilityOfNotUsedElements($form);

        $this->SendDebug('Form', json_encode($form, JSON_THROW_ON_ERROR), 0);
        return json_encode($form, JSON_THROW_ON_ERROR);
    }

    /**
     * Steuert die Sichtbarkeit von Konfigurationsfeldern im Formular basierend auf gesetzten IDs und dem "ShowNotUsedElements" Schalter.
     *
     * @param array $form Referenz auf das geladene Formular-Array.
     */
    private function SetVisibilityOfNotUsedElements(array &$form): void
    {
        $bShow = $this->ReadPropertyBoolean(self::PROP_SHOWNOTUSEDELEMENTS);

        // 1. Einfache Abhängigkeiten: 'Feldname' => 'Abhängig von VariableID'
        $simpleDependencies = [
            self::PROP_DAYUSEDWHENHOLIDAY                     => self::PROP_HOLIDAYINDICATORID,
            self::PROP_WAKEUPTIMEOFFSET                       => self::PROP_WAKEUPTIMEID,
            self::PROP_BEDTIMEOFFSET                         => self::PROP_BEDTIMEID,
            self::PROP_BRIGHTNESSAVGMINUTES                   => self::PROP_BRIGHTNESSID,
            self::PROP_BRIGHTNESSTHRESHOLDID                 => self::PROP_BRIGHTNESSID,
            self::PROP_BRIGHTNESSAVGMINUTESSHADOWINGBYSUNPOSITION  => self::PROP_BRIGHTNESSIDSHADOWINGBYSUNPOSITION,
            self::PROP_BRIGHTNESSTHRESHOLDIDSHADOWINGBYSUNPOSITION => self::PROP_BRIGHTNESSIDSHADOWINGBYSUNPOSITION,
            self::PROP_BRIGHTNESSAVGMINUTESSHADOWINGBRIGHTNESS     => self::PROP_BRIGHTNESSIDSHADOWINGBRIGHTNESS,
        ];

        foreach ($simpleDependencies as $field => $dependencyProp) {
            $form = $this->MyUpdateFormField($form, $field, 'visible',
                                             IPS_VariableExists($this->ReadPropertyInteger($dependencyProp)) || $bShow
            );
        }

        // 2. Lamellen-Abhängigkeiten (viele Felder hängen an PROP_SLATSLEVELID)
        $slatsExists = IPS_VariableExists($this->ReadPropertyInteger(self::PROP_SLATSLEVELID));
        $slatsFields = [
            self::PROP_LOWSUNPOSITIONSLATSLEVEL,
            self::PROP_HIGHSUNPOSITIONSLATSLEVEL,
            self::PROP_MINIMUMSHADERELEVANTSLATSLEVEL,
            self::PROP_MAXIMUMSHADERELEVANTSLATSLEVEL,
            self::PROP_SLATSLEVELLESSBRIGHTNESSSHADOWINGBRIGHTNESS,
            self::PROP_SLATSLEVELHIGHBRIGHTNESSSHADOWINGBRIGHTNESS,
            self::PROP_CONTACTCLOSESLATSLEVEL1,
            self::PROP_CONTACTCLOSESLATSLEVEL2,
            self::PROP_CONTACTOPENSLATSLEVEL1,
            self::PROP_CONTACTOPENSLATSLEVEL2,
            'SlatsLevel'
        ];

        for ($i = 1; $i <= 2; $i++) {
            for ($j = 2; $j <= 3; $j++) {
                $slatsFields[] = $this->openSlatsProp($i, $j);
                $slatsFields[] = $this->closeSlatsProp($i, $j);
            }
        }

        foreach ($slatsFields as $field) {
            $form = $this->MyUpdateFormField($form, $field, 'visible', $slatsExists || $bShow);
        }

        // 3. Speziallogik für Individual-Levels (Kombination aus Boolean + optional Lamellen)
        $nightIndividual = $this->ReadPropertyBoolean(self::PROP_ACTIVATEDINDIVIDUALNIGHTLEVELS);
        $dayIndividual   = $this->ReadPropertyBoolean(self::PROP_ACTIVATEDINDIVIDUALDAYLEVELS);

        $form = $this->MyUpdateFormField($form, self::PROP_NIGHTBLINDLEVEL, 'visible', $nightIndividual || $bShow);
        $form = $this->MyUpdateFormField($form, self::PROP_NIGHTSLATSLEVEL, 'visible', ($slatsExists && $nightIndividual) || $bShow);

        $form = $this->MyUpdateFormField($form, self::PROP_DAYBLINDLEVEL, 'visible', $dayIndividual || $bShow);
        $form = $this->MyUpdateFormField($form, self::PROP_DAYSLATSLEVEL, 'visible', ($slatsExists && $dayIndividual) || $bShow);

        // 4. Manuelle Sonderfälle
        $form = $this->MyUpdateFormField($form, 'ShadowingPosition', 'visible',
                                         ($this->ReadPropertyFloat(self::PROP_MINIMUMSHADERELEVANTBLINDLEVEL) > 0) ||
                                         ($this->ReadPropertyFloat(self::PROP_MAXIMUMSHADERELEVANTBLINDLEVEL) > 0) ||
                                         $bShow
        );
    }
    public function ReceiveData(string $JSONString): string
    {
        trigger_error(sprintf('Fatal error: no ReceiveData expected. (%s)', $JSONString));

        return parent::ReceiveData($JSONString);
    }


    /**
     * Die folgenden Funktionen stehen automatisch zur Verfügung, wenn das Modul über die "Module Control" eingefügt wurden.
     * Die Funktionen werden, mit dem selbst eingerichteten Prefix, in PHP und JSON-RPC zur Verfügung gestellt.
     *
     * @param bool $considerDeactivationTimeAuto
     *
     * @return bool
     * @throws \JsonException
     */
    public function ControlBlind(bool $considerDeactivationTimeAuto): bool
    {
        if (IPS_GetInstance($this->InstanceID)['InstanceStatus'] !== IS_ACTIVE) {
            return false;
        }

        if (((int)ini_get('max_execution_time') > 0) && ((int)ini_get('max_execution_time') <= 35)) {
            set_time_limit(35);
        }

        if (!IPS_SemaphoreEnter($this->InstanceID . '- Blind', 30 * 1000)) { //wir warten maximal 30 Sekunden
            $this->Logger_Dbg(__FUNCTION__, 'Cannot enter semaphore. The waiting time of 30s has expired');
            return false;
        }

        // globale Instanzvariablen setzen
        $this->profileBlindLevel = $this->GetPresentationInformation(self::PROP_BLINDLEVELID);

        // $deactivationTimeAuto: Zeitraum, in dem das automatisch gesetzte Level
        // erhalten bleibt, bevor es überschrieben wird.
        if ($considerDeactivationTimeAuto) {
            $deactivationTimeAuto = $this->ReadPropertyInteger(self::PROP_DEACTIVATIONAUTOMATICMOVEMENT) * 60;
        } else {
            $deactivationTimeAuto = 0;
        }

        //Blind Level ID ermitteln
        $blindLevelId = $this->ReadPropertyInteger(self::PROP_BLINDLEVELID);

        // Die aktuellen Positionen im Jalousieaktor auslesen
        $positionsAct['BlindLevel'] = (float)GetValue($blindLevelId);

        //Slats Level ID ermitteln
        $slatsLevelId = $this->ReadPropertyInteger(self::PROP_SLATSLEVELID);
        if (IPS_VariableExists($slatsLevelId)) {
            $this->profileSlatsLevel    = $this->GetPresentationInformation(self::PROP_SLATSLEVELID);
            $positionsAct['SlatsLevel'] = (float)GetValue($slatsLevelId);
        } else {
            $this->profileSlatsLevel    = null;
            $positionsAct['SlatsLevel'] = null;
        }

        // Ablaufprotokoll für diesen Lauf zurücksetzen (für Debug-Log und "Erklären"-Button)
        $this->decisionTrace = [];
        if ($this->dryRun) {
            $this->addTrace($this->Translate('Explanation of the control run (test run - the blind is not moved)'));
            $this->addTrace('');
        }
        $this->addTrace(sprintf('Aktuelle Position: %s', $this->describeTargetPositions($positionsAct)));

        // --- 1. Tageszeit bestimmen ---
        $dayState = $this->determineDayState($positionsAct['BlindLevel']);
        $this->addTrace('Tageszeit: ' . $this->buildDayStateTrace($dayState));

        //Zeitpunkt der letzten Rollladenbewegung (Höhe oder Lamellen)
        $tsBlindLastMovement = $this->GetBlindLastTimeStamp($blindLevelId, $slatsLevelId);

        // Attribut TimestampAutomatik auslesen
        $tsAutomatik = $this->ReadAttributeInteger(self::ATTR_TIMESTAMP_AUTOMATIC);

        // Grund einer eventuellen Bewegungssperre (für den Entscheidungs-Trace)
        $blockReason          = '';
        $this->moveSkipReason = '';

        // --- 2. Prüfen auf Tageswechsel oder manuelle Bewegungssperre ---
        if ($this->checkIsDayChange($dayState)) {
            //beim Tageswechsel ...
            $deactivationTimeAuto = 0;
            if (!$this->dryRun) {
                $this->WriteAttributeString(
                    self::ATTR_MANUALMOVEMENT,
                    json_encode(['timeStamp' => null, 'blindLevel' => null, 'slatsLevel' => null], JSON_THROW_ON_ERROR)
                );
            }
            $bNoMove = false;
            $this->addTrace('Bewegungssperre: keine (Tag/Nacht-Wechsel, erkannte manuelle Bedienung wird verworfen)');
        } else {
            // während der Verzögerung ist die ursprüngliche Tageszeit anzunehmen
            $isDay = $dayState['isDay'];

            $blockResult = $this->shouldBlockMovement(
                $positionsAct['BlindLevel'],
                $positionsAct['SlatsLevel'],
                $tsBlindLastMovement,
                $isDay,
                $this->ReadAttributeInteger('AttrTimeStampIsDayChange'),
                $tsAutomatik
            );
            $bNoMove     = $blockResult['block'];
            $blockReason = $blockResult['reason'];
            $this->addTrace('Bewegungssperre: ' . ($bNoMove ? $blockReason : 'keine'));
        }
        $isDay = $dayState['isDay'];

        $this->Logger_Dbg(
            __FUNCTION__,
            sprintf(
                'ModuleVersion: %s, tsAutomatik: %s, tsBlind: %s, posActBlindLevel: %.2f,  posActSlatsLevel: %s, bNoMove: %s, isDay: %s, considerDeactivationTimeAuto: %s',
                $this->getModuleVersion(),
                $this->FormatTimeStamp($tsAutomatik),
                $this->FormatTimeStamp($tsBlindLastMovement),
                $positionsAct['BlindLevel'],
                $positionsAct['SlatsLevel'] ?? 'null',
                (int)$bNoMove,
                (int)$isDay,
                (int)$considerDeactivationTimeAuto
            )
        );

        // --- 3. Basis-Zielposition berechnen (Tag/Nacht/Manuell) ---
        $calcResult   = $this->calculateBasePosition($bNoMove, $positionsAct, $dayState);
        $positionsNew = $calcResult['positions'];
        $Hinweis      = $calcResult['hint'];
        if (!$bNoMove) {
            $this->addTrace(sprintf('Basis-Zielposition: %s (%s)', $this->describeTargetPositions($positionsNew), $Hinweis !== '' ? $Hinweis : '—'));
        }

        // --- 4. Beschattungslogik anwenden (nur tagsüber und wenn keine Sperre) ---
        if ($isDay && !$bNoMove) {
            $shadowResult = $this->applyShadowingLogic($positionsAct['BlindLevel'], $positionsNew, $Hinweis);
            if ($shadowResult['positions'] !== $positionsNew) {
                $shadowDetail = $shadowResult['hint'];
                if ($shadowResult['brightnessInfo'] !== '') {
                    $shadowDetail .= ', ' . $shadowResult['brightnessInfo'];
                }
                if ($shadowResult['heatInfo'] !== '') {
                    $shadowDetail .= ', ' . $shadowResult['heatInfo'];
                }
                $this->addTrace(sprintf('Beschattung: aktiv -> %s (%s)', $this->describeTargetPositions($shadowResult['positions']), $shadowDetail));
            } elseif ($shadowResult['reason'] !== '') {
                $this->addTrace('Beschattung: keine (' . $shadowResult['reason'] . ')');
            } else {
                $this->addTrace('Beschattung: keine (nicht konfiguriert)');
            }
            $positionsNew = $shadowResult['positions'];
            $Hinweis      = $shadowResult['hint'];
        } else {
            // nachts gilt keine deactivation Time
            $deactivationTimeAuto = 0;
        }

        // --- 5. Kontakte (Fenster/Notfall) prüfen und Positionen ggf. überschreiben ---
        $contactResult = $this->applyContactLogic($positionsAct, $positionsNew, $deactivationTimeAuto, $bNoMove, $Hinweis);

        // Kontaktstatus immer protokollieren - auch wenn kein Kontakt aktiv ist oder ein offener
        // Kontakt die Zielposition nicht verändert (häufige Rückfrage: "warum sehe ich den Kontakt nicht?")
        $this->addTrace('Kontakte: ' . $contactResult['trace']);

        $positionsNew         = $contactResult['positions'];
        $deactivationTimeAuto = $contactResult['deactivationTimeAuto'];
        $bNoMove              = $contactResult['bNoMove'];
        $Hinweis              = $contactResult['hint'];
        $bEmergency           = $contactResult['bEmergency'];

        // --- 6. Bewegung ausführen ---
        if (!$bNoMove) {
            $blindLevel = $this->calculateNormalizedLevel($positionsNew['BlindLevel'], $this->profileBlindLevel);
            $slatsLevel = null;
            if (IPS_VariableExists($this->ReadPropertyInteger(self::PROP_SLATSLEVELID))) {
                $slatsLevel = $this->calculateNormalizedLevel($positionsNew['SlatsLevel'], $this->profileSlatsLevel);
            }

            $this->MoveBlind($blindLevel, $slatsLevel, $deactivationTimeAuto, $Hinweis);
        }

        // --- 7. Entscheidung des Laufs dokumentieren (immer, auch wenn nicht gefahren wurde) ---
        $this->traceDecisionResult($bNoMove, $blockReason, $positionsNew, $Hinweis);

        // vollständiges Ablaufprotokoll - sofern aktiviert - als HTML in der Statusvariable festhalten
        $this->writeDecisionTraceVariable();

        //im Notfall wird die Automatik deaktiviert
        if ($bEmergency && !$this->dryRun) {
            $this->SetValue(self::VAR_IDENT_ACTIVATED, false);
            $this->SetInstanceStatusAndTimerEvent();
        }

        // vollständiges Ablaufprotokoll ins Debug schreiben (hilft bei Support/Diagnose)
        $this->Logger_Dbg(__FUNCTION__, 'Ablauf:' . ' | ' . implode(' | ', $this->decisionTrace));

        IPS_SemaphoreLeave($this->InstanceID . '- Blind');

        return true;
    }

    /**
     * Simuliert einen Steuerungslauf ("Probelauf"), ohne den Rollladen zu bewegen oder Zustände zu verändern,
     * und liefert den vollständigen, lesbaren Ablauf als Text zurück. Dient dazu, dem Anwender nachvollziehbar
     * zu machen, warum sich der Rollladen aktuell bewegen würde - oder eben nicht.
     *
     * @return string Mehrzeiliges Ablaufprotokoll.
     * @throws \JsonException
     */
    public function ExplainControlBlind(): string
    {
        if (IPS_GetInstance($this->InstanceID)['InstanceStatus'] !== IS_ACTIVE) {
            return $this->Translate('The instance is not active.');
        }

        $this->dryRun = true;
        try {
            $this->ControlBlind(true);
        } finally {
            $this->dryRun = false;
        }

        if ($this->decisionTrace === []) {
            return $this->Translate('No decision could be determined (control run could not be performed).');
        }

        return implode(PHP_EOL, $this->decisionTrace);
    }

    private function determineDayState(float $currentBlindLevel): array
    {
        $scheduleAuf         = null;
        $scheduleAb          = null;
        $isDayByTimeSchedule = $this->getIsDayByTimeSchedule($scheduleAuf, $scheduleAb);
        $brightness          = null;
        $isDayByDayDetection = $this->getIsDayByDayDetection($brightness, $currentBlindLevel);

        if ($isDayByDayDetection === null) {
            $isDay = $isDayByTimeSchedule;
        } else {
            $isDay = $isDayByTimeSchedule && $isDayByDayDetection;
        }

        return [
            'isDay'               => $isDay,
            'isDayByTimeSchedule' => $isDayByTimeSchedule,
            'isDayByDayDetection' => $isDayByDayDetection,
            'brightness'          => $brightness,
            'scheduleAuf'         => $scheduleAuf,
            'scheduleAb'          => $scheduleAb
        ];
    }

    private function calculateBasePosition(bool $bNoMove, array $positionsAct, array $dayState): array
    {
        if ($bNoMove) {
            return ['positions' => $positionsAct, 'hint' => ''];
        }

        $positionsNew = $positionsAct;

        // Basis-Hinweis und Attribut-Update (für Tag und Nacht gleich)
        if ($dayState['isDayByTimeSchedule'] !== $this->ReadAttributeBoolean(self::ATTR_LAST_ISDAYBYTIMESCHEDULE)) {
            $hint = 'WP';
        } elseif ($dayState['isDay']) {
            $hint = 'Tag';
        } else {
            $hint = 'Nacht';
        }

        if (!$this->dryRun && $this->ReadAttributeInteger(self::ATTR_DAYTIME_CHANGE_TIME) === 0) {
            $this->WriteAttributeBoolean(self::ATTR_LAST_ISDAYBYTIMESCHEDULE, $dayState['isDayByTimeSchedule']);
        }

        if ($dayState['isDay']) {
            $positionsNew = $this->calculateDayPosition($positionsNew);
        } else {
            $positionsNew = $this->calculateNightPosition($positionsNew);
            if ($this->ReadPropertyBoolean(self::PROP_ACTIVATEDINDIVIDUALNIGHTLEVELS)) {
                $hint .= ', indiv.Pos.';
            }
        }

        if (isset($dayState['isDayByDayDetection'], $dayState['brightness'])) {
            // den tatsächlich verwendeten (ggf. gemittelten) Helligkeitswert anzeigen - konsistent mit der Tageszeit-Zeile
            $hint .= ', ' . GetValueFormattedEx($this->ReadPropertyInteger(self::PROP_BRIGHTNESSID), $dayState['brightness']);
        }
        return ['positions' => $positionsNew, 'hint' => $hint];
    }

    private function calculateDayPosition(array $positions): array
    {
        // 1. Manuelle Bewegung prüfen
        $manual          = json_decode($this->ReadAttributeString(self::ATTR_MANUALMOVEMENT), true, 512, JSON_THROW_ON_ERROR);
        $deactivationMin = $this->ReadPropertyInteger(self::PROP_DEACTIVATIONMANUALMOVEMENT);

        $isManualActive =
            isset($manual['timeStamp']) && ($deactivationMin === 0 || strtotime("+ $deactivationMin minutes", $manual['timeStamp']) > time());

        if ($isManualActive) {
            $positions['BlindLevel'] = $manual['blindLevel'];
        } else {
            $positions['BlindLevel'] =
                $this->ReadPropertyBoolean(self::PROP_ACTIVATEDINDIVIDUALDAYLEVELS) ? $this->ReadPropertyFloat(self::PROP_DAYBLINDLEVEL)
                    : $this->profileBlindLevel['MinValue'];
        }

        // Lamellen (unabhängig von manueller Behanghöhe laut Original-Logik)
        $positions['SlatsLevel'] =
            $this->ReadPropertyBoolean(self::PROP_ACTIVATEDINDIVIDUALDAYLEVELS) ? $this->ReadPropertyFloat(self::PROP_DAYSLATSLEVEL)
                : ($this->profileSlatsLevel['MinValue'] ?? null);

        return $positions;
    }

    private function calculateNightPosition(array $positions): array
    {
        if ($this->ReadPropertyBoolean(self::PROP_ACTIVATEDINDIVIDUALNIGHTLEVELS)) {
            $positions['BlindLevel'] = $this->ReadPropertyFloat(self::PROP_NIGHTBLINDLEVEL);
            $positions['SlatsLevel'] = $this->ReadPropertyFloat(self::PROP_NIGHTSLATSLEVEL);
        } else {
            $positions['BlindLevel'] = $this->profileBlindLevel['MaxValue'];
            $positions['SlatsLevel'] = $this->profileSlatsLevel['MaxValue'] ?? null;
        }

        return $positions;
    }

    private function applyShadowingLogic(float $currentBlindLevel, array $positionsNew, string $Hinweis): array
    {
        $this->shadowingReason         = '';
        $this->shadowingBrightnessInfo = '';
        $this->shadowingHeatInfo       = '';
        $brightnessInfo                = '';
        $heatInfo                      = '';

        // 1. Beschattung nach Sonnenstand
        $positionsShadowingBySun = $this->getPositionsOfShadowingBySunPosition($currentBlindLevel);
        $sunBrightnessInfo       = $this->shadowingBrightnessInfo; // entscheidungsrelevante Helligkeit der Sonnenstand-Beschattung
        $sunHeatInfo             = $this->shadowingHeatInfo;       // ggf. Hitze-/Wärmeschutz-Hinweis der Sonnenstand-Beschattung
        $this->shadowingBrightnessInfo = '';
        $this->shadowingHeatInfo       = '';
        if ($positionsShadowingBySun !== null) {
            $positionsNew = $this->mergePositions($positionsNew, $positionsShadowingBySun);

            if ($positionsNew['BlindLevel'] === $positionsShadowingBySun['BlindLevel']) {
                $Hinweis        = 'Beschattung nach Sonnenstand';
                $brightnessInfo = $sunBrightnessInfo;
                $heatInfo       = $sunHeatInfo;
            }
        }

        // 2. Beschattung nach Helligkeit
        $positionsShadowingBrightness  = $this->getPositionsOfShadowingByBrightness($currentBlindLevel);
        $brightnessShadowingInfo       = $this->shadowingBrightnessInfo; // entscheidungsrelevante Helligkeit der Helligkeits-Beschattung
        $this->shadowingBrightnessInfo = '';
        if ($positionsShadowingBrightness !== null) {
            $positionsNew = $this->mergePositions($positionsNew, $positionsShadowingBrightness);

            if ($positionsNew['BlindLevel'] === $positionsShadowingBrightness['BlindLevel']) {
                $Hinweis        = 'Beschattung nach Helligkeit';
                $brightnessInfo = $brightnessShadowingInfo;
            }
        }

        // Beschattung wurde berechnet, hat aber die Zielposition nicht verändert (Basisposition bereits restriktiver)
        if (($positionsShadowingBySun !== null || $positionsShadowingBrightness !== null) && $this->shadowingReason === '') {
            $this->shadowingReason = 'Beschattungsposition nicht restriktiver als die Basisposition';
        }

        return ['positions' => $positionsNew, 'hint' => $Hinweis, 'reason' => $this->shadowingReason, 'brightnessInfo' => $brightnessInfo, 'heatInfo' => $heatInfo];
    }

    /**
     * Hängt einen Grund an, warum eine aktivierte Beschattung nicht greift.
     */
    private function addShadowingReason(string $reason): void
    {
        $this->shadowingReason = $this->shadowingReason === '' ? $reason : $this->shadowingReason . '; ' . $reason;
    }

    /**
     * Führt die berechnete Basisposition mit einer Schattenposition zusammen.
     * Dabei wird je nach Profilrichtung (Reversed) der restriktivere Wert (Min oder Max) gewählt.
     *
     * @param array $current Die aktuell berechnete Zielposition (z.B. Tag/Nacht).
     * @param array $shadowing Die ermittelte Schattenposition.
     * @return array Das kombinierte Positions-Array.
     */
    private function mergePositions(array $current, array $shadowing): array
    {
        if ($this->isMinMaxReversed($this->profileBlindLevel['MinValue'], $this->profileBlindLevel['MaxValue'])) {
            $current['BlindLevel'] = min($current['BlindLevel'], $shadowing['BlindLevel']);
        } else {
            $current['BlindLevel'] = max($current['BlindLevel'], $shadowing['BlindLevel']);
        }

        if (IPS_VariableExists($this->ReadPropertyInteger(self::PROP_SLATSLEVELID))) {
            if ($this->isMinMaxReversed($this->profileSlatsLevel['MinValue'], $this->profileSlatsLevel['MaxValue'])) {
                $current['SlatsLevel'] = min($current['SlatsLevel'], $shadowing['SlatsLevel']);
            } else {
                $current['SlatsLevel'] = max($current['SlatsLevel'], $shadowing['SlatsLevel']);
            }
        }
        return $current;
    }

    /**
     * Wendet die Kontaktlogik (Fensterkontakte, Notfallkontakt) auf die berechneten Positionen an.
     *
     * @param array  $positionsAct         Die aktuellen Positionen des Aktors.
     * @param array  $positionsNew         Die bisher berechneten Zielpositionen (z.B. aus Tag/Nacht/Schatten).
     * @param int    $deactivationTimeAuto Die aktuelle automatische Deaktivierungszeit in Sekunden.
     * @param bool   $bNoMove              Status, ob aktuell eine Bewegungssperre vorliegt.
     * @param string $Hinweis              Der bisherige Status-Hinweis für das Logging.
     *
     * @return array{positions: array, deactivationTimeAuto: int, bNoMove: bool, hint: string, bEmergency: bool, trace: string}
     * @throws \JsonException
     */
    private function applyContactLogic(array $positionsAct, array $positionsNew, int $deactivationTimeAuto, bool $bNoMove, string $Hinweis): array
    {
        $this->Logger_Dbg(
            __FUNCTION__,
            sprintf(
                'positionsAct: %s, positionsNew: %s, deactivationTimeAuto: %d, bNoMove: %d, hint: %s',
                json_encode($positionsAct, JSON_THROW_ON_ERROR),
                json_encode($positionsNew, JSON_THROW_ON_ERROR),
                $deactivationTimeAuto,
                (int)$bNoMove,
                $Hinweis
            )
        );

        $levelContactEmergency      = $this->getLevelEmergencyContact();
        $positionsContactOpenBlind  = $this->getPositionsOfOpenBlindContact();
        $positionsContactCloseBlind = $this->getPositionsOfCloseBlindContact();
        $openingTraceLabel          = 'Kontakt offen';

        // Konfigurationsstatus der Kontakte (für den immer ausgegebenen Trace)
        $emergencyConfigured = IPS_VariableExists($this->ReadPropertyInteger(self::PROP_EMERGENCYCONTACTID));
        $openConfigured      = $this->getDefinedContacts('PROP_CONTACTOPEN', 'PROP_CONTACTOPENLEVEL', 'PROP_CONTACTOPENSLATSLEVEL') !== [];
        $closeConfigured     = $this->getDefinedContacts('PROP_CONTACTCLOSE', 'PROP_CONTACTCLOSELEVEL', 'PROP_CONTACTCLOSESLATSLEVEL') !== [];

        // 1. Notfall hat höchste Priorität
        if ($levelContactEmergency !== null) {
            if (!$this->dryRun) {
                $this->WriteAttributeBoolean(self::ATTR_CONTACT_OPEN, true);
            }
            $this->Logger_Dbg(
                __FUNCTION__,
                sprintf('NOTFALL: Kontakt geöffnet (posAct: %.2f, posNew: %.2f)', $positionsAct['BlindLevel'], $levelContactEmergency)
            );

            $positionsNew['BlindLevel'] = $levelContactEmergency;
            return [
                'positions'            => $positionsNew,
                'deactivationTimeAuto' => 0,
                'bNoMove'              => false,
                'hint'                 => 'Notfallkontakt offen',
                'bEmergency'           => true,
                'trace'                => sprintf('Notfallkontakt offen -> %s', $this->describeTargetPositions($positionsNew))
            ];
        }

        // 2. Priorität zwischen Öffnen/Schließen klären
        if ($positionsContactOpenBlind !== null && $positionsContactCloseBlind !== null) {
            if ($this->ReadPropertyBoolean(self::PROP_CONTACTSTOCLOSEHAVEHIGHERPRIORITY)) {
                $positionsContactOpenBlind = null;
            } else {
                $positionsContactCloseBlind = null;
            }
        }

        // 3. Kontakte prüfen
        $contactTrace = '';
        if ($positionsContactOpenBlind !== null) {
            $checkResult = $this->checkContactLimit($positionsAct, $positionsNew, $positionsContactOpenBlind, true);
            if ($checkResult['modified']) {
                $bNoMove      = false;
                $positionsNew = $checkResult['positions'];
                $Hinweis      = $openingTraceLabel;
                if ($checkResult['resetDeactivation']) {
                    $deactivationTimeAuto = 0;
                }
                if (!$this->dryRun) {
                    $this->WriteAttributeBoolean(self::ATTR_CONTACT_OPEN, true);
                }
                $this->Logger_Dbg(__FUNCTION__, 'Kontakt geöffnet (Open-Logik angewendet)');
                $contactTrace = sprintf('%s -> %s', $openingTraceLabel, $this->describeTargetPositions($positionsNew));
            } else {
                // Kontakt ist offen, das Öffnungslevel ist aber nicht offener als die bereits ermittelte
                // Zielposition - der Kontakt hat daher keine Wirkung.
                $contactTrace = sprintf(
                    '%s, aber Zielposition bereits offen genug (%s) -> keine Änderung',
                    $openingTraceLabel,
                    $this->describeTargetPositions($positionsNew)
                );
            }
        } elseif ($positionsContactCloseBlind !== null) {
            $checkResult = $this->checkContactLimit($positionsAct, $positionsNew, $positionsContactCloseBlind, false);
            if ($checkResult['modified']) {
                $bNoMove              = false;
                $positionsNew         = $checkResult['positions'];
                $Hinweis              = 'Kontakt offen';
                $deactivationTimeAuto = 0;
                if (!$this->dryRun) {
                    $this->WriteAttributeBoolean(self::ATTR_CONTACT_OPEN, true);
                }
                $this->Logger_Dbg(__FUNCTION__, 'Kontakt geöffnet (Close-Logik angewendet)');
                $contactTrace = sprintf('Kontakt offen -> %s', $this->describeTargetPositions($positionsNew));
            } else {
                // Kontakt ist offen, das Schließlevel ist aber nicht restriktiver als die bereits
                // ermittelte Zielposition - der Kontakt hat daher keine Wirkung.
                $contactTrace = sprintf(
                    'Kontakt offen, aber Zielposition bereits geschlossen genug (%s) -> keine Änderung',
                    $this->describeTargetPositions($positionsNew)
                );
            }
        } elseif ($this->ReadAttributeBoolean(self::ATTR_CONTACT_OPEN)) {
            // Reset, wenn kein Kontakt mehr aktiv ist
            $deactivationTimeAuto = 0;
            if (!$this->dryRun) {
                $this->WriteAttributeBoolean(self::ATTR_CONTACT_OPEN, false);
            }
        }

        // Fallback-Beschreibung, wenn kein offener Kontakt die Position beeinflusst hat
        if ($contactTrace === '') {
            if (!$emergencyConfigured && !$openConfigured && !$closeConfigured) {
                $contactTrace = 'nicht konfiguriert';
            } else {
                $contactTrace = 'kein Kontakt offen';
            }
        }

        $result = [
            'positions'            => $positionsNew,
            'deactivationTimeAuto' => $deactivationTimeAuto,
            'bNoMove'              => $bNoMove,
            'hint'                 => $Hinweis,
            'bEmergency'           => false,
            'trace'                => $contactTrace
        ];

        $this->Logger_Dbg(__FUNCTION__, 'Result: ' . json_encode($result, JSON_THROW_ON_ERROR));
        return $result;
    }

    private function checkContactLimit(array $currentPositions, array $targetPositions, array $contactLimit, bool $isOpeningContact): array
    {
        $modified          = false;
        $resetDeactivation = false;
        $reversed          = $this->isMinMaxReversed($this->profileBlindLevel['MinValue'], $this->profileBlindLevel['MaxValue']);

        // Bestimmen, ob das Kontakt-Limit eine Untergrenze oder Obergrenze darstellt
        $isLowerLimit = ($isOpeningContact && !$reversed) || (!$isOpeningContact && $reversed);

        $shouldModifyBlind = $isLowerLimit
            ? ($contactLimit['BlindLevel'] < $targetPositions['BlindLevel'])
            : ($contactLimit['BlindLevel'] > $targetPositions['BlindLevel']);

        if ($shouldModifyBlind) {
            $targetPositions['BlindLevel'] = $contactLimit['BlindLevel'];
            $modified                      = true;

            // DeactivationTime nur resetten, wenn wir den Aktor auch wirklich "wegbewegen" müssen
            // Im Originalcode war das bei 'Open' davon abhängig, ob das Limit > Act ist (bei reversed)
            if ($isOpeningContact) {
                $resetDeactivation = $reversed
                    ? ($contactLimit['BlindLevel'] > $currentPositions['BlindLevel'])
                    : ($contactLimit['BlindLevel']
                                                                                         < $currentPositions['BlindLevel']);
            } else {
                // Bei 'Close' wird laut Originalcode immer ein Reset durchgeführt
                $resetDeactivation = true;
            }
        }

        // Prüflogik für Lamellen (falls vorhanden)
        if (IPS_VariableExists($this->ReadPropertyInteger(self::PROP_SLATSLEVELID))) {
            $reversedSlats = $this->isMinMaxReversed($this->profileSlatsLevel['MinValue'], $this->profileSlatsLevel['MaxValue']);
            // Bestimmen, ob das Lamellen-Limit eine Untergrenze oder Obergrenze darstellt
            $isLowerLimitSlats = ($isOpeningContact && !$reversedSlats) || (!$isOpeningContact && $reversedSlats);

            $shouldModifySlats = $isLowerLimitSlats
                ? ($contactLimit['SlatsLevel'] < $targetPositions['SlatsLevel'])
                : ($contactLimit['SlatsLevel'] > $targetPositions['SlatsLevel']);

            if ($shouldModifySlats) {
                $targetPositions['SlatsLevel'] = $contactLimit['SlatsLevel'];
                $modified                      = true;
                $resetDeactivation             = true; // Bei Lamellenänderung wurde im Original deactivationTimeAuto immer auf 0 gesetzt
            }
        }

        return ['positions' => $targetPositions, 'modified' => $modified, 'resetDeactivation' => $resetDeactivation];
    }

    private function calculateNormalizedLevel(float $position, array $profile): int
    {
        $min   = $profile['MinValue'];
        $max   = $profile['MaxValue'];
        $range = $max - $min;

        if (abs($range) < PHP_FLOAT_EPSILON) {
            return 0;
        }

        $position = $this->clampToProfile($position, $profile);

        // Diese Zeile deckt beide Richtungen (reversed j/n) korrekt ab.
        return (int)round((($position - $min) / $range) * 100);
    }

    private function calculateProfilePositionByPercent(float $percent, array $profile): float
    {
        return $this->clampToProfile(
            $profile['MinValue'] + (($profile['MaxValue'] - $profile['MinValue']) * ($percent / 100)),
            $profile
        );
    }

    private function getModuleVersion(): string
    {
        $library = json_decode(
            file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'library.json'),
            true,
            512,
            JSON_THROW_ON_ERROR
        );
        return sprintf('%s.%s (%s)', $library['version'], $library['build'], date(DATE_ATOM, $library['date']));
    }

    /**
     * Setzt den Status der manuellen Bewegung zurück und markiert den aktuellen Zeitpunkt als automatische Bewegung.
     *
     * @return void
     * @throws \JsonException
     */
    private function resetManualMovement(): void
    {
        if ($this->dryRun) {
            return;
        }

        $this->WriteAttributeString(
            self::ATTR_MANUALMOVEMENT,
            json_encode(['timeStamp' => null, 'blindLevel' => null, 'slatsLevel' => null], JSON_THROW_ON_ERROR)
        );

        // Timestamp der Automatik merken (sonst wird die Bewegung später als manuelle Bewegung erkannt)
        $this->WriteAttributeInteger(self::ATTR_TIMESTAMP_AUTOMATIC, time());

        $this->Logger_Inf(sprintf('\'%s\' bewegt sich nun wieder automatisch.', $this->objectName));
    }

    private function RegisterProperties(): void
    {
        $this->RegisterPropertyInteger(self::PROP_BLINDLEVELID, 0);
        $this->RegisterPropertyInteger(self::PROP_SLATSLEVELID, 0);

        //week plan
        $this->RegisterPropertyInteger(self::PROP_WEEKLYTIMETABLEEVENTID, 0);
        $this->RegisterPropertyInteger(self::PROP_HOLIDAYINDICATORID, 0);
        $this->RegisterPropertyInteger(self::PROP_DAYUSEDWHENHOLIDAY, 0);
        $this->RegisterPropertyInteger(self::PROP_WAKEUPTIMEID, 0);
        $this->RegisterPropertyInteger(self::PROP_WAKEUPTIMEOFFSET, 0);
        $this->RegisterPropertyInteger(self::PROP_BEDTIMEID, 0);
        $this->RegisterPropertyInteger(self::PROP_BEDTIMEOFFSET, 0);
        $this->RegisterPropertyBoolean(self::PROP_ACTIVATEDINDIVIDUALDAYLEVELS, false);
        $this->RegisterPropertyFloat(self::PROP_DAYBLINDLEVEL, 0);
        $this->RegisterPropertyFloat(self::PROP_DAYSLATSLEVEL, 0);
        $this->RegisterPropertyBoolean(self::PROP_ACTIVATEDINDIVIDUALNIGHTLEVELS, false);
        $this->RegisterPropertyFloat(self::PROP_NIGHTBLINDLEVEL, 0);
        $this->RegisterPropertyFloat(self::PROP_NIGHTSLATSLEVEL, 0);

        //day detection
        $this->RegisterPropertyInteger(self::PROP_ISDAYINDICATORID, 0);
        $this->RegisterPropertyInteger(self::PROP_BRIGHTNESSID, 0);
        $this->RegisterPropertyInteger(self::PROP_BRIGHTNESSAVGMINUTES, 0);
        $this->RegisterPropertyInteger(self::PROP_BRIGHTNESSTHRESHOLDID, 0);

        //overruling day times
        $this->RegisterPropertyInteger(self::PROP_DAYSTARTID, 0);
        $this->RegisterPropertyInteger(self::PROP_DAYENDID, 0);

        $this->RegisterPropertyInteger(self::PROP_DELAYTIMEDAYNIGHTCHANGE, 0);
        $this->RegisterPropertyBoolean(self::PROP_DELAYTIMEDAYNIGHTCHANGEISRANDOMLY, false);

        //shadowing according to sun position
        $this->RegisterPropertyInteger(self::PROP_ACTIVATORIDSHADOWINGBYSUNPOSITION, 0);
        $this->RegisterPropertyInteger(self::PROP_AZIMUTHID, 0);
        $this->RegisterPropertyInteger(self::PROP_ALTITUDEID, 0);
        $this->RegisterPropertyFloat(self::PROP_AZIMUTHFROM, 0);
        $this->RegisterPropertyFloat(self::PROP_AZIMUTHTO, 360);
        $this->RegisterPropertyFloat(self::PROP_ALTITUDEFROM, 0);
        $this->RegisterPropertyFloat(self::PROP_ALTITUDETO, 90);
        $this->RegisterPropertyInteger(self::PROP_BRIGHTNESSIDSHADOWINGBYSUNPOSITION, 0);
        $this->RegisterPropertyInteger(self::PROP_BRIGHTNESSAVGMINUTESSHADOWINGBYSUNPOSITION, 0);
        $this->RegisterPropertyInteger(self::PROP_BRIGHTNESSTHRESHOLDIDSHADOWINGBYSUNPOSITION, 0);
        $this->RegisterPropertyInteger(self::PROP_TEMPERATUREIDSHADOWINGBYSUNPOSITION, 0);
        $this->RegisterPropertyFloat('LowSunPositionAltitude', 0);
        $this->RegisterPropertyFloat('HighSunPositionAltitude', 0);
        $this->RegisterPropertyFloat(self::PROP_LOWSUNPOSITIONBLINDLEVEL, 0);
        $this->RegisterPropertyFloat(self::PROP_HIGHSUNPOSITIONBLINDLEVEL, 0);
        $this->RegisterPropertyFloat(self::PROP_LOWSUNPOSITIONSLATSLEVEL, 0);
        $this->RegisterPropertyFloat(self::PROP_HIGHSUNPOSITIONSLATSLEVEL, 0);
        $this->RegisterPropertyInteger(self::PROP_DEPTHSUNLIGHT, 0);
        $this->RegisterPropertyInteger(self::PROP_WINDOWORIENTATION, 0);
        $this->RegisterPropertyInteger(self::PROP_WINDOWSSLOPE, 90);
        $this->RegisterPropertyInteger(self::PROP_WINDOWSHEIGHT, 0);
        $this->RegisterPropertyInteger(self::PROP_PARAPETHEIGHT, 0);
        $this->RegisterPropertyFloat(self::PROP_MINIMUMSHADERELEVANTBLINDLEVEL, 0);
        $this->RegisterPropertyFloat(self::PROP_HALFSHADERELEVANTBLINDLEVEL, 0);
        $this->RegisterPropertyFloat(self::PROP_MAXIMUMSHADERELEVANTBLINDLEVEL, 0);
        $this->RegisterPropertyFloat(self::PROP_MINIMUMSHADERELEVANTSLATSLEVEL, 0);
        $this->RegisterPropertyFloat(self::PROP_MAXIMUMSHADERELEVANTSLATSLEVEL, 0);

        //shadowing according to brightness
        $this->RegisterPropertyInteger(self::PROP_ACTIVATORIDSHADOWINGBRIGHTNESS, 0);
        $this->RegisterPropertyInteger(self::PROP_BRIGHTNESSIDSHADOWINGBRIGHTNESS, 0);
        $this->RegisterPropertyInteger(self::PROP_BRIGHTNESSAVGMINUTESSHADOWINGBRIGHTNESS, 0);
        $this->RegisterPropertyInteger(self::PROP_THRESHOLDIDLESSBRIGHTNESS, 0);
        $this->RegisterPropertyFloat(self::PROP_BLINDLEVELLESSBRIGHTNESSSHADOWINGBRIGHTNESS, 0);
        $this->RegisterPropertyFloat(self::PROP_SLATSLEVELLESSBRIGHTNESSSHADOWINGBRIGHTNESS, 0);
        $this->RegisterPropertyInteger(self::PROP_THRESHOLDIDHIGHBRIGHTNESS, 0);
        $this->RegisterPropertyFloat(self::PROP_BLINDLEVELHIGHBRIGHTNESSSHADOWINGBRIGHTNESS, 0);
        $this->RegisterPropertyFloat(self::PROP_SLATSLEVELHIGHBRIGHTNESSSHADOWINGBRIGHTNESS, 0);

        //contacts close
        $this->RegisterPropertyInteger(self::PROP_CONTACTCLOSE1ID, 0);
        $this->RegisterPropertyInteger(self::PROP_CONTACTCLOSE2ID, 0);
        $this->RegisterPropertyFloat(self::PROP_CONTACTCLOSELEVEL1, 0);
        $this->RegisterPropertyFloat(self::PROP_CONTACTCLOSELEVEL2, 0);
        $this->RegisterPropertyFloat(self::PROP_CONTACTCLOSESLATSLEVEL1, 0);
        $this->RegisterPropertyFloat(self::PROP_CONTACTCLOSESLATSLEVEL2, 0);
        $this->RegisterPropertyBoolean(self::PROP_CONTACTSTOCLOSEHAVEHIGHERPRIORITY, false);

        //contacts close - value groups (up to three positions per contact) + per-contact debounce
        //Position 1 nutzt die oben registrierten ContactCloseLevel{i}/ContactCloseSlatsLevel{i}
        for ($i = 1; $i <= 2; $i++) {
            $this->RegisterPropertyInteger($this->closeDelayProp($i), 0);
            for ($j = 1; $j <= 3; $j++) {
                $this->RegisterPropertyString($this->closeValuesProp($i, $j), '');
            }
            for ($j = 2; $j <= 3; $j++) {
                $this->RegisterPropertyFloat($this->closeLevelProp($i, $j), 0);
                $this->RegisterPropertyFloat($this->closeSlatsProp($i, $j), 0);
            }
        }

        //contacts open
        $this->RegisterPropertyInteger(self::PROP_CONTACTOPEN1ID, 0);
        $this->RegisterPropertyInteger(self::PROP_CONTACTOPEN2ID, 0);
        $this->RegisterPropertyFloat(self::PROP_CONTACTOPENLEVEL1, 0);
        $this->RegisterPropertyFloat(self::PROP_CONTACTOPENLEVEL2, 0);
        $this->RegisterPropertyFloat(self::PROP_CONTACTOPENSLATSLEVEL1, 0);
        $this->RegisterPropertyFloat(self::PROP_CONTACTOPENSLATSLEVEL2, 0);

        //contacts open - value groups (up to three positions per contact) + per-contact debounce
        //Position 1 nutzt die oben registrierten ContactOpenLevel{i}/ContactOpenSlatsLevel{i}
        for ($i = 1; $i <= 2; $i++) {
            $this->RegisterPropertyInteger($this->openDelayProp($i), 0);
            for ($j = 1; $j <= 3; $j++) {
                $this->RegisterPropertyString($this->openValuesProp($i, $j), '');
            }
            for ($j = 2; $j <= 3; $j++) {
                $this->RegisterPropertyFloat($this->openLevelProp($i, $j), 0);
                $this->RegisterPropertyFloat($this->openSlatsProp($i, $j), 0);
            }
        }

        //emergency contact
        $this->RegisterPropertyInteger(self::PROP_EMERGENCYCONTACTID, 0);


        $this->RegisterPropertyInteger(self::PROP_UPDATEINTERVAL, 1);
        $this->RegisterPropertyInteger(self::PROP_DEACTIVATIONAUTOMATICMOVEMENT, 20);
        $this->RegisterPropertyInteger(self::PROP_DEACTIVATIONMANUALMOVEMENT, 120);
        $this->RegisterPropertyFloat(self::PROP_MINMOVEMENT, 5.0);
        $this->RegisterPropertyFloat(self::PROP_MINMOVEMENTATENDPOSITION, 2.5);
        $this->RegisterPropertyBoolean(self::PROP_SHOWNOTUSEDELEMENTS, false);
        $this->RegisterPropertyBoolean(self::PROP_WRITELASTDECISION, false);
        $this->RegisterPropertyBoolean(self::PROP_WRITEDECISIONTRACE, false);
        $this->RegisterPropertyBoolean('WriteLogInformationToIPSLogger', false);
        $this->RegisterPropertyBoolean('WriteDebugInformationToLogfile', false);
        $this->RegisterPropertyBoolean('WriteDebugInformationToIPSLogger', false);
    }

    private function RegisterReferences(): void
    {
        $objectIDs = [
            $this->ReadPropertyInteger(self::PROP_BLINDLEVELID),
            $this->ReadPropertyInteger(self::PROP_WEEKLYTIMETABLEEVENTID),
            $this->ReadPropertyInteger(self::PROP_HOLIDAYINDICATORID),
            $this->ReadPropertyInteger(self::PROP_WAKEUPTIMEID),
            $this->ReadPropertyInteger(self::PROP_BEDTIMEID),

            $this->ReadPropertyInteger(self::PROP_ISDAYINDICATORID),
            $this->ReadPropertyInteger(self::PROP_BRIGHTNESSID),
            $this->ReadPropertyInteger(self::PROP_BRIGHTNESSTHRESHOLDID),

            $this->ReadPropertyInteger(self::PROP_DAYSTARTID),
            $this->ReadPropertyInteger(self::PROP_DAYENDID),

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
            $this->ReadPropertyInteger(self::PROP_TEMPERATUREIDSHADOWINGBYSUNPOSITION),

            $this->ReadPropertyInteger(self::PROP_ACTIVATORIDSHADOWINGBRIGHTNESS),
            $this->ReadPropertyInteger(self::PROP_BRIGHTNESSIDSHADOWINGBRIGHTNESS),
            $this->ReadPropertyInteger(self::PROP_THRESHOLDIDLESSBRIGHTNESS),
            $this->ReadPropertyInteger(self::PROP_THRESHOLDIDHIGHBRIGHTNESS)
        ];

        foreach ($this->GetReferenceList() as $ref) {
            $this->UnregisterReference($ref);
        }

        foreach ($objectIDs as $id) {
            if ($id >= 10000) {
                $this->RegisterReference($id);
            }
        }
    }

    private function RegisterMessages(): void
    {
        $objectIDs = [
            self::PROP_WEEKLYTIMETABLEEVENTID                      => $this->ReadPropertyInteger(self::PROP_WEEKLYTIMETABLEEVENTID),
            self::PROP_HOLIDAYINDICATORID                          => $this->ReadPropertyInteger(self::PROP_HOLIDAYINDICATORID),
            self::PROP_BRIGHTNESSID                                => $this->ReadPropertyInteger(self::PROP_BRIGHTNESSID),
            self::PROP_BRIGHTNESSTHRESHOLDID                       => $this->ReadPropertyInteger(self::PROP_BRIGHTNESSTHRESHOLDID),
            self::PROP_ISDAYINDICATORID                            => $this->ReadPropertyInteger(self::PROP_ISDAYINDICATORID),
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
            self::PROP_TEMPERATUREIDSHADOWINGBYSUNPOSITION         => $this->ReadPropertyInteger(self::PROP_TEMPERATUREIDSHADOWINGBYSUNPOSITION),
            self::PROP_ACTIVATORIDSHADOWINGBRIGHTNESS              => $this->ReadPropertyInteger(self::PROP_ACTIVATORIDSHADOWINGBRIGHTNESS),
            self::PROP_BRIGHTNESSIDSHADOWINGBRIGHTNESS             => $this->ReadPropertyInteger(self::PROP_BRIGHTNESSIDSHADOWINGBRIGHTNESS),
            self::PROP_THRESHOLDIDHIGHBRIGHTNESS                   => $this->ReadPropertyInteger(self::PROP_THRESHOLDIDHIGHBRIGHTNESS),
            self::PROP_THRESHOLDIDLESSBRIGHTNESS                   => $this->ReadPropertyInteger(self::PROP_THRESHOLDIDLESSBRIGHTNESS)
        ];

        $objectIDs_RequiredAction = [
            self::PROP_BLINDLEVELID => $this->ReadPropertyInteger(self::PROP_BLINDLEVELID),
            self::PROP_SLATSLEVELID => $this->ReadPropertyInteger(self::PROP_SLATSLEVELID),
        ];
        foreach ($this->GetMessageList() as $senderId => $msgs) {
            foreach ($msgs as $msg) {
                $this->UnregisterMessage($senderId, $msg);
            }
        }

        foreach ($objectIDs as $id) {
            if (IPS_VariableExists($id)) {
                $this->RegisterMessage($id, VM_UPDATE);
            } elseif (IPS_EventExists($id)) {
                $this->RegisterMessage($id, EM_UPDATE);
            }
        }

        foreach ($objectIDs_RequiredAction as $id) {
            if (IPS_VariableExists($id)) {
                $this->RegisterMessage($id, VM_CHANGEPROFILEACTION);
            }
        }
    }

    private function RegisterAttributes(): void
    {
        $this->RegisterAttributeInteger(self::ATTR_TIMESTAMP_AUTOMATIC, 0);
        $this->RegisterAttributeString(
            self::ATTR_MANUALMOVEMENT,
            json_encode(['timeStamp' => null, 'blindLevel' => null, 'slatsLevel' => null], JSON_THROW_ON_ERROR)
        );
        $this->RegisterAttributeInteger('AttrTimeStampIsDayChange', 0);
        $this->RegisterAttributeBoolean('AttrIsDay', false);
        $this->RegisterAttributeBoolean(self::ATTR_CONTACT_OPEN, false);
        $this->RegisterAttributeString(
            self::ATTR_LASTMOVE . self::PROP_BLINDLEVELID,
            json_encode(['timeStamp' => null, 'percentClose' => null, 'hint' => null], JSON_THROW_ON_ERROR)
        );

        $this->RegisterAttributeString(
            self::ATTR_LASTMOVE . self::PROP_SLATSLEVELID,
            json_encode(['timeStamp' => null, 'percentClose' => null, 'hint' => null], JSON_THROW_ON_ERROR)
        );
        $this->RegisterAttributeInteger(self::ATTR_DAYTIME_CHANGE_TIME, 0);
        $this->RegisterAttributeBoolean(self::ATTR_LAST_ISDAYBYTIMESCHEDULE, false);
    }

    private function RegisterVariables(): void
    {
        $this->RegisterVariableBoolean(self::VAR_IDENT_ACTIVATED, $this->Translate('Activated'), ['PRESENTATION' => VARIABLE_PRESENTATION_SWITCH]);
        $this->RegisterVariableString(self::VAR_IDENT_LAST_MESSAGE, $this->Translate('Last Message'));

        // Statusvariable "Letzte Entscheidung" nur auf Wunsch anlegen; andernfalls eine evtl. vorhandene wieder entfernen
        if ($this->ReadPropertyBoolean(self::PROP_WRITELASTDECISION)) {
            $this->RegisterVariableString(self::VAR_IDENT_LAST_DECISION, $this->Translate('Last Decision'));
        } elseif (@$this->GetIDForIdent(self::VAR_IDENT_LAST_DECISION) !== false) {
            $this->UnregisterVariable(self::VAR_IDENT_LAST_DECISION);
        }

        // Statusvariable "Letztes Ablaufprotokoll" (HTML) nur auf Wunsch anlegen; andernfalls eine evtl. vorhandene wieder entfernen.
        // Die Presentation "Web Content" rendert den HTML-Inhalt in der Visualisierung formatiert (Nachfolger des Profils ~HTMLBox).
        if ($this->ReadPropertyBoolean(self::PROP_WRITEDECISIONTRACE)) {
            $this->RegisterVariableString(self::VAR_IDENT_DECISION_TRACE, $this->Translate('Last Decision Trace'), ['PRESENTATION' => VARIABLE_PRESENTATION_WEB_CONTENT]);
        } elseif (@$this->GetIDForIdent(self::VAR_IDENT_DECISION_TRACE) !== false) {
            $this->UnregisterVariable(self::VAR_IDENT_DECISION_TRACE);
        }

        $this->EnableAction(self::VAR_IDENT_ACTIVATED);
    }

    private function SetInstanceStatusAndTimerEvent(): void
    {
        if ($ret = $this->checkVariableId(
            self::PROP_BLINDLEVELID,
            false,
            [VARIABLETYPE_INTEGER, VARIABLETYPE_FLOAT],
            true,
            self::STATUS_INST_BLIND_LEVEL_ID_IS_INVALID
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
            self::PROP_SLATSLEVELID,
            true,
            [VARIABLETYPE_INTEGER, VARIABLETYPE_FLOAT],
            true,
            self::STATUS_INST_SLATSLEVEL_ID_IS_INVALID
        )) {
            $this->SetStatus($ret);
            return;
        }

        if (IPS_VariableExists($this->ReadPropertyInteger(self::PROP_SLATSLEVELID))) {
            if (!$this->checkActionOfStatusVariable(self::PROP_SLATSLEVELID)) {
                $this->SetStatus(self::STATUS_INST_SLATSLEVEL_ID_IS_INVALID);
                return;
            }
            if (!$this->checkEmulateStatusOfVariableAction(self::PROP_SLATSLEVELID)) {
                $this->SetStatus(self::STATUS_INST_SLATS_LEVEL_IS_EMULATED);
                return;
            }
        }

        if ($ret = $this->checkEventId(self::PROP_WEEKLYTIMETABLEEVENTID, false, EVENTTYPE_SCHEDULE, self::STATUS_INST_TIMETABLE_ID_IS_INVALID)) {
            $this->SetStatus($ret);
            return;
        }

        if ($ret = $this->checkVariableId(
            self::PROP_WAKEUPTIMEID,
            true,
            [VARIABLETYPE_STRING],
            false,
            self::STATUS_INST_WAKEUPTIME_ID_IS_INVALID
        )) {
            $this->SetStatus($ret);
            return;
        }

        if ($ret = $this->checkVariableId(
            self::PROP_BEDTIMEID,
            true,
            [VARIABLETYPE_STRING],
            false,
            self::STATUS_INST_SLEEPTIME_ID_IS_INVALID
        )) {
            $this->SetStatus($ret);
            return;
        }

        if ($ret = $this->checkVariableId(
            self::PROP_HOLIDAYINDICATORID,
            true,
            [VARIABLETYPE_BOOLEAN],
            false,
            self::STATUS_INST_HOLYDAY_INDICATOR_ID_IS_INVALID
        )) {
            $this->SetStatus($ret);
            return;
        }

        if ($ret = $this->checkVariableId(
            self::PROP_BRIGHTNESSID,
            true,
            [VARIABLETYPE_INTEGER, VARIABLETYPE_FLOAT],
            false,
            self::STATUS_INST_BRIGHTNESS_ID_IS_INVALID
        )) {
            $this->SetStatus($ret);
            return;
        }

        if ($ret = $this->checkVariableId(
            self::PROP_DAYSTARTID,
            true,
            [VARIABLETYPE_STRING],
            false,
            self::STATUS_INST_DAYSTART_ID_IS_INVALID
        )) {
            $this->SetStatus($ret);
            return;
        }

        if ($ret = $this->checkVariableId(
            self::PROP_DAYENDID,
            true,
            [VARIABLETYPE_STRING],
            false,
            self::STATUS_INST_DAYEND_ID_IS_INVALID
        )) {
            $this->SetStatus($ret);
            return;
        }

        if ($ret = $this->checkVariableId(
            self::PROP_BRIGHTNESSTHRESHOLDID,
            true,
            [VARIABLETYPE_INTEGER, VARIABLETYPE_FLOAT],
            false,
            self::STATUS_INST_BRIGHTNESS_THRESHOLD_ID_IS_INVALID
        )) {
            $this->SetStatus($ret);
            return;
        }

        if ($ret = $this->checkVariableId(
            self::PROP_ISDAYINDICATORID,
            true,
            [VARIABLETYPE_BOOLEAN],
            false,
            self::STATUS_INST_ISDAY_INDICATOR_ID_IS_INVALID
        )) {
            $this->SetStatus($ret);
            return;
        }

        if ($ret = $this->checkVariableId(
            self::PROP_CONTACTOPEN1ID,
            true,
            [VARIABLETYPE_BOOLEAN, VARIABLETYPE_INTEGER, VARIABLETYPE_FLOAT, VARIABLETYPE_STRING],
            false,
            self::STATUS_INST_CONTACT1_ID_IS_INVALID
        )) {
            $this->SetStatus($ret);
            return;
        }

        if ($ret = $this->checkVariableId(
            self::PROP_CONTACTOPEN2ID,
            true,
            [VARIABLETYPE_BOOLEAN, VARIABLETYPE_INTEGER, VARIABLETYPE_FLOAT, VARIABLETYPE_STRING],
            false,
            self::STATUS_INST_CONTACT2_ID_IS_INVALID
        )) {
            $this->SetStatus($ret);
            return;
        }

        if ($ret = $this->checkVariableId(
            self::PROP_EMERGENCYCONTACTID,
            true,
            [VARIABLETYPE_BOOLEAN, VARIABLETYPE_INTEGER, VARIABLETYPE_FLOAT],
            false,
            self::STATUS_INST_EMERGENCY_CONTACT_ID_IS_INVALID
        )) {
            $this->SetStatus($ret);
            return;
        }

        if ($ret = $this->checkVariableId(
            self::PROP_ACTIVATORIDSHADOWINGBYSUNPOSITION,
            true,
            [VARIABLETYPE_BOOLEAN, VARIABLETYPE_INTEGER, VARIABLETYPE_FLOAT],
            false,
            self::STATUS_INST_ACTIVATORIDSHADOWINGBYSUNPOSITION_IS_INVALID
        )) {
            $this->SetStatus($ret);
            return;
        }

        if ($ret = $this->checkVariableId(
            self::PROP_AZIMUTHID,
            $this->ReadPropertyInteger(self::PROP_ACTIVATORIDSHADOWINGBYSUNPOSITION) < 10000,
            [VARIABLETYPE_FLOAT],
            false,
            self::STATUS_INST_AZIMUTHID_IS_INVALID
        )) {
            $this->SetStatus($ret);
            return;
        }

        if ($ret = $this->checkVariableId(
            self::PROP_ALTITUDEID,
            $this->ReadPropertyInteger(self::PROP_ACTIVATORIDSHADOWINGBYSUNPOSITION) < 10000,
            [VARIABLETYPE_FLOAT],
            false,
            self::STATUS_INST_ALTITUDEID_IS_INVALID
        )) {
            $this->SetStatus($ret);
            return;
        }

        if ($ret = $this->checkVariableId(
            self::PROP_BRIGHTNESSIDSHADOWINGBYSUNPOSITION,
            true,
            [VARIABLETYPE_INTEGER, VARIABLETYPE_FLOAT],
            false,
            self::STATUS_INST_BRIGTHNESSIDSHADOWINGBYSUNPOSITION_IS_INVALID
        )) {
            $this->SetStatus($ret);
            return;
        }

        if ($ret = $this->checkVariableId(
            self::PROP_BRIGHTNESSTHRESHOLDIDSHADOWINGBYSUNPOSITION,
            true,
            [VARIABLETYPE_INTEGER, VARIABLETYPE_FLOAT],
            false,
            self::STATUS_INST_BRIGHTNESSTHRESHOLDIDSHADOWINGBYSUNPOSITION_IS_INVALID
        )) {
            $this->SetStatus($ret);
            return;
        }

        if ($ret = $this->checkVariableId(
            self::PROP_TEMPERATUREIDSHADOWINGBYSUNPOSITION,
            true,
            [VARIABLETYPE_INTEGER, VARIABLETYPE_FLOAT],
            false,
            self::STATUS_INST_ROOMTEMPERATUREID_IS_INVALID
        )) {
            $this->SetStatus($ret);
            return;
        }

        if ($ret = $this->checkVariableId(
            self::PROP_ACTIVATORIDSHADOWINGBRIGHTNESS,
            true,
            [VARIABLETYPE_BOOLEAN, VARIABLETYPE_INTEGER, VARIABLETYPE_FLOAT],
            false,
            self::STATUS_INST_ACTIVATORIDSHADOWINGBRIGHTNESS_IS_INVALID
        )) {
            $this->SetStatus($ret);
            return;
        }

        if ($ret = $this->checkVariableId(
            self::PROP_BRIGHTNESSIDSHADOWINGBRIGHTNESS,
            true,
            [VARIABLETYPE_INTEGER, VARIABLETYPE_FLOAT],
            false,
            self::STATUS_INST_BRIGHTNESSIDSHADOWINGBRIGHTNESS_IS_INVALID
        )) {
            $this->SetStatus($ret);
            return;
        }

        if ($ret = $this->checkVariableId(
            self::PROP_THRESHOLDIDHIGHBRIGHTNESS,
            true,
            [VARIABLETYPE_INTEGER, VARIABLETYPE_FLOAT],
            false,
            self::STATUS_INST_THRESHOLDIDHIGHBRIGHTNESS_IS_INVALID
        )) {
            $this->SetStatus($ret);
            return;
        }

        if ($ret = $this->checkVariableId(
            self::PROP_THRESHOLDIDLESSBRIGHTNESS,
            true,
            [VARIABLETYPE_INTEGER, VARIABLETYPE_FLOAT],
            false,
            self::STATUS_INST_THRESHOLDIDLESSRIGHTNESS_IS_INVALID
        )) {
            $this->SetStatus($ret);
            return;
        }

        $this->profileBlindLevel = $this->GetPresentationInformation(self::PROP_BLINDLEVELID);
        if ($this->profileBlindLevel !== null) {
            $propertyBlindLevels = [
                self::PROP_DAYBLINDLEVEL,
                self::PROP_CONTACTOPENLEVEL1,
                self::PROP_CONTACTOPENLEVEL2,
                self::PROP_CONTACTCLOSELEVEL1,
                self::PROP_CONTACTCLOSELEVEL2,
                self::PROP_LOWSUNPOSITIONBLINDLEVEL,
                self::PROP_HIGHSUNPOSITIONBLINDLEVEL,
                self::PROP_MINIMUMSHADERELEVANTBLINDLEVEL,
                self::PROP_HALFSHADERELEVANTBLINDLEVEL,
                self::PROP_MAXIMUMSHADERELEVANTBLINDLEVEL,
                self::PROP_BLINDLEVELLESSBRIGHTNESSSHADOWINGBRIGHTNESS,
                self::PROP_BLINDLEVELHIGHBRIGHTNESSSHADOWINGBRIGHTNESS,
                self::PROP_SLATSLEVELLESSBRIGHTNESSSHADOWINGBRIGHTNESS,
                self::PROP_SLATSLEVELHIGHBRIGHTNESSSHADOWINGBRIGHTNESS
            ];

            // Öffnen-/Schließen-Kontakt-Wertegruppen (Höhen der Zusatzpositionen 2/3) mitprüfen
            for ($i = 1; $i <= 2; $i++) {
                for ($j = 2; $j <= 3; $j++) {
                    $propertyBlindLevels[] = $this->openLevelProp($i, $j);
                    $propertyBlindLevels[] = $this->closeLevelProp($i, $j);
                }
            }

            if ($this->ReadPropertyBoolean(self::PROP_ACTIVATEDINDIVIDUALDAYLEVELS)) {
                $propertyBlindLevels[] = self::PROP_DAYBLINDLEVEL;
            }

            if ($this->ReadPropertyBoolean(self::PROP_ACTIVATEDINDIVIDUALNIGHTLEVELS)) {
                $propertyBlindLevels[] = self::PROP_NIGHTBLINDLEVEL;
            }

            foreach ($propertyBlindLevels as $propertyBlindLevel) {
                if ($ret = $this->checkRangeFloat(
                    $propertyBlindLevel,
                    min($this->profileBlindLevel['MinValue'], $this->profileBlindLevel['MaxValue']),
                    max($this->profileBlindLevel['MinValue'], $this->profileBlindLevel['MaxValue']),
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

        if (IPS_VariableExists($this->ReadPropertyInteger(self::PROP_SLATSLEVELID))) {
            $this->profileSlatsLevel = $this->GetPresentationInformation(self::PROP_SLATSLEVELID);
            if ($this->profileSlatsLevel !== null) {
                $propertySlatsLevels = [
                    self::PROP_LOWSUNPOSITIONSLATSLEVEL,
                    self::PROP_HIGHSUNPOSITIONSLATSLEVEL,
                    self::PROP_MINIMUMSHADERELEVANTSLATSLEVEL,
                    self::PROP_MAXIMUMSHADERELEVANTSLATSLEVEL,
                    self::PROP_CONTACTCLOSESLATSLEVEL1,
                    self::PROP_CONTACTCLOSESLATSLEVEL2,
                    self::PROP_CONTACTOPENSLATSLEVEL1,
                    self::PROP_CONTACTOPENSLATSLEVEL2
                ];

                // Öffnen-/Schließen-Kontakt-Wertegruppen (Lamellen der Zusatzpositionen 2/3) mitprüfen
                for ($i = 1; $i <= 2; $i++) {
                    for ($j = 2; $j <= 3; $j++) {
                        $propertySlatsLevels[] = $this->openSlatsProp($i, $j);
                        $propertySlatsLevels[] = $this->closeSlatsProp($i, $j);
                    }
                }

                if ($this->ReadPropertyBoolean(self::PROP_ACTIVATEDINDIVIDUALDAYLEVELS)) {
                    $propertySlatsLevels[] = self::PROP_DAYSLATSLEVEL;
                }

                if ($this->ReadPropertyBoolean(self::PROP_ACTIVATEDINDIVIDUALNIGHTLEVELS)) {
                    $propertySlatsLevels[] = self::PROP_NIGHTSLATSLEVEL;
                }

                foreach ($propertySlatsLevels as $propertySlatsLevel) {
                    if ($ret = $this->checkRangeFloat(
                        $propertySlatsLevel,
                        min($this->profileSlatsLevel['MinValue'], $this->profileSlatsLevel['MaxValue']),
                        max($this->profileSlatsLevel['MinValue'], $this->profileSlatsLevel['MaxValue']),
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

        if ($ret =
            $this->checkRangeInteger(self::PROP_DEACTIVATIONMANUALMOVEMENT, 0, 100000, self::STATUS_INST_DEACTIVATION_TIME_MANUAL_IS_INVALID)) {
            $this->SetStatus($ret);
            return;
        }

        if ($ret =
            $this->checkRangeInteger(self::PROP_DEACTIVATIONAUTOMATICMOVEMENT, 0, 100000, self::STATUS_INST_DEACTIVATION_TIME_AUTOMATIC_IS_INVALID)) {
            $this->SetStatus($ret);
            return;
        }

        if ($ret = $this->checkTimeTable()) {
            $this->SetStatus($ret);
            return;
        }

        if ($this->GetValue(self::VAR_IDENT_ACTIVATED)) {
            $this->SetTimerInterval(self::TIMER_UPDATE, $this->ReadPropertyInteger(self::PROP_UPDATEINTERVAL) * 60 * 1000);
        } else {
            $this->SetTimerInterval(self::TIMER_UPDATE, 0);
            $this->SetTimerInterval(self::TIMER_DELAYED_MOVEMENT, 0);
            $this->SetTimerInterval(self::TIMER_OPEN_CONTACT1, 0);
            $this->SetTimerInterval(self::TIMER_OPEN_CONTACT2, 0);
            $this->SetTimerInterval(self::TIMER_CLOSE_CONTACT1, 0);
            $this->SetTimerInterval(self::TIMER_CLOSE_CONTACT2, 0);
            $this->SetStatus(IS_INACTIVE);
            return;
        }

        $this->SetStatus(IS_ACTIVE);
    }

    private function checkVariableId(string $propName, bool $optional, array $variableTypes, bool $mustBeSwitchable, int $errStatus): int
    {
        $variableID = $this->ReadPropertyInteger($propName);

        if (!$optional && !IPS_VariableExists($variableID)) {
            $this->Logger_Err(sprintf('\'%s\': ID nicht gesetzt: %s', $this->objectName, $propName));
            return $errStatus;
        }

        if (IPS_VariableExists($variableID)) {
            if (!$variable = @IPS_GetVariable($variableID)) {
                $this->Logger_Err(sprintf('\'%s\': falsche Variablen ID (#%s) für "%s"', $this->objectName, $variableID, $propName));
                return $errStatus;
            }

            if (!in_array($variable['VariableType'], $variableTypes, true)) {
                $this->Logger_Err(
                    sprintf(
                        '\'%s\': falscher Variablentyp (%s) für "%s" - nur %s erlaubt',
                        $this->objectName,
                        $variable['VariableType'],
                        $propName,
                        implode(', ', $variableTypes)
                    )
                );
                return $errStatus;
            }

            if ($mustBeSwitchable) {
                if ($variable['VariableCustomAction'] !== 0) {
                    $profileAction = $variable['VariableCustomAction'];
                } else {
                    $profileAction = $variable['VariableAction'];
                }

                if ($profileAction <= 10000) {
                    $this->Logger_Err(
                        sprintf('\'%s\': die Variable #%s für "%s" ist nicht schaltbar', $this->objectName, $variableID, $propName)
                    );
                    return $errStatus;
                }
            }
        }

        return 0;
    }

    private function checkActionOfStatusVariable(string $proName): bool
    {
        $var = IPS_GetVariable($this->ReadPropertyInteger($proName));

        return ($var['VariableAction'] || $var['VariableCustomAction'])
               && (count($var['VariableCustomPresentation'])
                   || count(
                       $var['VariablePresentation']
                   ));
    }


    private function checkEmulateStatusOfVariableAction(string $proName): bool
    {
        $var = IPS_GetVariable($this->ReadPropertyInteger($proName));

        if ($var['VariableAction'] !== 0) {
            $configuration = @IPS_GetConfiguration($var['VariableAction']);
            if ($configuration !== false) {
                $arrConfiguration = json_decode($configuration, true, 512, JSON_THROW_ON_ERROR);
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

        if (!$optional && !IPS_EventExists($eventID)) {
            $this->Logger_Err(sprintf('\'%s\': ID nicht gesetzt: %s', $this->objectName, $propName));
            return $errStatus;
        }

        if (IPS_EventExists($eventID)) {
            if (!$variable = @IPS_GetEvent($eventID)) {
                $this->Logger_Err(sprintf('\'%s\': falsche Event ID #%s', $this->objectName, $propName));
                return $errStatus;
            }

            if ($variable['EventType'] !== $eventType) {
                $this->Logger_Err(sprintf('\'%s\': falscher Eventtyp - nur %s erlaubt', $this->objectName, $eventType));
                return $errStatus;
            }
        }

        return 0;
    }

    private function checkRangeInteger(string $propName, int $min, int $max, int $errStatus): int
    {
        $value = $this->ReadPropertyInteger($propName);

        if ($value < $min || $value > $max) {
            $this->Logger_Err(sprintf('\'%s\': %s: Wert (%s) nicht im gültigen Bereich (%s - %s)', $this->objectName, $propName, $value, $min, $max));
            return $errStatus;
        }

        return 0;
    }

    private function checkRangeFloat(string $propName, float $min, float $max, int $errStatus): int
    {
        $value = $this->ReadPropertyFloat($propName);

        if ((int)$value === 0) {
            return 0;
        }

        if ($value < $min || $value > $max) {
            $this->Logger_Err(
                sprintf('\'%s\': %s: Wert (%.2f) nicht im gültigen Bereich (%.2f - %.2f)', $this->objectName, $propName, $value, $min, $max)
            );
            return $errStatus;
        }

        return 0;
    }

    private function activateDelayTimer(int $interval): void
    {
        if ($this->ReadPropertyBoolean(self::PROP_DELAYTIMEDAYNIGHTCHANGEISRANDOMLY)) {
            try {
                $interval = random_int(0, $interval);
            } catch (Exception) {
                $this->Logger_Dbg(__FUNCTION__, 'Generation of random integer failed!');
            }
        }

        $daytimeChangeTime = time() + $interval;
        $this->Logger_Dbg(__FUNCTION__, sprintf('DayChange is delayed by %s s to %s!', $interval, date('H:i:s', $daytimeChangeTime)));

        if ($this->dryRun) {
            return;
        }

        $this->WriteAttributeInteger(self::ATTR_DAYTIME_CHANGE_TIME, $daytimeChangeTime);

        $this->SetTimerInterval(self::TIMER_DELAYED_MOVEMENT, $interval * 1000);
    }

    private function deactivateDelayTimer(): void
    {
        $this->Logger_Dbg(__FUNCTION__, 'Delay Timer deactivated');

        if ($this->dryRun) {
            return;
        }

        $this->WriteAttributeInteger(self::ATTR_DAYTIME_CHANGE_TIME, 0);

        $this->SetTimerInterval(self::TIMER_DELAYED_MOVEMENT, 0);
    }

    /**
     * Prüft den Tageswechsel und berücksichtigt eine ggf. konfigurierte Verzögerung.
     * Aktualisiert den effektiven Tageszustand in $dayState['isDay'].
     *
     * @param array $dayState Ergebnis aus determineDayState(); 'isDay' wird ggf. angepasst.
     * @return bool True, wenn der Tageswechsel final vollzogen wurde (nach Ablauf der Verzögerung).
     */
    private function checkIsDayChange(array &$dayState): bool
    {
        $isDay = $dayState['isDay'];
        if ($this->ReadAttributeBoolean('AttrIsDay') !== $isDay) { //Tageswechsel erreicht

            if ((time() - $this->ReadAttributeInteger(self::ATTR_TIMESTAMP_AUTOMATIC)) <= 1) {
                //wenn die Automatic gerade erst eingeschaltet worden ist, dann wird die Verzögerungszeit ignoriert
                $delayTime = 0;
            } else {
                $delayTime = $this->ReadPropertyInteger(self::PROP_DELAYTIMEDAYNIGHTCHANGE);
            }

            if ($delayTime > 0) { //Verzögerung aktiviert
                $attrDaytimeChangeTime = $this->ReadAttributeInteger(self::ATTR_DAYTIME_CHANGE_TIME);

                if ($attrDaytimeChangeTime === 0) {
                    $this->activateDelayTimer($delayTime);
                    $dayState['isDay'] = $this->ReadAttributeBoolean('AttrIsDay');
                    return false;
                }

                if (time() < $attrDaytimeChangeTime) {
                    $dayState['isDay'] = $this->ReadAttributeBoolean('AttrIsDay');
                    return false;
                }

                $this->deactivateDelayTimer(); //Timer wieder ausschalten

            }
            if (!$this->dryRun) {
                $this->WriteAttributeBoolean('AttrIsDay', $isDay);
                $this->WriteAttributeInteger('AttrTimeStampIsDayChange', time());
            }
            $this->Logger_Dbg(__FUNCTION__, 'DayChange!');
            $dayState['isDay'] = $isDay;
            return true;
        }

        $dayState['isDay'] = $isDay;
        return false;
    }

    private function getDefinedContacts(string $contactIdKey, string $blindLevelKey, string $slatsLevelKey): array
    {
        $contacts = [];
        for ($i = 1; $i <= 2; $i++) { // Erweitern um weitere Kontakte, falls nötig
            $id = $this->ReadPropertyInteger(constant("self::{$contactIdKey}{$i}ID"));
            if (IPS_VariableExists($id)) {
                $contacts[constant("self::{$contactIdKey}{$i}ID")] = [
                    'id'         => $id,
                    'blindlevel' => $this->ReadPropertyFloat(constant("self::{$blindLevelKey}{$i}")),
                    'slatslevel' => $this->ReadPropertyFloat(constant("self::{$slatsLevelKey}{$i}"))
                ];
            }
        }
        return $contacts;
    }


    // Property-Namen der Öffnen-Kontakt-Wertegruppen (i = Kontakt 1/2, j = Zustand 1..3)
    private function openValuesProp(int $i, int $j): string
    {
        return sprintf('ContactOpenValues%d_%d', $i, $j);
    }

    private function openLevelProp(int $i, int $j): string
    {
        // Position 1 nutzt das bestehende Property (kein "_1"-Suffix) -> keine Migration nötig
        return $j === 1 ? sprintf('ContactOpenLevel%d', $i) : sprintf('ContactOpenLevel%d_%d', $i, $j);
    }

    private function openSlatsProp(int $i, int $j): string
    {
        return $j === 1 ? sprintf('ContactOpenSlatsLevel%d', $i) : sprintf('ContactOpenSlatsLevel%d_%d', $i, $j);
    }

    private function openDelayProp(int $i): string
    {
        return sprintf('ContactOpenDelay%d', $i);
    }

    // Property-Namen der Schließen-Kontakt-Wertegruppen (i = Kontakt 1/2, j = Zustand 1..3)
    private function closeValuesProp(int $i, int $j): string
    {
        return sprintf('ContactCloseValues%d_%d', $i, $j);
    }

    private function closeLevelProp(int $i, int $j): string
    {
        // Position 1 nutzt das bestehende Property (kein "_1"-Suffix) -> keine Migration nötig
        return $j === 1 ? sprintf('ContactCloseLevel%d', $i) : sprintf('ContactCloseLevel%d_%d', $i, $j);
    }

    private function closeSlatsProp(int $i, int $j): string
    {
        return $j === 1 ? sprintf('ContactCloseSlatsLevel%d', $i) : sprintf('ContactCloseSlatsLevel%d_%d', $i, $j);
    }

    private function closeDelayProp(int $i): string
    {
        return sprintf('ContactCloseDelay%d', $i);
    }

    /**
     * Ermittelt die Öffnungsbegrenzung der Öffnen-Kontakte.
     *
     * Je Kontakt können bis zu drei Wertegruppen mit eigener Position konfiguriert sein.
     * Ist für einen Kontakt keine Wertegruppe gesetzt, gilt das klassische Boolean/Reversed-
     * Verhalten mit ContactOpenLevel{i}/ContactOpenSlatsLevel{i}. Die Positionen der Kontakte
     * werden - wie bisher - zur jeweils "am weitesten geöffneten" Position kombiniert.
     */
    private function getPositionsOfOpenBlindContact(): ?array
    {
        $blindPositions = null;

        for ($i = 1; $i <= 2; $i++) {
            $contactId = $this->ReadPropertyInteger(constant("self::PROP_CONTACTOPEN{$i}ID"));
            if (!IPS_VariableExists($contactId)) {
                continue;
            }

            $position = $this->getSingleOpenContactPosition($i, $contactId);
            if ($position === null) {
                continue;
            }

            if ($blindPositions === null) {
                $blindPositions = $position;
            } else {
                $blindPositions = $this->combineOpeningLimits($blindPositions, $position);
            }
        }

        return $blindPositions;
    }

    /**
     * Liefert die Position eines einzelnen Öffnen-Kontakts oder null, wenn er nicht "offen" ist.
     *
     * @return array{BlindLevel: float, SlatsLevel: float}|null
     */
    private function getSingleOpenContactPosition(int $i, int $contactId): ?array
    {
        // Wertegruppen einsammeln (leere ignorieren)
        $hasValueGroups = false;
        for ($j = 1; $j <= 3; $j++) {
            if (trim($this->ReadPropertyString($this->openValuesProp($i, $j))) !== '') {
                $hasValueGroups = true;
                break;
            }
        }

        // Klassisches Boolean-Verhalten, wenn keine Wertegruppe konfiguriert ist:
        // Es gilt dann die bestehende Ein/Aus-Position (ContactOpenLevel{i}/SlatsLevel{i}).
        if (!$hasValueGroups) {
            if (!$this->isContactOpen(constant("self::PROP_CONTACTOPEN{$i}ID"))) {
                return null;
            }
            $position = [
                'BlindLevel' => $this->ReadPropertyFloat(constant("self::PROP_CONTACTOPENLEVEL{$i}")),
                'SlatsLevel' => $this->ReadPropertyFloat(constant("self::PROP_CONTACTOPENSLATSLEVEL{$i}"))
            ];
            $this->Logger_Dbg(
                __FUNCTION__,
                sprintf('Öffnen-Kontakt %d (#%s): offen -> blindlevel: %s, slatslevel: %s', $i, $contactId, $position['BlindLevel'], $position['SlatsLevel'])
            );
            return $position;
        }

        // Wertegruppen-Modus: erste passende Gruppe bestimmt die Position
        $value        = GetValue($contactId);
        $variableType = IPS_GetVariable($contactId)['VariableType'];

        for ($j = 1; $j <= 3; $j++) {
            $configured = $this->ReadPropertyString($this->openValuesProp($i, $j));
            if (trim($configured) === '') {
                continue;
            }
            if ($this->matchesConfiguredValue($value, $configured, $variableType)) {
                $position = [
                    'BlindLevel' => $this->ReadPropertyFloat($this->openLevelProp($i, $j)),
                    'SlatsLevel' => $this->ReadPropertyFloat($this->openSlatsProp($i, $j))
                ];
                $this->Logger_Dbg(
                    __FUNCTION__,
                    sprintf('Öffnen-Kontakt %d (#%s): Wert "%s" trifft Zustand %d -> blindlevel: %s, slatslevel: %s', $i, $contactId, $this->formatConfiguredValue($value), $j, $position['BlindLevel'], $position['SlatsLevel'])
                );
                return $position;
            }
        }

        $this->Logger_Dbg(
            __FUNCTION__,
            sprintf('Öffnen-Kontakt %d (#%s): Wert "%s" trifft keinen Zustand -> keine Wirkung', $i, $contactId, $this->formatConfiguredValue(GetValue($contactId)))
        );

        return null;
    }

    /**
     * Ermittelt die Schließbegrenzung der Schließen-Kontakte.
     *
     * Je Kontakt können bis zu drei Wertegruppen mit eigener Position konfiguriert sein.
     * Ist für einen Kontakt keine Wertegruppe gesetzt, gilt das klassische Boolean/Reversed-
     * Verhalten mit ContactCloseLevel{i}/ContactCloseSlatsLevel{i}. Mehrere gleichzeitig offene
     * Kontakte werden zur am weitesten geschlossenen Position kombiniert (combineClosingLimits()).
     */
    private function getPositionsOfCloseBlindContact(): ?array
    {
        $blindPositions = null;

        for ($i = 1; $i <= 2; $i++) {
            $contactId = $this->ReadPropertyInteger(constant("self::PROP_CONTACTCLOSE{$i}ID"));
            if (!IPS_VariableExists($contactId)) {
                continue;
            }

            $position = $this->getSingleCloseContactPosition($i, $contactId);
            if ($position === null) {
                continue;
            }

            if ($blindPositions === null) {
                $blindPositions = $position;
            } else {
                $blindPositions = $this->combineClosingLimits($blindPositions, $position);
            }
        }

        return $blindPositions;
    }

    /**
     * Liefert die Position eines einzelnen Schließen-Kontakts oder null, wenn er nicht "offen" ist.
     *
     * @return array{BlindLevel: float, SlatsLevel: float}|null
     */
    private function getSingleCloseContactPosition(int $i, int $contactId): ?array
    {
        // Wertegruppen einsammeln (leere ignorieren)
        $hasValueGroups = false;
        for ($j = 1; $j <= 3; $j++) {
            if (trim($this->ReadPropertyString($this->closeValuesProp($i, $j))) !== '') {
                $hasValueGroups = true;
                break;
            }
        }

        // Klassisches Boolean-Verhalten, wenn keine Wertegruppe konfiguriert ist:
        // Es gilt dann die bestehende Ein/Aus-Position (ContactCloseLevel{i}/SlatsLevel{i}).
        if (!$hasValueGroups) {
            if (!$this->isContactOpen(constant("self::PROP_CONTACTCLOSE{$i}ID"))) {
                return null;
            }
            $position = [
                'BlindLevel' => $this->ReadPropertyFloat(constant("self::PROP_CONTACTCLOSELEVEL{$i}")),
                'SlatsLevel' => $this->ReadPropertyFloat(constant("self::PROP_CONTACTCLOSESLATSLEVEL{$i}"))
            ];
            $this->Logger_Dbg(
                __FUNCTION__,
                sprintf('Schließen-Kontakt %d (#%s): offen -> blindlevel: %s, slatslevel: %s', $i, $contactId, $position['BlindLevel'], $position['SlatsLevel'])
            );
            return $position;
        }

        // Wertegruppen-Modus: erste passende Gruppe bestimmt die Position
        $value        = GetValue($contactId);
        $variableType = IPS_GetVariable($contactId)['VariableType'];

        for ($j = 1; $j <= 3; $j++) {
            $configured = $this->ReadPropertyString($this->closeValuesProp($i, $j));
            if (trim($configured) === '') {
                continue;
            }
            if ($this->matchesConfiguredValue($value, $configured, $variableType)) {
                $position = [
                    'BlindLevel' => $this->ReadPropertyFloat($this->closeLevelProp($i, $j)),
                    'SlatsLevel' => $this->ReadPropertyFloat($this->closeSlatsProp($i, $j))
                ];
                $this->Logger_Dbg(
                    __FUNCTION__,
                    sprintf('Schließen-Kontakt %d (#%s): Wert "%s" trifft Zustand %d -> blindlevel: %s, slatslevel: %s', $i, $contactId, $this->formatConfiguredValue($value), $j, $position['BlindLevel'], $position['SlatsLevel'])
                );
                return $position;
            }
        }

        $this->Logger_Dbg(
            __FUNCTION__,
            sprintf('Schließen-Kontakt %d (#%s): Wert "%s" trifft keinen Zustand -> keine Wirkung', $i, $contactId, $this->formatConfiguredValue(GetValue($contactId)))
        );

        return null;
    }

    /**
     * Vergleicht einen Variablenwert mit einer kommaseparierten Liste konfigurierter Werte.
     */
    private function matchesConfiguredValue(mixed $actualValue, string $configuredValues, int $variableType): bool
    {
        $values = array_filter(array_map('trim', explode(',', $configuredValues)), static fn(string $value): bool => $value !== '');

        foreach ($values as $value) {
            switch ($variableType) {
                case VARIABLETYPE_BOOLEAN:
                    $configuredValue = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
                    if ($configuredValue !== null && $actualValue === $configuredValue) {
                        return true;
                    }
                    break;

                case VARIABLETYPE_INTEGER:
                    if (filter_var($value, FILTER_VALIDATE_INT) !== false && $actualValue === (int)$value) {
                        return true;
                    }
                    break;

                case VARIABLETYPE_FLOAT:
                    if (is_numeric($value) && abs((float)$actualValue - (float)$value) <= 0.000001) {
                        return true;
                    }
                    break;

                case VARIABLETYPE_STRING:
                    if ($actualValue === $value) {
                        return true;
                    }
                    break;
            }
        }

        return false;
    }

    private function formatConfiguredValue(mixed $value): string
    {
        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        return (string)$value;
    }

    /**
     * Verknüpft zwei Öffnungsbegrenzungen. Es gewinnt jeweils die weiter geöffnete Position.
     */
    private function combineOpeningLimits(array $first, array $second): array
    {
        $first['BlindLevel'] = $this->isMinMaxReversed($this->profileBlindLevel['MinValue'], $this->profileBlindLevel['MaxValue'])
            ? max($first['BlindLevel'], $second['BlindLevel'])
            : min($first['BlindLevel'], $second['BlindLevel']);

        if (isset($this->profileSlatsLevel)) {
            $first['SlatsLevel'] = $this->isMinMaxReversed($this->profileSlatsLevel['MinValue'], $this->profileSlatsLevel['MaxValue'])
                ? max($first['SlatsLevel'], $second['SlatsLevel'])
                : min($first['SlatsLevel'], $second['SlatsLevel']);
        }

        return $first;
    }

    /**
     * Verknüpft zwei Schließbegrenzungen. Es gewinnt jeweils die weiter geschlossene Position,
     * damit die Anforderung aller gleichzeitig offenen Schließen-Kontakte erfüllt wird
     * (exakt invertiert zu combineOpeningLimits).
     */
    private function combineClosingLimits(array $first, array $second): array
    {
        $first['BlindLevel'] = $this->isMinMaxReversed($this->profileBlindLevel['MinValue'], $this->profileBlindLevel['MaxValue'])
            ? min($first['BlindLevel'], $second['BlindLevel'])
            : max($first['BlindLevel'], $second['BlindLevel']);

        if (isset($this->profileSlatsLevel)) {
            $first['SlatsLevel'] = $this->isMinMaxReversed($this->profileSlatsLevel['MinValue'], $this->profileSlatsLevel['MaxValue'])
                ? min($first['SlatsLevel'], $second['SlatsLevel'])
                : max($first['SlatsLevel'], $second['SlatsLevel']);
        }

        return $first;
    }


    private function isContactOpen(string $propName): bool
    {
        $contactId = $this->ReadPropertyInteger($propName);
        if (!IPS_VariableExists($contactId)) {
            return false;
        }

        if ($prof = $this->GetProfileInformation_org($propName)) {
            //$reversed = false; //todo: was ist mit reversed Kontakten?
            $reversed = $prof['Reversed'];
        } else {
            $reversed = false;
        }

        $isOpen = $reversed ? !GetValue($contactId) : (bool)GetValue($contactId);

        $this->Logger_Dbg(
            __FUNCTION__,
            sprintf('%s (#%s): value: %s, reversed: %s, offen: %s', $propName, $contactId, (int)GetValue($contactId), (int)$reversed, (int)$isOpen)
        );

        return $isOpen;
    }

    private function getLevelEmergencyContact(): ?float
    {
        $emergencyContactId = $this->ReadPropertyInteger(self::PROP_EMERGENCYCONTACTID);
        if (!IPS_VariableExists($emergencyContactId)) {
            return null;
        }

        // Wenn der Kontakt offen ist (Logik inkl. Reversed-Profil in isContactOpen)
        if ($this->isContactOpen(self::PROP_EMERGENCYCONTACTID)) {
            $level = $this->isMinMaxReversed($this->profileBlindLevel['MinValue'], $this->profileBlindLevel['MaxValue'])
                ? (float)$this->profileBlindLevel['MinValue'] : (float)$this->profileBlindLevel['MaxValue'];

            $this->Logger_Dbg(
                __FUNCTION__,
                sprintf(
                    'emergency contact is open: #%s, value: %s, target level: %s',
                    $emergencyContactId,
                    $this->GetFormattedValue($emergencyContactId),
                    $level
                )
            );

            return $level;
        }

        return null;
    }

    private function getPositionsOfShadowingBySunPosition(float $levelAct): ?array
    {
        $activatorID = $this->ReadPropertyInteger(self::PROP_ACTIVATORIDSHADOWINGBYSUNPOSITION);

        if (!IPS_VariableExists($activatorID)) {
            // Beschattung nach Sonnenstand ist nicht konfiguriert
            return null;
        }
        if (!GetValue($activatorID)) {
            $this->addShadowingReason('nach Sonnenstand nicht aktiviert');
            return null;
        }

        $temperatureID = $this->ReadPropertyInteger(self::PROP_TEMPERATUREIDSHADOWINGBYSUNPOSITION);
        if (!IPS_VariableExists($temperatureID)) {
            $temperature = null;
        } else {
            $temperature = (float)GetValue($temperatureID);
        }

        $brightness = $this->GetBrightness(
            self::PROP_BRIGHTNESSIDSHADOWINGBYSUNPOSITION,
            self::PROP_BRIGHTNESSAVGMINUTESSHADOWINGBYSUNPOSITION,
            $levelAct,
            true
        );

        if (!is_null($brightness)) {
            $thresholdBrightness = $this->getBrightnessThreshold(
                $this->ReadPropertyInteger(self::PROP_BRIGHTNESSTHRESHOLDIDSHADOWINGBYSUNPOSITION),
                $levelAct,
                $temperature
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

        $this->Logger_Dbg(
            __FUNCTION__,
            sprintf(
                'active: %d, brightness(act/thresh): %.1f/%.1f, azimuth: %.1f (%.1f - %.1f), altitude: %.1f (%.1f - %.1f), temperature: %s',
                (int)GetValue($activatorID),
                $brightness,
                $thresholdBrightness,
                floor($rSunAzimuth * 10) / 10,
                $azimuthFrom,
                $azimuthTo,
                floor($rSunAltitude * 10) / 10,
                $altitudeFrom,
                $altitudeTo,
                $temperature ?? 'null'
            )
        );

        $positions      = null;
        $azimuthMatches = $this->isAzimuthInRange($rSunAzimuth, $azimuthFrom, $azimuthTo);
        if (($brightness >= $thresholdBrightness) && $azimuthMatches
            && ($rSunAltitude >= $altitudeFrom)
            && ($rSunAltitude <= $altitudeTo)) {
            // entscheidungsrelevante Helligkeit für die Erklärung festhalten (nur wenn ein Helligkeitssensor konfiguriert ist)
            if ($brightness !== null && IPS_VariableExists($this->ReadPropertyInteger(self::PROP_BRIGHTNESSIDSHADOWINGBYSUNPOSITION))) {
                $this->shadowingBrightnessInfo = $this->buildShadowingBrightnessInfo(
                    $this->ReadPropertyInteger(self::PROP_BRIGHTNESSIDSHADOWINGBYSUNPOSITION),
                    $brightness,
                    $this->ReadPropertyInteger(self::PROP_BRIGHTNESSTHRESHOLDIDSHADOWINGBYSUNPOSITION),
                    $thresholdBrightness,
                    $temperature
                );
            }
            // Simple variant
            if ($this->ReadPropertyInteger(self::PROP_DEPTHSUNLIGHT) === 0) {
                $positions = $this->getBlindPositionsFromSunPositionSimple($rSunAltitude);

                $this->Logger_Dbg(
                    __FUNCTION__,
                    sprintf(
                        'BlindLevelFromSunPosition(Simple): %.2f, SlatsLevelFromSunPosition(Simple): %.2f',
                        $positions['BlindLevel'],
                        $positions['SlatsLevel']
                    )
                );
            } else { //exact variant
                $positions = $this->getBlindPositionsFromSunPositionExact($rSunAltitude, $rSunAzimuth);

                $this->Logger_Dbg(
                    __FUNCTION__,
                    sprintf(
                        'BlindLevelFromSunPosition(Exact): %.2f, SlatsLevelFromSunPosition(Exact): %.2f',
                        $positions['BlindLevel'],
                        $positions['SlatsLevel']
                    )
                );
            }

            //wenn zusätzlicher *Hitzeschutz* notwenig oder bereits eingeschaltet und Hysterese nicht unterschritten
            $levelPositionHeat = round($this->calculateProfilePositionByPercent(90.0, $this->profileBlindLevel), 2);

            if (($temperature > 30.0) || ((round($levelAct, 1) === round($levelPositionHeat, 1)) && ($temperature > (30.0 - 0.5)))) {
                $positions['BlindLevel'] = $levelPositionHeat;
                $this->Logger_Dbg(__FUNCTION__, sprintf('Temp gt 30°, levelAct: %.2f, level: %.2f', $levelAct, $positions['BlindLevel']));
                $this->shadowingHeatInfo = sprintf('Hitzeschutz: %s > 30 °C', GetValueFormattedEx($temperatureID, $temperature));
                return $positions;
            }

            //wenn zusätzlicher *Wärmeschutz* notwendig oder bereits eingeschaltet und Hysterese nicht unterschritten
            $levelCorrectionHeat = round(0.15 * ($this->profileBlindLevel['MaxValue'] - $this->profileBlindLevel['MinValue']), 2);

            if (($temperature > 27.0)
                || ((round($levelAct, 1) === round($positions['BlindLevel'] + $levelCorrectionHeat, 1))
                    && ($temperature > (27.0 - 0.5)))) {
                $positions['BlindLevel'] = $this->clampToProfile($positions['BlindLevel'] + $levelCorrectionHeat, $this->profileBlindLevel);
                $this->Logger_Dbg(
                    __FUNCTION__,
                    sprintf(
                        'Temp gt 27°, levelAct: %.2f, level: %.2f, levelCorrectionHeat: %.2f',
                        $levelAct,
                        $positions['BlindLevel'],
                        $levelCorrectionHeat
                    )
                );
                $this->shadowingHeatInfo = sprintf('Wärmeschutz: %s > 27 °C', GetValueFormattedEx($temperatureID, $temperature));
                return $positions;
            }
        }

        // Beschattung ist aktiviert, aber die Bedingungen sind nicht erfüllt -> Grund(e) für die Erklärung sammeln
        if ($positions === null) {
            $reasons = [];
            if ($brightness !== null && $brightness < $thresholdBrightness) {
                $reasons[] = sprintf(
                    'Helligkeit %s unter Schwellwert %s',
                    $this->formatBrightnessForTrace($this->ReadPropertyInteger(self::PROP_BRIGHTNESSIDSHADOWINGBYSUNPOSITION), $brightness),
                    $this->formatBrightnessForTrace($this->ReadPropertyInteger(self::PROP_BRIGHTNESSTHRESHOLDIDSHADOWINGBYSUNPOSITION), $thresholdBrightness)
                );
            }
            if (!$azimuthMatches) {
                $reasons[] = sprintf('Azimut %.1f° außerhalb %.1f°-%.1f°', floor($rSunAzimuth * 10) / 10, $azimuthFrom, $azimuthTo);
            }
            if ($rSunAltitude < $altitudeFrom || $rSunAltitude > $altitudeTo) {
                $reasons[] = sprintf('Sonnenhöhe %.1f° außerhalb %.1f°-%.1f°', floor($rSunAltitude * 10) / 10, $altitudeFrom, $altitudeTo);
            }
            if ($reasons !== []) {
                $this->addShadowingReason('nach Sonnenstand: ' . implode(', ', $reasons));
            }
        }

        return $positions;
    }

    /**
     * Formatiert einen Helligkeits-/Schwellwert für die Erklärungsausgabe.
     * Wenn die zugehörige Variable existiert, wird deren Darstellung (z.B. Einheit "lx") über GetValueFormattedEx genutzt,
     * sonst wird der Wert kompakt als Zahl ausgegeben.
     */
    private function formatBrightnessForTrace(int $variableID, float $value): string
    {
        if (IPS_VariableExists($variableID)) {
            return GetValueFormattedEx($variableID, $value);
        }
        return rtrim(rtrim(sprintf('%.1f', $value), '0'), '.');
    }

    /**
     * Erzeugt die entscheidungsrelevante Helligkeitsangabe einer greifenden Beschattung
     * (effektiver, ggf. gemittelter Helligkeitswert und der Schwellwert), z.B. "Helligkeit 91317 lx ≥ Schwellwert 50000 lx".
     *
     * Bei hohen Außentemperaturen senkt die Temperaturkorrektur den Schwellwert (10 % je Grad über 24 °C). Übersteigt
     * die Reduktion 100 %, wird der Schwellwert rechnerisch <= 0; die Beschattung erfolgt dann unabhängig von der
     * Helligkeit (Hitzeschutz). Statt eines verwirrenden negativen lx-Wertes wird dies dann im Klartext ausgegeben.
     */
    private function buildShadowingBrightnessInfo(int $brightnessID, float $brightness, int $thresholdID, float $threshold, ?float $temperature = null): string
    {
        if ($temperature !== null && $temperature > 24 && $threshold <= 0) {
            return sprintf(
                'Helligkeit %s, Beschattung temperaturbedingt unabhängig von der Helligkeit (%s)',
                $this->formatBrightnessForTrace($brightnessID, $brightness),
                GetValueFormattedEx($this->ReadPropertyInteger(self::PROP_TEMPERATUREIDSHADOWINGBYSUNPOSITION), $temperature)
            );
        }

        // Hinweis, dass der Schwellwert temperaturbedingt angepasst wurde (10 % je Grad über 24 °C bzw. unter 10 °C)
        $tempNote = '';
        if ($temperature !== null && ($temperature > 24 || $temperature < 10)) {
            $tempNote = sprintf(
                ' (temperaturkorrigiert, %s)',
                GetValueFormattedEx($this->ReadPropertyInteger(self::PROP_TEMPERATUREIDSHADOWINGBYSUNPOSITION), $temperature)
            );
        }

        return sprintf(
            'Helligkeit %s ≥ Schwellwert %s%s',
            $this->formatBrightnessForTrace($brightnessID, $brightness),
            $this->formatBrightnessForTrace($thresholdID, $threshold),
            $tempNote
        );
    }

    private function isAzimuthInRange(float $azimuth, float $from, float $to): bool
    {
        // Ein Bereich mit 360 Grad Spannweite soll immer den Vollkreis abdecken.
        if (abs($to - $from) >= (360.0 - PHP_FLOAT_EPSILON)) {
            return true;
        }

        $azimuth = fmod($azimuth + 360.0, 360.0);
        $from    = fmod($from + 360.0, 360.0);
        $to      = fmod($to + 360.0, 360.0);

        if ($from <= $to) {
            return ($azimuth >= $from) && ($azimuth <= $to);
        }

        return ($azimuth >= $from) || ($azimuth <= $to);
    }

    private function GetBrightness(string $propBrightnessID, string $propBrightnessAvgMinutes, float $levelAct, bool $shadowing): ?float
    {
        $brightnessID = $this->ReadPropertyInteger($propBrightnessID);
        if (!IPS_VariableExists($brightnessID)) {
            return null;
        }

        $currentBrightness    = (float)GetValue($brightnessID);
        $brightnessAvgMinutes = $this->ReadPropertyInteger($propBrightnessAvgMinutes);

        if ($brightnessAvgMinutes <= 0) {
            return $currentBrightness;
        }

        $archiveIds = IPS_GetInstanceListByModuleID('{43192F0B-135B-4CE7-A0A7-1475603F3060}');
        if (empty($archiveIds)) {
            return $currentBrightness;
        }
        $archiveId = $archiveIds[0];

        if (AC_GetLoggingStatus($archiveId, $brightnessID)) {
            $werte = @AC_GetAggregatedValues($archiveId, $brightnessID, 6, strtotime('-' . $brightnessAvgMinutes . ' minutes'), time(), 0);
            if (empty($werte)) {
                //bei der Sommer auf Winterzeitumstellung gab es eine Warning (EndTime is before StartTime) um kurz vor 3
                return (float)GetValue($brightnessID);
            }

            $brightnessAvg = round(array_sum(array_column($werte, 'Avg')) / count($werte), 2);

            if ($shadowing) {
                $isReversed = $this->isMinMaxReversed($this->profileBlindLevel['MinValue'], $this->profileBlindLevel['MaxValue']);

                // Prüfen, ob der Rollladen (teilweise) herabgefahren ist
                $isDown = $isReversed ? ($levelAct < $this->profileBlindLevel['MinValue']) : ($levelAct > $this->profileBlindLevel['MinValue']);

                $brightnessAvg = $isDown ? max($brightnessAvg, $currentBrightness) : min($brightnessAvg, $currentBrightness);
            }

            return $brightnessAvg;
        }

        return (float)GetValue($brightnessID);
    }

    private function getBrightnessThreshold(int $thresholdIDBrightness, float $levelAct, float $temperature = null): float
    {
        $baseThreshold = (float)GetValue($thresholdIDBrightness);
        $threshold     = $baseThreshold;

        if ($temperature !== null) {
            // 10 % Anpassung je Grad Abweichung von den Grenztemperaturen
            $adjustmentFactor = 0.10 * $baseThreshold;

            if ($temperature > 24) {
                $threshold -= ($temperature - 24) * $adjustmentFactor;
            } elseif ($temperature < 10) {
                $threshold += (10 - $temperature) * $adjustmentFactor;
            }
        }

        // Hysterese berücksichtigen: Wenn der Rollladen bereits (teilweise) herabgefahren ist,
        // wird der Schwellwert gesenkt, um ein ständiges Auf- und Abfahren zu verhindern.
        $isReversed = $this->isMinMaxReversed($this->profileBlindLevel['MinValue'], $this->profileBlindLevel['MaxValue']);
        $isDown     = $isReversed ? ($levelAct < $this->profileBlindLevel['MinValue']) : ($levelAct > $this->profileBlindLevel['MinValue']);

        if ($isDown) {
            $hysteresis = 0.1 * $baseThreshold;
            $threshold  -= $hysteresis;
        }

        return $threshold;
    }

    // calculates the level according to the profile
    private function getBlindPositionsFromSunPositionSimple(float $rSunAltitude): array
    {
        $blindLevel = $this->calculateAltitudeDependentPosition(
            $this->ReadPropertyFloat(self::PROP_LOWSUNPOSITIONBLINDLEVEL),
            $this->ReadPropertyFloat(self::PROP_HIGHSUNPOSITIONBLINDLEVEL),
            $rSunAltitude
        );

        $slatsLevel = $this->calculateAltitudeDependentPosition(
            $this->ReadPropertyFloat(self::PROP_LOWSUNPOSITIONSLATSLEVEL),
            $this->ReadPropertyFloat(self::PROP_HIGHSUNPOSITIONSLATSLEVEL),
            $rSunAltitude
        );

        return [
            'BlindLevel' => $this->clampToProfile($blindLevel, $this->profileBlindLevel),
            'SlatsLevel' => $this->clampToProfile($slatsLevel, $this->profileSlatsLevel)
        ];
    }

    /**
     * Begrenzt einen Wert auf die Min/Max Grenzen des Profils,
     * unabhängig davon, ob Min > Max (reversed) oder Min < Max ist.
     */
    private function clampToProfile(float $value, ?array $profile): ?float
    {
        if (is_null($profile)) {
            return null;
        }

        $limit1 = $profile['MinValue'];
        $limit2 = $profile['MaxValue'];

        return max(min($limit1, $limit2), min(max($limit1, $limit2), $value));
    }


    /**
     * Berechnet eine Position (Höhe oder Lamelle) durch lineare Interpolation der Tangens-Werte
     * zwischen einer niedrigen und einer hohen Sonnenstandsposition.
     *
     * @param float $lowPosition  Die Zielposition bei niedrigem Sonnenstand.
     * @param float $highPosition Die Zielposition bei hohem Sonnenstand.
     * @param float $sunAltitude  Der aktuelle Sonnenstand (Elevation) in Grad.
     *
     * @return float Die berechnete Zielposition.
     */
    private function calculateAltitudeDependentPosition(float $lowPosition, float $highPosition, float $sunAltitude): float
    {
        $altitudeLow  = $this->ReadPropertyFloat('LowSunPositionAltitude');
        $altitudeHigh = $this->ReadPropertyFloat('HighSunPositionAltitude');

        if (abs($altitudeLow - $altitudeHigh) < PHP_FLOAT_EPSILON) {
            return $lowPosition;
        }

        // Umrechnung und Tangens-Berechnung zentralisieren
        $tanLow  = tan(deg2rad($altitudeLow));
        $tanHigh = tan(deg2rad($altitudeHigh));
        $tanAct  = tan(deg2rad($sunAltitude));

        // Lineare Interpolation basierend auf den Tangens-Werten
        $factor = ($tanAct - $tanLow) / ($tanHigh - $tanLow);

        return $lowPosition + ($highPosition - $lowPosition) * $factor;
    }

    /**
     * Berechnet die Behang- und Lamellenposition basierend auf einer exakten Sonnenstandsberechnung.
     * Berücksichtigt Fenstergeometrie, Ausrichtung, Neigung und die gewünschte maximale Einstrahltiefe.
     *
     * @param float $degSunAltitude Sonnenhöhe in Grad.
     * @param float $degSunAzimuth  Sonnenazimut in Grad.
     *
     * @return array Array mit 'BlindLevel' und 'SlatsLevel'.
     */
    private function getBlindPositionsFromSunPositionExact(float $degSunAltitude, float $degSunAzimuth): array
    {
        $height      = $this->ReadPropertyInteger(self::PROP_WINDOWSHEIGHT);
        $parapet     = $this->ReadPropertyInteger(self::PROP_PARAPETHEIGHT);
        $slope       = $this->ReadPropertyInteger(self::PROP_WINDOWSSLOPE);
        $orientation = $this->ReadPropertyInteger(self::PROP_WINDOWORIENTATION);
        $maxDepth    = $this->ReadPropertyInteger(self::PROP_DEPTHSUNLIGHT);

        //-- Fenster (und Sonne) auf Ost/West ausrichten (Süden = 180°)
        $azimuthNorm = deg2rad(($degSunAzimuth - $orientation) + 180);
        $altitudeRad = deg2rad(90 + $degSunAltitude);

        // Sonnenvektor berechnen (Normalisiertes Koordinatensystem)
        $vSun = [
            sin($altitudeRad) * sin($azimuthNorm),
            cos($altitudeRad),
            sin($altitudeRad) * cos($azimuthNorm) * -1
        ];

        //-- Fenstergeometrie unter Berücksichtigung der Neigung
        $slopeRad = deg2rad(90 - $slope);
        $x1 = cos($slopeRad) * $height;
        $x2 = sin($slopeRad) * $height;

        // Stützvektoren: H (Oberkante Fenster), P (Unterkante/Brüstung)
        $hWindow = [0, $parapet + $x1, $x2];
        $pWindow = [0, $parapet, 0];

        //-- Schattenpunkte auf der Bodenebene (X0-X2) bestimmen
        $hShadow = $x2 + $this->Schattenpunkt_X0_X2_Ebene($hWindow, $vSun);
        $pShadow = $this->Schattenpunkt_X0_X2_Ebene($pWindow, $vSun);

        //-- Beschattungsgrad bestimmen (0 = offen, 1 = geschlossen)
        if ($maxDepth >= $hShadow) {
            $degree = 0.0;
        } elseif ($maxDepth <= $pShadow) {
            $degree = 1.0;
        } else {
            // Lineare Interpolation zwischen Ober- und Unterkante
            $degree = 1.0 - ($maxDepth - $pShadow) / ($hShadow - $pShadow);
        }

        $degree = max(0.0, min(1.0, $degree));

        $this->Logger_Dbg(
            __FUNCTION__,
            sprintf(
                'H_Shadow: %.0f, P_Shadow: %.0f, depth: %d => degree: %.0f%%',
                $hShadow, $pShadow, $maxDepth, $degree * 100
            )
        );

        if ($degree <= 0.001) {
            return [
                'BlindLevel' => $this->profileBlindLevel['MinValue'],
                'SlatsLevel' => $this->profileSlatsLevel['MinValue'] ?? null
            ];
        }

        return $this->calculatePositionsFromShadowingDegree($degree);
    }

    private function Schattenpunkt_X0_X2_Ebene(array $Stuetzvektor, array $Vektor): float
    {
        $r = -$Stuetzvektor[1] / $Vektor[1];
        return $r * $Vektor[2];
    }

    /**
     * Berechnet die Behang- und Lamellenpositionen basierend auf einem Beschattungsgrad (0.0 bis 1.0).
     * Berücksichtigt dabei eine lineare (1. Grad) oder quadratische (2. Grad) Kennlinie für die Höhe.
     *
     * @param float $degreeOfShadowing Beschattungsgrad von 0.0 (offen) bis 1.0 (geschlossen).
     * @return array{BlindLevel: float, SlatsLevel: float} Die berechneten Positionen.
     */
    private function calculatePositionsFromShadowingDegree(float $degreeOfShadowing): array
    {
        $blindLevelMin  = $this->ReadPropertyFloat(self::PROP_MINIMUMSHADERELEVANTBLINDLEVEL);
        $blindLevelHalf = $this->ReadPropertyFloat(self::PROP_HALFSHADERELEVANTBLINDLEVEL);
        $blindLevelMax  = $this->ReadPropertyFloat(self::PROP_MAXIMUMSHADERELEVANTBLINDLEVEL);

        if ($blindLevelHalf === 0.0) {
            // Funktion 1. Grades: f(x) = a * x + b
            // Stützpunkte: f(0) = min, f(1) = max
            $b          = $blindLevelMin;
            $a          = ($blindLevelMax - $blindLevelMin);
            $blindLevel = $a * $degreeOfShadowing + $b;

            $this->Logger_Dbg(
                __FUNCTION__,
                sprintf('1. Grades -> min: %s, max: %s -> Level: %.2f', $blindLevelMin, $blindLevelMax, $blindLevel)
            );
        } else {
            // Funktion 2. Grades: f(x) = a * x² + b * x + c
            // Stützpunkte: f(0) = min, f(0.5) = half, f(1) = max
            $c          = $blindLevelMin;
            $b          = 4 * $blindLevelHalf - $blindLevelMax - 3 * $blindLevelMin;
            $a          = $blindLevelMax - $blindLevelMin - $b;
            $blindLevel = $a * $degreeOfShadowing ** 2 + $b * $degreeOfShadowing + $c;

            $this->Logger_Dbg(
                __FUNCTION__,
                sprintf('2. Grades -> min: %s, half: %s, max: %s -> Level: %.2f', $blindLevelMin, $blindLevelHalf, $blindLevelMax, $blindLevel)
            );
        }

        // Begrenzung auf Profilgrenzen
        $blindLevel = $this->clampToProfile($blindLevel, $this->profileBlindLevel);

        // Lamellenposition berechnen (immer linear)
        $slatsLevelMin = $this->ReadPropertyFloat(self::PROP_MINIMUMSHADERELEVANTSLATSLEVEL);
        $slatsLevelMax = $this->ReadPropertyFloat(self::PROP_MAXIMUMSHADERELEVANTSLATSLEVEL);
        $slatsLevel    = ($slatsLevelMax - $slatsLevelMin) * $degreeOfShadowing + $slatsLevelMin;

        return [
            'BlindLevel' => $blindLevel,
            'SlatsLevel' => $slatsLevel
        ];
    }

    /**
     * Ermittelt die Zielpositionen für die Beschattung basierend auf der Helligkeit.
     *
     * Die Methode prüft zwei konfigurierbare Schwellwerte (hoch/niedrig) und gibt
     * die entsprechenden Positionen für Rollladen und Lamellen zurück, sofern
     * die Helligkeit den jeweiligen Schwellwert überschreitet.
     *
     * @param float $levelAct Aktueller Stand des Rollladens (für Hysterese-Berechnung in GetBrightness).
     * @return array{BlindLevel: float, SlatsLevel: float}|null Array mit Zielpositionen oder null, wenn keine Beschattung aktiv.
     */
    private function getPositionsOfShadowingByBrightness(float $levelAct): ?array
    {
        $activatorID = $this->ReadPropertyInteger(self::PROP_ACTIVATORIDSHADOWINGBRIGHTNESS);

        if (!IPS_VariableExists($activatorID)) {
            // Beschattung nach Helligkeit ist nicht konfiguriert
            return null;
        }
        if (!GetValue($activatorID)) {
            $this->addShadowingReason('nach Helligkeit nicht aktiviert');
            return null;
        }

        $brightnessID = $this->ReadPropertyInteger(self::PROP_BRIGHTNESSIDSHADOWINGBRIGHTNESS);
        if (!IPS_VariableExists($brightnessID)) {
            trigger_error(sprintf('Instance %s: BrightnessIDShadowingBrightness does not exist', $this->InstanceID));
            return null;
        }

        $thresholdIDHighBrightness = $this->ReadPropertyInteger(self::PROP_THRESHOLDIDHIGHBRIGHTNESS);
        $thresholdIDLessBrightness = $this->ReadPropertyInteger(self::PROP_THRESHOLDIDLESSBRIGHTNESS);
        if (!IPS_VariableExists($thresholdIDHighBrightness) && !IPS_VariableExists($thresholdIDLessBrightness)) {
            trigger_error(sprintf('Instance %s: Neither ThresholdIDHighBrightness nor ThresholdIDLowBrightness exist', $this->InstanceID));
            return null;
        }

        $positions  = null;
        $brightness =
            $this->GetBrightness(self::PROP_BRIGHTNESSIDSHADOWINGBRIGHTNESS, self::PROP_BRIGHTNESSAVGMINUTESSHADOWINGBRIGHTNESS, $levelAct, true);

        if (!isset($brightness)) {
            return null;
        }

        if (IPS_VariableExists($thresholdIDHighBrightness)) {
            $thresholdLessBrightness = GetValue($thresholdIDHighBrightness);
            if ($brightness >= $thresholdLessBrightness) {
                $positions['BlindLevel'] = $this->ReadPropertyFloat(self::PROP_BLINDLEVELHIGHBRIGHTNESSSHADOWINGBRIGHTNESS);
                $positions['SlatsLevel'] = $this->ReadPropertyFloat(self::PROP_SLATSLEVELHIGHBRIGHTNESSSHADOWINGBRIGHTNESS);
                $this->shadowingBrightnessInfo = $this->buildShadowingBrightnessInfo($brightnessID, $brightness, $thresholdIDHighBrightness, (float)$thresholdLessBrightness);
                $this->Logger_Dbg(
                    __FUNCTION__,
                    sprintf(
                        'active: %d, Beschattung bei hoher Helligkeit (%s/%s): BlindLevel: %s, SlatsLevel: %s',
                        (int)GetValue($activatorID),
                        $brightness,
                        $thresholdLessBrightness,
                        $positions['BlindLevel'],
                        $positions['SlatsLevel']
                    )
                );
                return $positions;
            }
        }

        if (IPS_VariableExists($thresholdIDLessBrightness)) {
            $thresholdBrightness = GetValue($thresholdIDLessBrightness);
            if ($brightness >= $thresholdBrightness) {
                $positions['BlindLevel'] = $this->ReadPropertyFloat(self::PROP_BLINDLEVELLESSBRIGHTNESSSHADOWINGBRIGHTNESS);
                $positions['SlatsLevel'] = $this->ReadPropertyFloat(self::PROP_SLATSLEVELLESSBRIGHTNESSSHADOWINGBRIGHTNESS);
                $this->shadowingBrightnessInfo = $this->buildShadowingBrightnessInfo($brightnessID, $brightness, $thresholdIDLessBrightness, (float)$thresholdBrightness);
                $this->Logger_Dbg(
                    __FUNCTION__,
                    sprintf(
                        'active: %d, Beschattung bei niedriger Helligkeit (%s/%s): BlindLevel: %s, SlatsLevel: %s',
                        (int)GetValue($activatorID),
                        $brightness,
                        $thresholdBrightness,
                        $positions['BlindLevel'],
                        $positions['SlatsLevel']
                    )
                );

                return $positions;
            }
        }

        // aktiviert, aber Helligkeit unter den konfigurierten Schwellwerten
        $threshold = IPS_VariableExists($thresholdIDLessBrightness) ? GetValue($thresholdIDLessBrightness)
            : (IPS_VariableExists($thresholdIDHighBrightness) ? GetValue($thresholdIDHighBrightness) : null);
        if ($threshold !== null) {
            $thresholdIDForFormat = IPS_VariableExists($thresholdIDLessBrightness) ? $thresholdIDLessBrightness : $thresholdIDHighBrightness;
            $this->addShadowingReason(
                sprintf(
                    'nach Helligkeit: Helligkeit %s unter Schwellwert %s',
                    $this->formatBrightnessForTrace($brightnessID, $brightness),
                    $this->formatBrightnessForTrace($thresholdIDForFormat, (float)$threshold)
                )
            );
        }

        return null;
    }


    /**
     * Prüft, ob die automatische Bewegung aufgrund eines manuellen Eingriffs gesperrt ist.
     *
     * @param float      $blindLevelAct       Aktuelle Behanghöhe des Aktors.
     * @param float|null $slatsLevelAct       Aktuelle Lamellenposition des Aktors (null falls nicht vorhanden).
     * @param int        $tsBlindLastMovement Zeitstempel der letzten physischen Änderung am Aktor.
     * @param bool       $isDay               Status, ob es aktuell Tag ist.
     * @param int        $tsIsDayChanged      Zeitstempel des letzten Wechsels zwischen Tag und Nacht.
     * @param int        $tsAutomatik         Zeitstempel der letzten automatischen Bewegung durch das Modul.
     *
     * @return bool True, wenn die Bewegung blockiert werden soll, andernfalls False.
     * @throws \JsonException Bei Fehlern während der Status-Dekodierung.
     */
    /**
     * Prüft, ob eine automatische Fahrt wegen einer (zuvor erkannten) manuellen Bedienung gesperrt ist.
     *
     * @return array{block: bool, reason: string} block = true, wenn gesperrt; reason = Klartextbegründung (nur bei block = true gefüllt).
     */
    private function shouldBlockMovement(
        float $blindLevelAct,
        ?float $slatsLevelAct,
        int $tsBlindLastMovement,
        bool $isDay,
        int $tsIsDayChanged,
        int $tsAutomatik
    ): array {
        // 1. Karenzzeit nach automatischer Bewegung prüfen
        if ($tsBlindLastMovement <= strtotime('+5 sec', $tsAutomatik)) {
            return ['block' => false, 'reason' => ''];
        }

        // 2. Manuellen Status synchronisieren (Logik für ATTR_MANUALMOVEMENT extrahiert)
        $manualState = $this->syncManualMovementAttribute($blindLevelAct, $slatsLevelAct, $tsBlindLastMovement);
        $tsManual    = $manualState['timeStamp'];

        if ($tsManual === null || $tsManual <= $tsIsDayChanged) {
            return ['block' => false, 'reason' => ''];
        }

        // 3. Sperr-Logik
        if (!$isDay) {
            $reason = sprintf('manuelle Bedienung in der Nacht (%s)', date('H:i', $tsManual));
            $this->Logger_Dbg(__FUNCTION__, 'Sperre: ' . $reason);
            return ['block' => true, 'reason' => $reason];
        }

        // Tagsüber: Sperre, nur wenn geschlossen oder innerhalb der Deaktivierungszeit
        $deactivationTimeManuSecs = $this->ReadPropertyInteger(self::PROP_DEACTIVATIONMANUALMOVEMENT) * 60;

        $isClosed = ($blindLevelAct === $this->profileBlindLevel['MaxValue']) &&
                    ($slatsLevelAct === ($this->profileSlatsLevel['MaxValue'] ?? null));

        if ($isClosed) {
            $reason = sprintf('manuell vollständig geschlossen (%s)', date('H:i', $tsManual));
        } elseif ($deactivationTimeManuSecs === 0) {
            $reason = sprintf('manuelle Bedienung am Tag (%s), Sperre bis zum nächsten Tag/Nacht-Wechsel', date('H:i', $tsManual));
        } elseif (strtotime("+ $deactivationTimeManuSecs seconds", $tsManual) > time()) {
            $reason = sprintf(
                'manuelle Bedienung am Tag (%s), Sperre bis %s',
                date('H:i', $tsManual),
                date('H:i', strtotime("+ $deactivationTimeManuSecs seconds", $tsManual))
            );
        } else {
            // Zeit abgelaufen -> Automatik wieder freigeben
            $this->resetManualMovement();
            return ['block' => false, 'reason' => ''];
        }

        $this->Logger_Dbg(__FUNCTION__, 'Sperre: ' . $reason);
        return ['block' => true, 'reason' => $reason];
    }


    /**
     * Synchronisiert das Attribut für manuelle Bewegungen basierend auf aktuellen Aktorwerten.
     *
     * @param float      $blindLevelAct       Aktuelle Behanghöhe des Aktors.
     * @param float|null $slatsLevelAct       Aktuelle Lamellenposition des Aktors (null falls nicht vorhanden).
     * @param int        $tsBlindLastMovement Zeitstempel der letzten physischen Änderung am Aktor.
     *
     * @return array{timeStamp: int|null, blindLevel: float|null, slatsLevel: float|null} Der wirksame manuelle Status.
     * @throws \JsonException Bei Fehlern während der JSON-Kodierung/Dekodierung.
     */
    private function syncManualMovementAttribute(float $blindLevelAct, ?float $slatsLevelAct, int $tsBlindLastMovement): array
    {
        $currentManual = json_decode($this->ReadAttributeString(self::ATTR_MANUALMOVEMENT), true, 512, JSON_THROW_ON_ERROR);

        // Nur fortfahren, wenn ein neuer manueller Zeitstempel erkannt wurde
        if ($tsBlindLastMovement === $currentManual['timeStamp']) {
            return $currentManual;
        }

        $newManual = ['timeStamp' => $tsBlindLastMovement, 'blindLevel' => $blindLevelAct, 'slatsLevel' => $slatsLevelAct];

        // Neuen Zustand speichern (im Probelauf nicht persistieren)
        if (!$this->dryRun) {
            $this->WriteAttributeString(self::ATTR_MANUALMOVEMENT, json_encode($newManual, JSON_THROW_ON_ERROR));
        }

        $this->Logger_Dbg(
            __FUNCTION__,
            sprintf(
                'Rollladen wurde manuell gesetzt. blindLevelAct: %.2f, slatsLevelAct: %s, TimestampManual: %s, Diff: %s s',
                $blindLevelAct,
                $slatsLevelAct !== null ? sprintf('%.2f', $slatsLevelAct) : 'null',
                $this->FormatTimeStamp($tsBlindLastMovement),
                time() - $tsBlindLastMovement
            )
        );

        // Informationstext generieren und loggen (im Probelauf nicht)
        if (!$this->dryRun) {
            $this->logManualMovementInfo($blindLevelAct, $slatsLevelAct);
        }

        return $newManual;
    }

    private function logManualMovementInfo(float $blindLevelAct, ?float $slatsLevelAct): void
    {
        $blindLevelClosed = $this->profileBlindLevel['MaxValue'];
        $blindLevelOpened = $this->profileBlindLevel['MinValue'];

        if ($slatsLevelAct === null) {
            if ($blindLevelAct === $blindLevelClosed) {
                $this->Logger_Inf(sprintf('\'%s\' wurde manuell geschlossen.', $this->objectName));
            } elseif ($blindLevelAct === $blindLevelOpened) {
                $this->Logger_Inf(sprintf('\'%s\' wurde manuell geöffnet.', $this->objectName));
            } else {
                $percent = ($blindLevelAct - $blindLevelOpened) / ($blindLevelClosed - $blindLevelOpened);
                $this->Logger_Inf(sprintf('\'%s\' wurde manuell auf %.0f%% gefahren.', $this->objectName, 100 * $percent));
            }
            return;
        }

        // Logik mit Lamellen
        $slatsLevelClosed = $this->profileSlatsLevel['MaxValue'];
        $slatsLevelOpened = $this->profileSlatsLevel['MinValue'];

        if (($blindLevelAct === $blindLevelClosed) && ($slatsLevelAct === $slatsLevelClosed)) {
            $this->Logger_Inf(sprintf('\'%s\' wurde manuell geschlossen.', $this->objectName));
        } elseif (($blindLevelAct === $blindLevelOpened) && ($slatsLevelAct === $slatsLevelOpened)) {
            $this->Logger_Inf(sprintf('\'%s\' wurde manuell geöffnet.', $this->objectName));
        } else {
            $blindPercent = ($blindLevelAct - $blindLevelOpened) / ($blindLevelClosed - $blindLevelOpened);
            $slatsPercent = ($slatsLevelAct - $slatsLevelOpened) / ($slatsLevelClosed - $slatsLevelOpened);

            $this->Logger_Inf(
                sprintf(
                    '\'%s\' wurde manuell auf %.0f%%(Höhe), %.0f%%(Lamellen) gefahren.',
                    $this->objectName,
                    100 * $blindPercent,
                    100 * $slatsPercent
                )
            );
        }
    }


    //-----------------------------------------------
    /**
     * Bewegt den Rollladen und optional die Lamellen auf eine prozentuale Position.
     *
     * @param int $percentBlindClose Position des Rollladens (0-100%).
     * @param int|null $percentSlatsClose Position der Lamellen (0-100%) oder null, falls nicht vorhanden.
     * @param int $deactivationTimeAuto Zeit in Sekunden, für die die Automatik deaktiviert werden soll.
     * @param string $hint Grund der Bewegung für das Logging.
     * @return bool True, wenn mindestens eine Bewegung erfolgreich eingeleitet wurde.
     */
    public function MoveBlind(int $percentBlindClose, ?int $percentSlatsClose, int $deactivationTimeAuto, string $hint): bool
    {
        if (IPS_GetInstance($this->InstanceID)['InstanceStatus'] !== IS_ACTIVE) {
            return false;
        }

        $this->Logger_Dbg(
            __FUNCTION__,
            sprintf(
                'percentBlindClose: %s, percentSlatClose: %s, deactivationTimeAuto: %s, hint: %s',
                $percentBlindClose,
                $percentSlatsClose ?? 'null',
                $deactivationTimeAuto,
                $hint
            )
        );


        assert($percentBlindClose >= 0 && $percentBlindClose <= 100);
        assert(is_null($percentSlatsClose) || ($percentSlatsClose >= 0 && $percentSlatsClose <= 100));

        // Profile laden für die Umrechnung von Prozent in Aktor-Werte
        $this->profileBlindLevel = $this->GetPresentationInformation(self::PROP_BLINDLEVELID);

        if ($this->profileBlindLevel === null) {
            return false;
        }

        $tsAutomatic = $this->ReadAttributeInteger(self::ATTR_TIMESTAMP_AUTOMATIC);
        $moveBladeOk = $this->MoveToPosition(self::PROP_BLINDLEVELID, $percentBlindClose, $tsAutomatic, $deactivationTimeAuto, $hint);

        // Optionale Lamellensteuerung ausführen
        if (IPS_VariableExists($this->ReadPropertyInteger(self::PROP_SLATSLEVELID))) {
            $this->profileSlatsLevel = $this->GetPresentationInformation(self::PROP_SLATSLEVELID);
            $moveSlatsOk             = $this->MoveToPosition(self::PROP_SLATSLEVELID, $percentSlatsClose, $tsAutomatic, $deactivationTimeAuto, $hint);

            return $moveBladeOk || $moveSlatsOk;
        }

        return $moveBladeOk;
    }

    /**
     * Fährt den Rollladen (und ggf. Lamellen) auf eine vordefinierte Beschattungsposition.
     */
    private function MoveBlindToShadowingPosition(int $percentShadowing): bool
    {
        if (IPS_GetInstance($this->InstanceID)['InstanceStatus'] !== IS_ACTIVE) {
            return false;
        }

        $this->Logger_Dbg(__FUNCTION__, sprintf('percentClose: %s', $percentShadowing));

        if (($percentShadowing < 0) || ($percentShadowing > 100)) {
            return false;
        }

        // globale Instanzvariablen setzen
        $this->profileBlindLevel = $this->GetPresentationInformation(self::PROP_BLINDLEVELID);

        if ($this->profileBlindLevel === null) {
            return false;
        }

        $positions = $this->calculatePositionsFromShadowingDegree($percentShadowing / 100);
        $hint      = sprintf('%s%% Beschattung', $percentShadowing);

        // Hauptbehang bewegen
        $blindLevel = $this->calculateNormalizedLevel($positions['BlindLevel'], $this->profileBlindLevel);
        $success    = $this->MoveToPosition(self::PROP_BLINDLEVELID, $blindLevel, 0, 0, $hint);

        // Optionale Lamellen bewegen
        if (IPS_VariableExists($this->ReadPropertyInteger(self::PROP_SLATSLEVELID))) {
            $this->profileSlatsLevel = $this->GetPresentationInformation(self::PROP_SLATSLEVELID);
            $slatsLevel              = $this->calculateNormalizedLevel($positions['SlatsLevel'], $this->profileSlatsLevel);
            $moveSlatsOk             = $this->MoveToPosition(self::PROP_SLATSLEVELID, $slatsLevel, 0, 0, $hint);

            return $success || $moveSlatsOk;
        }

        return $success;
    }

    private function MoveToPosition(string $propName, int $percentClose, int $tsAutomatic, int $deactivationTimeAuto, string $hint): bool
    {
        $positionID = $this->ReadPropertyInteger($propName);
        if (!IPS_VariableExists($positionID)) {
            return false;
        }

        $profile = $this->GetPresentationInformation($propName);
        if ($profile === null) {
            return false;
        }

        // 1. Double-Movement-Check (Spamschutz)
        if ($this->isSameMovementRecently($propName, $percentClose)) {
            if (!$this->dryRun) {
                $this->WriteAttributeInteger(self::ATTR_TIMESTAMP_AUTOMATIC, time());
            }
            return false;
        }

        // 2. Zielwert berechnen
        $positionNew = $this->calculateValueFromPercent($percentClose, $profile);
        $positionAct = (float)GetValue($positionID);

        // 3. Bewegungs-Validierung (Early Returns für bessere Lesbarkeit)
        if (!$this->shouldPerformMovement($propName, $positionID, $positionAct, $positionNew, $profile, $tsAutomatic, $deactivationTimeAuto)) {
            return false;
        }

        // Im Probelauf ("Erklären") wird die Fahrt nur ermittelt, aber nicht ausgeführt
        if ($this->dryRun) {
            return true;
        }

        // 4. Ausführung
        if (!RequestAction($positionID, $positionNew)) {
            $this->logMovementError($positionID, $propName, $percentClose);
            return false;
        }

        // 5. Nachbereitung
        $ret = $this->waitUntilBlindLevelIsReached($propName, $positionNew);
        $this->finalizeMovement($propName, $percentClose, $positionAct, $positionNew, $hint, $ret);

        return $ret;
    }

    private function calculateValueFromPercent(int $percent, array $profile): float
    {
        $min = $profile['MinValue'];
        $max = $profile['MaxValue'];

        if ($this->isMinMaxReversed($min, $max)) {
            return $max + (1 - $percent / 100) * ($min - $max);
        }

        return $min + ($percent / 100) * ($max - $min);
    }

    private function isSameMovementRecently(string $propName, int $percentClose): bool
    {
        $lastMove = json_decode($this->ReadAttributeString(self::ATTR_LASTMOVE . $propName), true, 512, JSON_THROW_ON_ERROR);
        $isSame = (int)$lastMove['percentClose'] === $percentClose;
        $isRecent = $lastMove['timeStamp'] > strtotime('-' . self::IGNORE_MOVEMENT_TIME . ' secs');

        if ($isSame && $isRecent) {
            $this->Logger_Dbg(__FUNCTION__, "Move ignored! Same position recently.");
            $this->moveSkipReason = 'gleiche Zielposition wurde gerade erst angefahren';
            return true;
        }
        return false;
    }

    /**
     * Prüft alle Bedingungen, die eine physikalische Fahrt verhindern könnten.
     */
    private function shouldPerformMovement(string $propName, int $id, float $act, float $new, array $profile, int $tsAuto, int $deactivation): bool
    {
        $diffPercentage = abs(($new - $act) / ($profile['MaxValue'] - $profile['MinValue']));
        $timeSinceAuto  = time() - $tsAuto;

        $minMove    = $this->ReadPropertyFloat(self::PROP_MINMOVEMENT) / 100;
        $minMoveEnd = $this->ReadPropertyFloat(self::PROP_MINMOVEMENTATENDPOSITION) / 100;

        // 1. Sperrzeit noch aktiv?
        if ($timeSinceAuto < $deactivation) {
            $this->Logger_Dbg(__FUNCTION__, "#$id($propName): Sperrzeit ($deactivation s) noch nicht erreicht ($timeSinceAuto s).");
            $this->moveSkipReason = sprintf('Karenzzeit nach Automatikfahrt aktiv (noch %d s)', $deactivation - $timeSinceAuto);
            return false;
        }

        // 2. Toleranzbereich (bereits erreicht)?
        if ($diffPercentage <= (self::ALLOWED_TOLERANCE_MOVEMENT / 100)) {
            $this->Logger_Dbg(__FUNCTION__, "#$id($propName): Position $act bereits im Toleranzbereich.");
            $this->moveSkipReason = 'Zielposition bereits erreicht';
            return false;
        }

        // 3. Zu kleine Bewegung (außer es ist eine Endposition)
        $isEndPosition = in_array($new, [$profile['MinValue'], $profile['MaxValue']], false);
        if (!$isEndPosition && ($diffPercentage < $minMove)) {
            $this->Logger_Dbg(__FUNCTION__, sprintf("#$id($propName): Bewegung zu klein (%.2f%% < %.2f%%).", $diffPercentage * 100, $minMove * 100));
            $this->moveSkipReason = sprintf('Änderung zu gering (%.0f %% < %.0f %%)', $diffPercentage * 100, $minMove * 100);
            return false;
        }

        // 4. Zu kleine Bewegung zur Endposition
        if ($diffPercentage < $minMoveEnd) {
            $this->Logger_Dbg(__FUNCTION__, sprintf("#$id($propName): Endposition fast erreicht (Differenz %.2f%%).", $diffPercentage * 100));
            $this->moveSkipReason = sprintf('Endposition nahezu erreicht (Differenz %.0f %%)', $diffPercentage * 100);
            return false;
        }

        return true; // Alle Prüfungen bestanden, Fahrt frei!
    }

    /**
     * Protokolliert einen Fehler, wenn RequestAction fehlgeschlagen ist.
     */
    private function logMovementError(int $id, string $propName, int $percent): void
    {
        $parentName = IPS_GetName(IPS_GetParent($id));

        $this->Logger_Err(
            sprintf(
                '\'%s\': ID %s (%s): Fehler beim Setzen des Wertes. (Value = %s, Parent: "%s").',
                $this->objectName,
                $id,
                $propName,
                $percent,
                $parentName
            )
        );
    }

    /**
     * Schließt eine erfolgreiche Bewegung ab (Attribute setzen, Logging).
     */
    private function finalizeMovement(string $propName, int $percent, float $act, float $new, string $hint, bool $reached): void
    {
        $id = $this->ReadPropertyInteger($propName);

        // 1. Automatik-Zeitstempel für die manuelle Sperrlogik merken
        $this->WriteAttributeInteger(self::ATTR_TIMESTAMP_AUTOMATIC, time());

        // 2. Letzte Bewegung persistieren (für isSameMovementRecently)
        $this->WriteAttributeString(
            self::ATTR_LASTMOVE . $propName,
            json_encode(['timeStamp' => time(), 'percentClose' => $percent, 'hint' => $hint], JSON_THROW_ON_ERROR)
        );

        // 3. Debug-Ausgaben
        $this->Logger_Dbg(__FUNCTION__, sprintf('#%s(%s): %s to %s', $id, $propName, $act, $new));
        $this->Logger_Dbg(
            __FUNCTION__,
            "$this->objectName: TimestampAutomatik: " . $this->FormatTimeStamp(time())
        );

        // 4. Benutzer-Info schreiben (nur wenn Ziel erreicht wurde)
        if ($reached) {
            $this->WriteInfo($propName, $new, $hint);
        }
    }

    private function waitUntilBlindLevelIsReached(string $propName, $positionNew): bool
    {
        $levelID                  = $this->ReadPropertyInteger($propName);
        $minMovementAtEndPosition = $this->ReadPropertyFloat(self::PROP_MINMOVEMENTATENDPOSITION);

        $profile         = $this->GetPresentationInformation($propName);
        $percentCloseNew = ($positionNew - $profile['MinValue']) / ($profile['MaxValue'] - $profile['MinValue']) * 100;
        if ($this->isMinMaxReversed($profile['MinValue'], $profile['MaxValue'])) {
            $percentCloseNew = 100 - $percentCloseNew;
        }

        for ($i = 0; $i < self::MOVEMENT_WAIT_TIME; $i++) { //wir warten maximal 90 Sekunden
            $currentValue        = GetValue($levelID);
            $percentCloseCurrent = ($currentValue - $profile['MinValue']) / ($profile['MaxValue'] - $profile['MinValue']) * 100;

            if ($this->isMinMaxReversed($profile['MinValue'], $profile['MaxValue'])) {
                $percentCloseCurrent = 100 - $percentCloseCurrent;
            }

            if (abs($percentCloseNew - $percentCloseCurrent) > $minMovementAtEndPosition) {
                sleep(1);
            } else {
                $this->Logger_Dbg(
                    __FUNCTION__,
                    sprintf(
                        '#%s(%s): Position reached (Value: %s, Diff: %.2f) at %s.',
                        $levelID,
                        $propName,
                        $currentValue,
                        $percentCloseNew - $percentCloseCurrent,
                        $this->FormatTimeStamp(IPS_GetVariable($levelID)['VariableChanged'])
                    )
                );
                return true;
            }
        }

        $percentCloseCurrent = (GetValue($levelID) - $profile['MinValue']) / ($profile['MaxValue'] - $profile['MinValue']) * 100;

        if ($this->isMinMaxReversed($profile['MinValue'], $profile['MaxValue'])) {
            $percentCloseCurrent = 100 - $percentCloseCurrent;
        }
        $this->Logger_Inf(
            sprintf(
                '\'%s\': Die Statusvariable #%s(%s) hat die Zielposition (%s%% geschlossen) nicht erreicht! (Differenz: %.2f%%).',
                $this->objectName,
                $levelID,
                $propName,
                $percentCloseNew,
                $percentCloseNew - $percentCloseCurrent
            )
        );

        return false;
    }

    private function WriteInfo(string $propName, float $rLevelneu, string $hint): void
    {
        // Konfiguration laden basierend auf dem Property-Namen
        $isBlind = ($propName === self::PROP_BLINDLEVELID);
        $profile = $isBlind ? $this->profileBlindLevel : $this->profileSlatsLevel;

        // Prozentberechnung normalisieren
        $min = (float)$profile['MinValue'];
        $max = (float)$profile['MaxValue'];

        // Status-Text ermitteln
        if (abs($rLevelneu - $max) < PHP_FLOAT_EPSILON) {
            $actionText = 'geschlossen';
        } elseif (abs($rLevelneu - $min) < PHP_FLOAT_EPSILON) {
            $actionText = 'geöffnet';
        } else {
            // Division durch Null verhindern, falls Min == Max (unwahrscheinlich, aber sicher ist sicher)
            $range        = ($max - $min);
            $levelPercent = $range != 0 ? ($rLevelneu - $min) / $range : 0;
            $actionText   = sprintf('auf %.0f%% gefahren', 100 * $levelPercent);
        }

        // Subjekt des Satzes bestimmen
        $subject = $isBlind ? sprintf("'%s' wurde", $this->objectName) : sprintf("Die Lamellen '%s' wurden", $this->objectName);

        // Nachricht zusammensetzen
        $logMessage = sprintf('%s %s.', $subject, $actionText);

        // Hinweis anhängen, falls vorhanden
        if ($hint !== '') {
            // Punkt am Ende entfernen und Hint in Klammern setzen
            $logMessage = rtrim($logMessage, '.') . " ($hint)";
        }

        $this->Logger_Inf($logMessage);
    }

    /**
     * Hängt eine Zeile an das Ablaufprotokoll des aktuellen Steuerungslaufs an.
     */
    private function addTrace(string $line): void
    {
        $this->decisionTrace[] = $line;
    }

    /**
     * Erzeugt eine lesbare Beschreibung des ermittelten Tag-/Nacht-Zustands inklusive der zugrunde liegenden Quellen.
     */
    private function buildDayStateTrace(array $dayState): string
    {
        $parts   = [];
        $wochenplan = sprintf('Wochenplan: %s', $dayState['isDayByTimeSchedule'] ? 'Tag' : 'Nacht');
        if (!empty($dayState['scheduleAuf']) || !empty($dayState['scheduleAb'])) {
            $wochenplan .= sprintf(' (%s–%s)', $dayState['scheduleAuf'] ?? '—', $dayState['scheduleAb'] ?? '—');
        }
        $parts[] = $wochenplan;
        if ($dayState['isDayByDayDetection'] !== null) {
            $parts[] = sprintf('Tagerkennung: %s', $dayState['isDayByDayDetection'] ? 'Tag' : 'Nacht');
        }
        if ($dayState['brightness'] !== null) {
            $parts[] = sprintf('Helligkeit: %s', GetValueFormattedEx($this->ReadPropertyInteger(self::PROP_BRIGHTNESSID), $dayState['brightness']));
        }

        return sprintf('%s (%s)', $dayState['isDay'] ? 'Tag' : 'Nacht', implode(', ', $parts));
    }

    /**
     * Dokumentiert die Entscheidung des aktuellen Steuerungslaufs im Entscheidungs-Trace (Debug-Log und
     * "Erklären"-Button) und - sofern aktiviert - in der Statusvariable LAST_DECISION. Damit bleibt
     * nachvollziehbar, warum sich der Rollladen bewegt hat oder eben nicht.
     *
     * In die Statusvariable wird nur geschrieben, wenn sich die Entscheidung gegenüber dem letzten Lauf
     * geändert hat. So werden wiederholt identische Einträge (z.B. dauerhaft "Zielposition bereits erreicht")
     * vermieden.
     *
     * @param bool   $bNoMove      true, wenn eine Bewegungssperre vorlag (es wurde kein Fahrbefehl ausgelöst).
     * @param string $blockReason  Begründung der Sperre (aus shouldBlockMovement).
     * @param array  $positionsNew Die ermittelte Zielposition (Rohwerte).
     * @param string $hint         Der Grund der ermittelten Zielposition (WP/Tag/Nacht/Beschattung/...).
     */
    private function traceDecisionResult(bool $bNoMove, string $blockReason, array $positionsNew, string $hint): void
    {
        $target  = $this->describeTargetPositions($positionsNew);
        $hintTxt = $hint !== '' ? $hint : '';

        if ($bNoMove) {
            // Sperre: es wurde gar kein Fahrbefehl ausgelöst
            $reason  = $blockReason !== '' ? $blockReason : 'Bewegungssperre aktiv';
            $message = sprintf('Keine Fahrt: %s.', $reason);
        } elseif ($this->moveSkipReason !== '') {
            // Fahrbefehl wurde geprüft, aber als nicht erforderlich verworfen (bereits erreicht, Karenzzeit, zu kleine Bewegung)
            $message = sprintf('Keine Fahrt: %s (Ziel: %s%s).', $this->moveSkipReason, $target, $hintTxt !== '' ? ', ' . $hintTxt : '');
        } else {
            // Fahrbefehl wurde ausgelöst
            $message = sprintf('Fahrt: %s%s.', $target, $hintTxt !== '' ? sprintf(' (%s)', $hintTxt) : '');
        }

        $this->addTrace('Ergebnis: ' . $message);
        $this->Logger_Dbg(__FUNCTION__, $message);

        // Statusvariable nur bei aktivierter Option und nur bei einer geänderten Entscheidung aktualisieren
        if (!$this->dryRun && $this->ReadPropertyBoolean(self::PROP_WRITELASTDECISION)
            && @$this->GetIDForIdent(self::VAR_IDENT_LAST_DECISION) !== false
            && $this->GetValue(self::VAR_IDENT_LAST_DECISION) !== $message) {
            $this->SetValue(self::VAR_IDENT_LAST_DECISION, $message);
        }
    }

    /**
     * Schreibt das vollständige Ablaufprotokoll des aktuellen Steuerungslaufs - sofern aktiviert - als HTML
     * in die Statusvariable DECISION_TRACE. So kann der Anwender in der Visualisierung jederzeit nachvollziehen,
     * wie die Entscheidung des letzten Laufs zustande gekommen ist.
     *
     * Es wird bei jedem (echten) Lauf geschrieben, da der Inhalt zusätzlich einen Zeitstempel enthält.
     */
    private function writeDecisionTraceVariable(): void
    {
        if ($this->dryRun
            || !$this->ReadPropertyBoolean(self::PROP_WRITEDECISIONTRACE)
            || @$this->GetIDForIdent(self::VAR_IDENT_DECISION_TRACE) === false) {
            return;
        }

        $this->SetValue(self::VAR_IDENT_DECISION_TRACE, $this->buildDecisionTraceHtml());
    }

    /**
     * Formatiert das aktuelle Ablaufprotokoll ($this->decisionTrace) als HTML-Dokument für die Statusvariable
     * DECISION_TRACE. Jede Trace-Zeile der Form "Label: Wert" wird als eigene Tabellenzeile dargestellt; die
     * Ergebniszeile wird hervorgehoben.
     */
    private function buildDecisionTraceHtml(): string
    {
        $rows = '';
        foreach ($this->decisionTrace as $line) {
            if ($line === '') {
                continue;
            }

            $isResult  = str_starts_with($line, 'Ergebnis:');
            $rowStyle  = $isResult ? ' style="font-weight:bold;"' : '';
            $cellStyle = 'padding:2px 8px 2px 0;vertical-align:top;';

            $pos = strpos($line, ': ');
            if ($pos !== false) {
                $label = htmlspecialchars(substr($line, 0, $pos), ENT_QUOTES, 'UTF-8');
                $value = htmlspecialchars(substr($line, $pos + 2), ENT_QUOTES, 'UTF-8');
                $rows  .= sprintf(
                    '<tr%s><td style="%swhite-space:nowrap;">%s</td><td style="%s">%s</td></tr>',
                    $rowStyle,
                    $cellStyle,
                    $label,
                    $cellStyle,
                    $value
                );
            } else {
                $rows .= sprintf(
                    '<tr%s><td colspan="2" style="%s">%s</td></tr>',
                    $rowStyle,
                    $cellStyle,
                    htmlspecialchars($line, ENT_QUOTES, 'UTF-8')
                );
            }
        }

        $heading   = htmlspecialchars($this->Translate('Last Decision Trace'), ENT_QUOTES, 'UTF-8');
        $timestamp = htmlspecialchars(date('d.m.Y H:i:s'), ENT_QUOTES, 'UTF-8');

        return sprintf(
            '<div style="font-family:sans-serif;font-size:13px;">'
            . '<div style="font-weight:bold;margin-bottom:4px;">%s</div>'
            . '<table style="border-collapse:collapse;">%s</table>'
            . '<div style="color:#888;margin-top:6px;font-size:11px;">%s</div>'
            . '</div>',
            $heading,
            $rows,
            $timestamp
        );
    }

    /**
     * Erzeugt eine für den Anwender lesbare Beschreibung einer Zielposition (Behang und ggf. Lamellen).
     */
    private function describeTargetPositions(array $positions): string
    {
        $blindLevel = $positions['BlindLevel'];

        if (!isset($positions['SlatsLevel']) || $this->profileSlatsLevel === null) {
            return $this->describeLevel($blindLevel, $this->profileBlindLevel);
        }

        $slatsLevel = $positions['SlatsLevel'];

        if (($blindLevel === $this->profileBlindLevel['MaxValue']) && ($slatsLevel === $this->profileSlatsLevel['MaxValue'])) {
            return 'geschlossen';
        }
        if (($blindLevel === $this->profileBlindLevel['MinValue']) && ($slatsLevel === $this->profileSlatsLevel['MinValue'])) {
            return 'geöffnet';
        }

        return sprintf(
            'Höhe %s, Lamellen %s',
            $this->describeLevel($blindLevel, $this->profileBlindLevel),
            $this->describeLevel($slatsLevel, $this->profileSlatsLevel)
        );
    }

    /**
     * Wandelt einen Rohwert anhand des Profils in einen lesbaren Text um (geöffnet/geschlossen/X % geschlossen).
     */
    private function describeLevel(float $rawLevel, array $profile): string
    {
        $min = (float)$profile['MinValue']; // geöffnet
        $max = (float)$profile['MaxValue']; // geschlossen

        if (abs($rawLevel - $max) < PHP_FLOAT_EPSILON) {
            return 'geschlossen';
        }
        if (abs($rawLevel - $min) < PHP_FLOAT_EPSILON) {
            return 'geöffnet';
        }

        $range   = $max - $min;
        $percent = abs($range) > PHP_FLOAT_EPSILON ? (($rawLevel - $min) / $range) * 100 : 0;
        return sprintf('%.0f %% geschlossen', $percent);
    }

    private function checkTimeTable(): int
    {
        $eventScheduleGroups = IPS_GetEvent($this->ReadPropertyInteger(self::PROP_WEEKLYTIMETABLEEVENTID))['ScheduleGroups'];

        foreach ($eventScheduleGroups as $scheduleGroup) {
            $countID1 = $this->CountNumberOfPointsWithActionId($scheduleGroup['Points'], 1); //down
            $countID2 = $this->CountNumberOfPointsWithActionId($scheduleGroup['Points'], 2); //up

            if (($countID1 + $countID2) === 0) {
                $this->Logger_Dbg(
                    __FUNCTION__,
                    sprintf(
                        'Invalid TimeTable: No Points with ActionID 1 or 2 found. (ScheduleGroup: %s)',
                        json_encode($scheduleGroup, JSON_THROW_ON_ERROR)
                    )
                );
                return self::STATUS_INST_TIMETABLE_IS_INVALID;
            }

            if ($countID2 > 1) {
                $this->Logger_Dbg(
                    __FUNCTION__,
                    sprintf(
                        'Invalid TimeTable: More (%s) than one Point with ActionID 2. (ScheduleGroup: %s)',
                        $countID2,
                        json_encode($scheduleGroup, JSON_THROW_ON_ERROR)
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

        $isDayIndicatorID = $this->ReadPropertyInteger(self::PROP_ISDAYINDICATORID);

        if (!IPS_VariableExists($isDayIndicatorID)
            && (!IPS_VariableExists($this->ReadPropertyInteger(self::PROP_BRIGHTNESSID))
                || !IPS_VariableExists(
                    $this->ReadPropertyInteger(self::PROP_BRIGHTNESSTHRESHOLDID)
                ))) {
            return null;
        }

        if (IPS_VariableExists($isDayIndicatorID)) {
            $isDayIndicator = GetValueBoolean($isDayIndicatorID);
            $this->Logger_Dbg(__FUNCTION__, sprintf('DayIndicator (#%s): %d', $isDayIndicatorID, $isDayIndicator));
            $isDayDayDetection = $isDayIndicator;
        } else {
            //optional Values
            if ($this->ReadPropertyInteger(self::PROP_BRIGHTNESSID)) {
                $brightness = $this->GetBrightness(self::PROP_BRIGHTNESSID, self::PROP_BRIGHTNESSAVGMINUTES, $levelAct, false);
            }

            $brightnessThresholdID = $this->ReadPropertyInteger(self::PROP_BRIGHTNESSTHRESHOLDID);
            if ($brightnessThresholdID) {
                $brightnessThreshold = GetValue($brightnessThresholdID);
            }

            if (isset($brightness, $brightnessThreshold)) {
                $this->Logger_Dbg(__FUNCTION__, sprintf('Brightness: %.1f, Threshold: %.1f', $brightness, $brightnessThreshold));
                $isDayDayDetection = $brightness > $brightnessThreshold;
            }
        }

        if (!isset($isDayDayDetection)) {
            return null;
        }

        // übersteuernde Tageszeiten auswerten
        $dayStart_ts = false;
        $dayEnd_ts   = false;

        if (IPS_VariableExists($this->ReadPropertyInteger(self::PROP_DAYSTARTID))) {
            $dayStart    = GetValueString($this->ReadPropertyInteger(self::PROP_DAYSTARTID));
            $dayStart_ts = strtotime($dayStart);
            if ($dayStart_ts === false) {
                $this->Logger_Dbg(__FUNCTION__, sprintf('No valid DayStart found: \'%s\' (ignored)', $dayStart));
            } else {
                $this->Logger_Dbg(__FUNCTION__, sprintf('DayStart found: %s', $dayStart));
            }
        }

        if (IPS_VariableExists($this->ReadPropertyInteger(self::PROP_DAYENDID))) {
            $dayEnd    = GetValueString($this->ReadPropertyInteger(self::PROP_DAYENDID));
            $dayEnd_ts = strtotime($dayEnd);
            if ($dayEnd_ts === false) {
                $this->Logger_Dbg(__FUNCTION__, sprintf('No valid DayEnd found: \'%s\' (ignored)', $dayEnd));
            } else {
                $this->Logger_Dbg(__FUNCTION__, sprintf('DayEnd found: %s', $dayEnd));
            }
        }

        if ($dayStart_ts && $dayEnd_ts) {
            $isDayDayDetection = (time() > $dayStart_ts) && (time() < $dayEnd_ts);
        } elseif ($dayStart_ts && (time() < strtotime('12:00'))) {
            $isDayDayDetection = time() > $dayStart_ts;
        } elseif ($dayEnd_ts && (time() > strtotime('12:00'))) {
            $isDayDayDetection = time() < $dayEnd_ts;
        }

        return $isDayDayDetection;
    }

    private function getIsDayByTimeSchedule(?string &$heute_auf = null, ?string &$heute_ab = null): ?bool
    {
        //Ermitteln, welche Zeiten heute und gestern gelten
        // heute_auf/heute_ab werden zusätzlich per Referenz zurückgegeben und fließen in den
        // Ablauf-Trace (Tageszeit-Zeile) ein.

        $heute_auf = null;
        $heute_ab  = null;

        if (!$this->getUpAndDownPoints($heute_auf, $heute_ab)) {
            return null;
        }
        $this->SendDebug(__FUNCTION__, sprintf('heute_auf: %s, heute_ab: %s', $heute_auf, $heute_ab), 0);
        return ($heute_auf !== null) && (time() >= strtotime($heute_auf)) && ($heute_ab === null || (time() <= strtotime($heute_ab)));
    }

    //-------------------------------------
    private function getUpAndDownPoints(?string &$heute_auf, ?string &$heute_ab): bool
    {
        // An Feiertagen und Urlaubstagen können abweichende Tage gelten
        $holidayIndicatorID = $this->ReadPropertyInteger(self::PROP_HOLIDAYINDICATORID);
        if (IPS_VariableExists($holidayIndicatorID) && ($this->ReadPropertyInteger(self::PROP_DAYUSEDWHENHOLIDAY) !== 0)
            && GetValueBoolean(
                $this->ReadPropertyInteger(self::PROP_HOLIDAYINDICATORID)
            )) {
            $weekDay = $this->ReadPropertyInteger(self::PROP_DAYUSEDWHENHOLIDAY);
        } else {
            $weekDay = (int)date('N');
        }

        //Ermitteln, welche Zeiten heute laut Wochenplan gelten
        if (!$this->getUpDownTime($weekDay, $heute_auf, $heute_ab)) {
            //der Wochenplan ist ungültig
            return false;
        }

        //gibt es übersteuernde Zeiten?
        $idWakeUpTime = $this->ReadPropertyInteger(self::PROP_WAKEUPTIMEID);
        if (IPS_VariableExists($idWakeUpTime)) {
            $heute_auf_ts = strtotime(GetValueString($idWakeUpTime));
            if ($heute_auf_ts === false) {
                $this->Logger_Dbg(__FUNCTION__, sprintf('No valid WakeUpTime found: \'%s\' (ignored)', GetValueString($idWakeUpTime)));
            } else {
                // es wurde eine gültige Zeit gefunden
                $heute_auf = date('H:i', $heute_auf_ts + $this->ReadPropertyInteger(self::PROP_WAKEUPTIMEOFFSET) * 60);
                $this->Logger_Dbg(__FUNCTION__, sprintf('WakeUpTime found: %s', $heute_auf));
            }
        }

        $idBedTime = $this->ReadPropertyInteger(self::PROP_BEDTIMEID);
        if (IPS_VariableExists($idBedTime)) {
            $heute_ab_ts = strtotime(GetValueString($idBedTime));
            if ($heute_ab_ts === false) {
                $this->Logger_Dbg(__FUNCTION__, sprintf('No valid BedTime found: \'%s\' (ignored)', GetValueString($idBedTime)));
            } else {
                // es wurde eine gültige Zeit gefunden
                $heute_ab = date('H:i', $heute_ab_ts + $this->ReadPropertyInteger(self::PROP_BEDTIMEOFFSET) * 60);
                $this->Logger_Dbg(__FUNCTION__, sprintf('BedTime: %s', $heute_ab));
            }
        }
        return true;
    }

    //-----------------------------------------------
    private function getUpDownTime(int $weekDay, ?string &$auf, ?string &$ab): bool
    {
        $weeklyTimeTableEventId = $this->ReadPropertyInteger(self::PROP_WEEKLYTIMETABLEEVENTID);
        if (!$event = @IPS_GetEvent($weeklyTimeTableEventId)) {
            trigger_error(sprintf('Instance %s: wrong Event ID #%s', $this->InstanceID, $weeklyTimeTableEventId));
            return false;
        }
        $this->SendDebug(__FUNCTION__, sprintf('event: %s', json_encode($event, JSON_THROW_ON_ERROR)), 0);
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

    /**
     * Retrieves profile information associated with a given property name.
     *
     * @param string $propName The property name for which the profile information is to be fetched.
     *
     * @return array|null Returns an associative array containing profile information if the property exists and has a valid profile; otherwise,
     *                    returns null.
     */
    private function GetProfileInformation_org(string $propName): ?array
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

        $reversed = strcasecmp('reversed', end($profileNameParts)) === 0; //Groß-/Kleinschreibung wird ignoriert
        switch ($propName) {
            case self::PROP_BLINDLEVELID:
            case self::PROP_SLATSLEVELID:
                return [
                    'Name'        => $profileName,
                    'ProfileType' => $profile['ProfileType'],
                    'MinValue'    => $profile['MinValue'],
                    'MaxValue'    => $profile['MaxValue'],
                    'Reversed'    => $reversed
                ];
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
                    'Reversed'    => $reversed
                ];
            default:
                trigger_error('Unknown propName: ' . $propName);
        }

        return null;
    }

    private function GetPresentationInformation(string $propName): ?array
    {
        if (!($presentation = @IPS_GetVariablePresentation($this->ReadPropertyInteger($propName)))) {
            return null;
        }

        switch ($presentation['PRESENTATION']) {
            case VARIABLE_PRESENTATION_LEGACY:
                return $this->GetProfileInformationFromPresentation($presentation);

            case VARIABLE_PRESENTATION_SWITCH;
                return [
                    'MinValue' => 0,
                    'MaxValue' => 1
                ];

            case VARIABLE_PRESENTATION_SHUTTER:
                return [
                    'MinValue' => $presentation['OPEN_OUTSIDE_VALUE'],
                    'MaxValue' => $presentation['CLOSE_INSIDE_VALUE']
                ];

            case VARIABLE_PRESENTATION_SLIDER:
                return [
                    'MinValue' => $presentation['MIN'],
                    'MaxValue' => $presentation['MAX']
                ];

            case VARIABLE_PRESENTATION_ENUMERATION:
                // "Aufzählung": OPTIONS ist ein JSON-String mit Value/Caption-Paaren.
                // Die Aufzählung trägt keine Richtungsinformation, daher wird sie wie ein
                // Legacy-Profil ohne ".Reversed" behandelt: kleinster Wert = geöffnet, größter = geschlossen.
                // Für reversierte Rollläden (z. B. Homematic) ist die Darstellung "Rolladen" zu verwenden.
                $options = json_decode($presentation['OPTIONS'] ?? '[]', true);
                $values  = is_array($options) ? array_column($options, 'Value') : [];
                if (empty($values)) {
                    return null;
                }
                return [
                    'MinValue' => min($values),
                    'MaxValue' => max($values)
                ];

            default:
                //assert(false, sprintf('unsupported presentation: %s with "%s"', $presentation['PRESENTATION'], $propName));
                trigger_error(sprintf('unsupported presentation: %s with "%s"', $presentation['PRESENTATION'], $propName));
                return null;
        }
    }


    private function isMinMaxReversed(int|float $min, int|float $max): bool
    {
        return $min > $max;
    }

    private function GetProfileInformationFromPresentation(array $presentation): ?array
    {
        $profileName = $presentation['PROFILE'];
        if ($profileName === '') {
            return null;
        }

        if ($profile = @IPS_GetVariableProfile($profileName)) {
            $profileNameParts = explode('.', $profileName);
        } else {
            return null;
        }

        $reversed = strcasecmp('reversed', end($profileNameParts)) === 0; //Groß-/Kleinschreibung wird ignoriert

        return [
            'MinValue' => $reversed ? $profile['MaxValue'] : $profile['MinValue'],
            'MaxValue' => $reversed ? $profile['MinValue'] : $profile['MaxValue'],
        ];
    }

    private function MyUpdateFormField(array $form, string $name, string $parameter, $value): array
    {
        foreach ($form as $key => &$item) {
            if ($key === 'elements' || $key === 'actions') {
                $item = $this->MyUpdateFormField($item, $name, $parameter, $value);
            } elseif (isset($item['items'])) {
                $item['items'] = $this->MyUpdateFormField($item['items'], $name, $parameter, $value);
            } elseif (isset($item['type']) && in_array($item['type'], ['Select', 'NumberSpinner', 'SelectVariable'])) {
                if ($item['name'] === $name) {
                    $item[$parameter] = $value;
                    return $form;
                }
            }
        }
        return $form;
    }

    //-----------------------------------------------
    private function GetBlindLastTimeStamp(int $idBlindLevel, int $idSlatsLevel): int
    {
        $tsBlindChanged = IPS_GetVariable($idBlindLevel)['VariableChanged'];

        if (IPS_VariableExists($idSlatsLevel)) {
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
            $ret = (string)$val;
        } elseif (is_array($val)) {
            $ret = json_encode($val, JSON_THROW_ON_ERROR);
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

        if (!$this->dryRun) {
            $this->SetValue(self::VAR_IDENT_LAST_MESSAGE, $message);
        }
    }

    private function Logger_Inf(string $message): void
    {
        $this->SendDebug('LOG_INFO', $message, 0);
        if (function_exists('IPSLogger_Inf') && $this->ReadPropertyBoolean('WriteLogInformationToIPSLogger')) {
            IPSLogger_Inf(__CLASS__, $message);
        } else {
            $this->LogMessage($message, KL_NOTIFY);
        }

        if (!$this->dryRun) {
            $this->SetValue(self::VAR_IDENT_LAST_MESSAGE, $message);
        }
    }

    private function Logger_Dbg(string $message, string $data): void
    {
        $this->SendDebug($message, $data, 0);
        if (function_exists('IPSLogger_Dbg') && $this->ReadPropertyBoolean('WriteDebugInformationToIPSLogger')) {
            IPSLogger_Dbg(__CLASS__ . '.' . $this->objectName . '.' . $message, $data);
        }
        if ($this->ReadPropertyBoolean('WriteDebugInformationToLogfile')) {
            $this->LogMessage(sprintf('%s: %s', $message, $data), KL_DEBUG);
        }
    }
}
