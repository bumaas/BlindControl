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


    //variable names
    private const string VAR_IDENT_LAST_MESSAGE = 'LAST_MESSAGE';
    private const string VAR_IDENT_ACTIVATED    = 'ACTIVATED';

    private const int MOVEMENT_WAIT_TIME         = 90; //Wartezeit bis zur Erreichung der Zielposition in Sekunden
    private const int IGNORE_MOVEMENT_TIME       = 40; //Nach einer Bewegung wird eine erneute gleiche Bewegung innerhalb dieser Zeit ignoriert
    private const int ALLOWED_TOLERANCE_MOVEMENT = 1; //erlaubte Abweichung bei Bewegungen in Prozent

    private string $objectName;

    private ?array $profileBlindLevel;

    private ?array $profileSlatsLevel;


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

        // --- 1. Tageszeit bestimmen ---
        $dayState = $this->determineDayState($positionsAct['BlindLevel']);
        $isDay    = $dayState['isDay'];

        //Zeitpunkt der letzten Rollladenbewegung (Höhe oder Lamellen)
        $tsBlindLastMovement = $this->GetBlindLastTimeStamp($blindLevelId, $slatsLevelId);

        // Attribut TimestampAutomatik auslesen
        $tsAutomatik = $this->ReadAttributeInteger(self::ATTR_TIMESTAMP_AUTOMATIC);

        // --- 2. Prüfen auf Tageswechsel oder manuelle Bewegungssperre ---
        if ($this->checkIsDayChange($isDay)) {
            //beim Tageswechsel ...
            $deactivationTimeAuto = 0;
            $this->WriteAttributeString(
                self::ATTR_MANUALMOVEMENT,
                json_encode(['timeStamp' => null, 'blindLevel' => null, 'slatsLevel' => null], JSON_THROW_ON_ERROR)
            );
            $bNoMove = false;
        } else {
            // während der Verzögerung ist die ursprüngliche Tageszeit anzunehmen
            $isDay = $this->ReadAttributeBoolean('AttrIsDay');

            $bNoMove = $this->shouldBlockMovement(
                $positionsAct['BlindLevel'],
                $positionsAct['SlatsLevel'],
                $tsBlindLastMovement,
                $isDay,
                $this->ReadAttributeInteger('AttrTimeStampIsDayChange'),
                $tsAutomatik
            );
        }

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

        // --- 4. Beschattungslogik anwenden (nur tagsüber und wenn keine Sperre) ---
        if ($isDay && !$bNoMove) {
            $shadowResult = $this->applyShadowingLogic($positionsAct['BlindLevel'], $positionsNew, $Hinweis);
            $positionsNew = $shadowResult['positions'];
            $Hinweis      = $shadowResult['hint'];
        } else {
            // nachts gilt keine deactivation Time
            $deactivationTimeAuto = 0;
        }

        // --- 5. Kontakte (Fenster/Notfall) prüfen und Positionen ggf. überschreiben ---
        $contactResult = $this->applyContactLogic($positionsAct, $positionsNew, $deactivationTimeAuto, $bNoMove, $Hinweis);

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

        //im Notfall wird die Automatik deaktiviert
        if ($bEmergency) {
            $this->SetValue(self::VAR_IDENT_ACTIVATED, false);
            $this->SetInstanceStatusAndTimerEvent();
        }

        IPS_SemaphoreLeave($this->InstanceID . '- Blind');

        return true;
    }

    private function determineDayState(float $currentBlindLevel): array
    {
        $isDayByTimeSchedule = $this->getIsDayByTimeSchedule();
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
            'brightness'          => $brightness
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

        if ($this->ReadAttributeInteger(self::ATTR_DAYTIME_CHANGE_TIME) === 0) {
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
            $hint .= ', ' . $this->GetFormattedValue($this->ReadPropertyInteger(self::PROP_BRIGHTNESSID));
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
        // 1. Beschattung nach Sonnenstand
        $positionsShadowingBySun = $this->getPositionsOfShadowingBySunPosition($currentBlindLevel);
        if ($positionsShadowingBySun !== null) {
            $positionsNew = $this->mergePositions($positionsNew, $positionsShadowingBySun);

            if ($positionsNew['BlindLevel'] === $positionsShadowingBySun['BlindLevel']) {
                $Hinweis =
                    IPS_VariableExists($this->ReadPropertyInteger(self::PROP_BRIGHTNESSIDSHADOWINGBYSUNPOSITION)) ? 'Beschattung nach Sonnenstand, '
                                                                                                                    . $this->GetFormattedValue(
                            $this->ReadPropertyInteger(self::PROP_BRIGHTNESSIDSHADOWINGBYSUNPOSITION)
                        ) : 'Beschattung nach Sonnenstand';
            }
        }

        // 2. Beschattung nach Helligkeit
        $positionsShadowingBrightness = $this->getPositionsOfShadowingByBrightness($currentBlindLevel);
        if ($positionsShadowingBrightness !== null) {
            $positionsNew = $this->mergePositions($positionsNew, $positionsShadowingBrightness);

            if ($positionsNew['BlindLevel'] === $positionsShadowingBrightness['BlindLevel']) {
                $Hinweis = 'Beschattung nach Helligkeit, ' . $this->GetFormattedValue(
                        $this->ReadPropertyInteger(self::PROP_BRIGHTNESSIDSHADOWINGBRIGHTNESS)
                    );
            }
        }

        return ['positions' => $positionsNew, 'hint' => $Hinweis];
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
     * @return array{positions: array, deactivationTimeAuto: int, bNoMove: bool, hint: string, bEmergency: bool}
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

        // 1. Notfall hat höchste Priorität
        if ($levelContactEmergency !== null) {
            $this->WriteAttributeBoolean(self::ATTR_CONTACT_OPEN, true);
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
                'bEmergency'           => true
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
        if ($positionsContactOpenBlind !== null) {
            $checkResult = $this->checkContactLimit($positionsAct, $positionsNew, $positionsContactOpenBlind, true);
            if ($checkResult['modified']) {
                $bNoMove      = false;
                $positionsNew = $checkResult['positions'];
                $Hinweis      = 'Kontakt offen';
                if ($checkResult['resetDeactivation']) {
                    $deactivationTimeAuto = 0;
                }
                $this->WriteAttributeBoolean(self::ATTR_CONTACT_OPEN, true);
                $this->Logger_Dbg(__FUNCTION__, 'Kontakt geöffnet (Open-Logik angewendet)');
            }
        } elseif ($positionsContactCloseBlind !== null) {
            $checkResult = $this->checkContactLimit($positionsAct, $positionsNew, $positionsContactCloseBlind, false);
            if ($checkResult['modified']) {
                $bNoMove              = false;
                $positionsNew         = $checkResult['positions'];
                $Hinweis              = 'Kontakt offen';
                $deactivationTimeAuto = 0;
                $this->WriteAttributeBoolean(self::ATTR_CONTACT_OPEN, true);
                $this->Logger_Dbg(__FUNCTION__, 'Kontakt geöffnet (Close-Logik angewendet)');
            }
        } elseif ($this->ReadAttributeBoolean(self::ATTR_CONTACT_OPEN)) {
            // Reset, wenn kein Kontakt mehr aktiv ist
            $deactivationTimeAuto = 0;
            $this->WriteAttributeBoolean(self::ATTR_CONTACT_OPEN, false);
        }

        $result = [
            'positions'            => $positionsNew,
            'deactivationTimeAuto' => $deactivationTimeAuto,
            'bNoMove'              => $bNoMove,
            'hint'                 => $Hinweis,
            'bEmergency'           => false
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

        // Diese Zeile deckt beide Richtungen (reversed j/n) korrekt ab.
        return (int)round((($position - $min) / $range) * 100);
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

        //contacts open
        $this->RegisterPropertyInteger(self::PROP_CONTACTOPEN1ID, 0);
        $this->RegisterPropertyInteger(self::PROP_CONTACTOPEN2ID, 0);
        $this->RegisterPropertyFloat(self::PROP_CONTACTOPENLEVEL1, 0);
        $this->RegisterPropertyFloat(self::PROP_CONTACTOPENLEVEL2, 0);
        $this->RegisterPropertyFloat(self::PROP_CONTACTOPENSLATSLEVEL1, 0);
        $this->RegisterPropertyFloat(self::PROP_CONTACTOPENSLATSLEVEL2, 0);

        //emergency contact
        $this->RegisterPropertyInteger(self::PROP_EMERGENCYCONTACTID, 0);


        $this->RegisterPropertyInteger(self::PROP_UPDATEINTERVAL, 1);
        $this->RegisterPropertyInteger(self::PROP_DEACTIVATIONAUTOMATICMOVEMENT, 20);
        $this->RegisterPropertyInteger(self::PROP_DEACTIVATIONMANUALMOVEMENT, 120);
        $this->RegisterPropertyFloat(self::PROP_MINMOVEMENT, 5.0);
        $this->RegisterPropertyFloat(self::PROP_MINMOVEMENTATENDPOSITION, 2.5);
        $this->RegisterPropertyBoolean(self::PROP_SHOWNOTUSEDELEMENTS, false);
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
            [VARIABLETYPE_BOOLEAN, VARIABLETYPE_INTEGER, VARIABLETYPE_FLOAT],
            false,
            self::STATUS_INST_CONTACT1_ID_IS_INVALID
        )) {
            $this->SetStatus($ret);
            return;
        }

        if ($ret = $this->checkVariableId(
            self::PROP_CONTACTOPEN2ID,
            true,
            [VARIABLETYPE_BOOLEAN, VARIABLETYPE_INTEGER, VARIABLETYPE_FLOAT],
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

        $this->WriteAttributeInteger(self::ATTR_DAYTIME_CHANGE_TIME, $daytimeChangeTime);

        $this->SetTimerInterval(self::TIMER_DELAYED_MOVEMENT, $interval * 1000);
    }

    private function deactivateDelayTimer(): void
    {
        $this->Logger_Dbg(__FUNCTION__, 'Delay Timer deactivated');

        $this->WriteAttributeInteger(self::ATTR_DAYTIME_CHANGE_TIME, 0);

        $this->SetTimerInterval(self::TIMER_DELAYED_MOVEMENT, 0);
    }

    private function checkIsDayChange(bool $isDay): bool
    {
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
                    return false;
                }

                if (time() < $attrDaytimeChangeTime) {
                    return false;
                }

                $this->deactivateDelayTimer(); //Timer wieder ausschalten

            }
            $this->WriteAttributeBoolean('AttrIsDay', $isDay);
            $this->WriteAttributeInteger('AttrTimeStampIsDayChange', time());
            $this->Logger_Dbg(__FUNCTION__, 'DayChange!');
            return true;
        }

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


    private function getBlindPositions(array $contacts): ?array
    {
        $contactOpen    = null;
        $blindPositions = null;

        foreach ($contacts as $propName => $contact) {
            if ($this->isContactOpen($propName)) {
                $contactOpen = true;

                if (isset($blindPositions)) {
                    $blindPositions['BlindLevel'] =
                        $this->isMinMaxReversed($this->profileBlindLevel['MinValue'], $this->profileBlindLevel['MaxValue']) ? max(
                            $blindPositions['BlindLevel'],
                            $contact['blindlevel']
                        ) : min($blindPositions['BlindLevel'], $contact['blindlevel']);

                    if (isset($this->profileSlatsLevel)) {
                        $blindPositions['SlatsLevel'] =
                            $this->isMinMaxReversed($this->profileSlatsLevel['MinValue'], $this->profileSlatsLevel['MaxValue']) ? max(
                                $blindPositions['SlatsLevel'],
                                $contact['slatslevel']
                            ) : min($blindPositions['SlatsLevel'], $contact['slatslevel']);
                    }
                } else {
                    $blindPositions['BlindLevel'] = $contact['blindlevel'];
                    $blindPositions['SlatsLevel'] = $contact['slatslevel'];
                }

                $this->Logger_Dbg(
                    __FUNCTION__,
                    sprintf(
                        'contact is open: #%s, value: %s, blindlevel: %s, slatslevel: %s',
                        $contact['id'],
                        $this->GetFormattedValue($contact['id']),
                        $contact['blindlevel'],
                        $contact['slatslevel']
                    )
                );
            }
        }

        return $contactOpen ? $blindPositions : null;
    }

    private function getPositionsOfOpenBlindContact(): ?array
    {
        $contacts = $this->getDefinedContacts('PROP_CONTACTOPEN', 'PROP_CONTACTOPENLEVEL', 'PROP_CONTACTOPENSLATSLEVEL');
        return $this->getBlindPositions($contacts);
    }

    private function getPositionsOfCloseBlindContact(): ?array
    {
        $contacts = $this->getDefinedContacts('PROP_CONTACTCLOSE', 'PROP_CONTACTCLOSELEVEL', 'PROP_CONTACTCLOSESLATSLEVEL');
        return $this->getBlindPositions($contacts);
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

        $this->Logger_Dbg(
            __FUNCTION__,
            sprintf('%s (#%s): value: %s, reversed: %s', $propName, $contactId, (int)GetValue($contactId), (int)$reversed)
        );

        if ($reversed) {
            return !GetValue($contactId);
        }

        return (bool)GetValue($contactId);
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

        if (!IPS_VariableExists($activatorID) || !GetValue($activatorID)) {
            // keine Beschattung nach Sonnenstand gewünscht bzw. nicht notwendig
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

        $positions = null;
        if (($brightness >= $thresholdBrightness) && ($rSunAzimuth >= $azimuthFrom) && ($rSunAzimuth <= $azimuthTo)
            && ($rSunAltitude >= $altitudeFrom)
            && ($rSunAltitude <= $altitudeTo)) {
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
            if ($this->isMinMaxReversed($this->profileBlindLevel['MinValue'], $this->profileBlindLevel['MaxValue'])) {
                $levelPositionHeat = round(0.10 * ($this->profileBlindLevel['MinValue'] - $this->profileBlindLevel['MaxValue']), 2);
            } else {
                $levelPositionHeat = round(0.90 * ($this->profileBlindLevel['MaxValue'] - $this->profileBlindLevel['MinValue']), 2);
            }

            if (($temperature > 30.0) || ((round($levelAct, 1) === round($levelPositionHeat, 1)) && ($temperature > (30.0 - 0.5)))) {
                $positions['BlindLevel'] = $levelPositionHeat;
                $this->Logger_Dbg(__FUNCTION__, sprintf('Temp gt 30°, levelAct: %.2f, level: %.2f', $levelAct, $positions['BlindLevel']));
                return $positions;
            }

            //wenn zusätzlicher *Wärmeschutz* notwendig oder bereits eingeschaltet und Hysterese nicht unterschritten
            if ($this->isMinMaxReversed($this->profileBlindLevel['MinValue'], $this->profileBlindLevel['MaxValue'])) {
                $levelCorrectionHeat = -round(0.15 * ($this->profileBlindLevel['MinValue'] - $this->profileBlindLevel['MaxValue']), 2);
            } else {
                $levelCorrectionHeat = round(0.15 * ($this->profileBlindLevel['MaxValue'] - $this->profileBlindLevel['MinValue']), 2);
            }

            if (($temperature > 27.0)
                || ((round($levelAct, 1) === round($positions['BlindLevel'] + $levelCorrectionHeat, 1))
                    && ($temperature > (27.0 - 0.5)))) {
                $positions['BlindLevel'] += $levelCorrectionHeat;
                $this->Logger_Dbg(
                    __FUNCTION__,
                    sprintf(
                        'Temp gt 27°, levelAct: %.2f, level: %.2f, levelCorrectionHeat: %.2f',
                        $levelAct,
                        $positions['BlindLevel'],
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

        if (!IPS_VariableExists($activatorID) || !GetValue($activatorID)) {
            // keine Beschattung bei Helligkeit gewünscht bzw. nicht notwendig
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
    private function shouldBlockMovement(
        float $blindLevelAct,
        ?float $slatsLevelAct,
        int $tsBlindLastMovement,
        bool $isDay,
        int $tsIsDayChanged,
        int $tsAutomatik
    ): bool {
        // 1. Karenzzeit nach automatischer Bewegung prüfen
        if ($tsBlindLastMovement <= strtotime('+5 sec', $tsAutomatik)) {
            return false;
        }

        // 2. Manuellen Status synchronisieren (Logik für ATTR_MANUALMOVEMENT extrahiert)
        $this->syncManualMovementAttribute($blindLevelAct, $slatsLevelAct, $tsBlindLastMovement);

        $manualState = json_decode($this->ReadAttributeString(self::ATTR_MANUALMOVEMENT), true, 512, JSON_THROW_ON_ERROR);
        $tsManual = $manualState['timeStamp'];

        if ($tsManual === null || $tsManual <= $tsIsDayChanged) {
            return false;
        }

        // 3. Sperr-Logik
        if (!$isDay) {
            $this->Logger_Dbg(__FUNCTION__, sprintf('Sperre: Manuelle Bewegung in der Nacht (%s)', date('H:i:s', $tsManual)));
            return true;
        }

        // Tagsüber: Sperre, nur wenn geschlossen oder innerhalb der Deaktivierungszeit
        $deactivationTimeManuSecs = $this->ReadPropertyInteger(self::PROP_DEACTIVATIONMANUALMOVEMENT) * 60;

        $isClosed = ($blindLevelAct === $this->profileBlindLevel['MaxValue']) &&
                    ($slatsLevelAct === ($this->profileSlatsLevel['MaxValue'] ?? null));

        if ($isClosed || ($deactivationTimeManuSecs === 0) || (strtotime("+ $deactivationTimeManuSecs seconds", $tsManual) > time())) {
            $this->Logger_Dbg(__FUNCTION__, 'Sperre: Manuelle Bewegung am Tag aktiv.');
            return true;
        }

        // Zeit abgelaufen -> Automatik wieder freigeben
        $this->resetManualMovement();
        return false;
    }


    /**
     * Synchronisiert das Attribut für manuelle Bewegungen basierend auf aktuellen Aktorwerten.
     *
     * @param float      $blindLevelAct       Aktuelle Behanghöhe des Aktors.
     * @param float|null $slatsLevelAct       Aktuelle Lamellenposition des Aktors (null falls nicht vorhanden).
     * @param int        $tsBlindLastMovement Zeitstempel der letzten physischen Änderung am Aktor.
     *
     * @throws \JsonException Bei Fehlern während der JSON-Kodierung/Dekodierung.
     */
    private function syncManualMovementAttribute(float $blindLevelAct, ?float $slatsLevelAct, int $tsBlindLastMovement): void
    {
        $currentManual = json_decode($this->ReadAttributeString(self::ATTR_MANUALMOVEMENT), true, 512, JSON_THROW_ON_ERROR);

        // Nur fortfahren, wenn ein neuer manueller Zeitstempel erkannt wurde
        if ($tsBlindLastMovement === $currentManual['timeStamp']) {
            return;
        }

        // Neuen Zustand speichern
        $this->WriteAttributeString(
            self::ATTR_MANUALMOVEMENT,
            json_encode(['timeStamp' => $tsBlindLastMovement, 'blindLevel' => $blindLevelAct, 'slatsLevel' => $slatsLevelAct], JSON_THROW_ON_ERROR)
        );

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

        // Informationstext generieren und loggen
        $this->logManualMovementInfo($blindLevelAct, $slatsLevelAct);
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
            $this->WriteAttributeInteger(self::ATTR_TIMESTAMP_AUTOMATIC, time());
            return false;
        }

        // 2. Zielwert berechnen
        $positionNew = $this->calculateValueFromPercent($percentClose, $profile);
        $positionAct = (float)GetValue($positionID);

        // 3. Bewegungs-Validierung (Early Returns für bessere Lesbarkeit)
        if (!$this->shouldPerformMovement($propName, $positionID, $positionAct, $positionNew, $profile, $tsAutomatic, $deactivationTimeAuto)) {
            return false;
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
            return false;
        }

        // 2. Toleranzbereich (bereits erreicht)?
        if ($diffPercentage <= (self::ALLOWED_TOLERANCE_MOVEMENT / 100)) {
            $this->Logger_Dbg(__FUNCTION__, "#$id($propName): Position $act bereits im Toleranzbereich.");
            return false;
        }

        // 3. Zu kleine Bewegung (außer es ist eine Endposition)
        $isEndPosition = in_array($new, [$profile['MinValue'], $profile['MaxValue']], false);
        if (!$isEndPosition && ($diffPercentage < $minMove)) {
            $this->Logger_Dbg(__FUNCTION__, sprintf("#$id($propName): Bewegung zu klein (%.2f%% < %.2f%%).", $diffPercentage * 100, $minMove * 100));
            return false;
        }

        // 4. Zu kleine Bewegung zur Endposition
        if ($diffPercentage < $minMoveEnd) {
            $this->Logger_Dbg(__FUNCTION__, sprintf("#$id($propName): Endposition fast erreicht (Differenz %.2f%%).", $diffPercentage * 100));
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

    private function getIsDayByTimeSchedule(): ?bool
    {
        //Ermitteln, welche Zeiten heute und gestern gelten

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
            IPSLogger_Dbg(__CLASS__ . '.' . $this->objectName . '.' . $message, $data);
        }
        if ($this->ReadPropertyBoolean('WriteDebugInformationToLogfile')) {
            $this->LogMessage(sprintf('%s: %s', $message, $data), KL_DEBUG);
        }
    }
}
