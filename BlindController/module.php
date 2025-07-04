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

    //shadowing according to sun position
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


    //shadowing according to brightness
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

    private $profileBlindLevel;

    private $profileSlatsLevel;


    // die folgenden Funktionen überschreiben die interne IPS_() Funktionen
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

    public function RequestAction($Ident, $Value): void
    {
        if (is_bool($Value)) {
            $this->Logger_Dbg(__FUNCTION__, sprintf('Ident: %s, Value: %s', $Ident, (int)$Value));
        } else {
            $this->Logger_Dbg(__FUNCTION__, sprintf('Ident: %s, Value: %s', $Ident, $Value));
        }

        switch ($Ident) {
            case self::VAR_IDENT_ACTIVATED:
                if ($Value) {
                    $this->resetManualMovement();
                } else {
                    $this->Logger_Inf(sprintf('\'%s\' wurde deaktiviert.', IPS_GetObject($this->InstanceID)['ObjectName']));
                }

                $this->SetValue($Ident, $Value);
                $this->SetInstanceStatusAndTimerEvent();
                IPS_RunScriptText(sprintf('BLC_ControlBlind(%s, %s);', $this->InstanceID, 'false'));
                //todo: kann später mal durch RegisterOnceTimer() ersetzt werden. Setzt aber 5.5 voraus.
                break;

            case self::PROP_SLATSLEVELID:
                $this->UpdateFormField(
                    self::PROP_NIGHTSLATSLEVEL,
                    'visible',
                    (($Value > 0) && $this->ReadPropertyBoolean(self::PROP_ACTIVATEDINDIVIDUALNIGHTLEVELS))
                    || $this->ReadPropertyBoolean(
                        self::PROP_SHOWNOTUSEDELEMENTS
                    )
                );
                $this->UpdateFormField(
                    self::PROP_DAYSLATSLEVEL,
                    'visible',
                    (($Value > 0) && $this->ReadPropertyBoolean(self::PROP_ACTIVATEDINDIVIDUALDAYLEVELS))
                    || $this->ReadPropertyBoolean(
                        self::PROP_SHOWNOTUSEDELEMENTS
                    )
                );
                $this->UpdateFormField(
                    self::PROP_LOWSUNPOSITIONSLATSLEVEL,
                    'visible',
                    ($Value > 0) || $this->ReadPropertyBoolean(self::PROP_SHOWNOTUSEDELEMENTS)
                );
                $this->UpdateFormField(
                    self::PROP_HIGHSUNPOSITIONSLATSLEVEL,
                    'visible',
                    ($Value > 0) || $this->ReadPropertyBoolean(self::PROP_SHOWNOTUSEDELEMENTS)
                );
                $this->UpdateFormField(
                    self::PROP_MINIMUMSHADERELEVANTSLATSLEVEL,
                    'visible',
                    ($Value > 0) || $this->ReadPropertyBoolean(self::PROP_SHOWNOTUSEDELEMENTS)
                );
                $this->UpdateFormField(
                    self::PROP_HIGHSUNPOSITIONSLATSLEVEL,
                    'visible',
                    ($Value > 0) || $this->ReadPropertyBoolean(self::PROP_SHOWNOTUSEDELEMENTS)
                );
                $this->UpdateFormField(
                    self::PROP_SLATSLEVELLESSBRIGHTNESSSHADOWINGBRIGHTNESS,
                    'visible',
                    ($Value > 0) || $this->ReadPropertyBoolean(self::PROP_SHOWNOTUSEDELEMENTS)
                );
                $this->UpdateFormField(
                    self::PROP_SLATSLEVELHIGHBRIGHTNESSSHADOWINGBRIGHTNESS,
                    'visible',
                    ($Value > 0) || $this->ReadPropertyBoolean(self::PROP_SHOWNOTUSEDELEMENTS)
                );
                $this->UpdateFormField(
                    self::PROP_CONTACTCLOSESLATSLEVEL1,
                    'visible',
                    ($Value > 0) || $this->ReadPropertyBoolean(self::PROP_SHOWNOTUSEDELEMENTS)
                );
                $this->UpdateFormField(
                    self::PROP_CONTACTCLOSESLATSLEVEL2,
                    'visible',
                    ($Value > 0) || $this->ReadPropertyBoolean(self::PROP_SHOWNOTUSEDELEMENTS)
                );
                $this->UpdateFormField(
                    self::PROP_CONTACTOPENSLATSLEVEL1,
                    'visible',
                    ($Value > 0) || $this->ReadPropertyBoolean(self::PROP_SHOWNOTUSEDELEMENTS)
                );
                $this->UpdateFormField(
                    self::PROP_CONTACTOPENSLATSLEVEL2,
                    'visible',
                    ($Value > 0) || $this->ReadPropertyBoolean(self::PROP_SHOWNOTUSEDELEMENTS)
                );
                break;

            case self::PROP_HOLIDAYINDICATORID:
                $this->UpdateFormField(
                    self::PROP_DAYUSEDWHENHOLIDAY,
                    'visible',
                    ($Value > 0) || $this->ReadPropertyBoolean(self::PROP_SHOWNOTUSEDELEMENTS)
                );
                break;

            case self::PROP_WAKEUPTIMEID:
                $this->UpdateFormField(
                    self::PROP_WAKEUPTIMEOFFSET,
                    'visible',
                    ($Value > 0) || $this->ReadPropertyBoolean(self::PROP_SHOWNOTUSEDELEMENTS)
                );
                break;

            case self::PROP_BEDTIMEID:
                $this->UpdateFormField(
                    self::PROP_BEDTIMEOFFSET,
                    'visible',
                    ($Value > 0) || $this->ReadPropertyBoolean(self::PROP_SHOWNOTUSEDELEMENTS)
                );
                break;

            case self::PROP_ACTIVATEDINDIVIDUALDAYLEVELS:
                $this->UpdateFormField(self::PROP_DAYBLINDLEVEL, 'visible', $Value || $this->ReadPropertyBoolean(self::PROP_SHOWNOTUSEDELEMENTS));
                $this->UpdateFormField(
                    self::PROP_DAYSLATSLEVEL,
                    'visible',
                    (IPS_VariableExists($this->ReadPropertyInteger(self::PROP_SLATSLEVELID)) && $Value)
                    || $this->ReadPropertyBoolean(
                        self::PROP_SHOWNOTUSEDELEMENTS
                    )
                );
                break;

            case self::PROP_ACTIVATEDINDIVIDUALNIGHTLEVELS:
                $this->UpdateFormField(self::PROP_NIGHTBLINDLEVEL, 'visible', $Value || $this->ReadPropertyBoolean(self::PROP_SHOWNOTUSEDELEMENTS));
                $this->UpdateFormField(
                    self::PROP_NIGHTSLATSLEVEL,
                    'visible',
                    (IPS_VariableExists($this->ReadPropertyInteger(self::PROP_SLATSLEVELID)) && $Value)
                    || $this->ReadPropertyBoolean(
                        self::PROP_SHOWNOTUSEDELEMENTS
                    )
                );
                break;

            case self::PROP_BRIGHTNESSID:
                $this->UpdateFormField(
                    self::PROP_BRIGHTNESSAVGMINUTES,
                    'visible',
                    $Value || $this->ReadPropertyBoolean(self::PROP_SHOWNOTUSEDELEMENTS)
                );
                $this->UpdateFormField(
                    self::PROP_BRIGHTNESSTHRESHOLDID,
                    'visible',
                    $Value || $this->ReadPropertyBoolean(self::PROP_SHOWNOTUSEDELEMENTS)
                );
                break;

            case self::PROP_BRIGHTNESSIDSHADOWINGBYSUNPOSITION:
                $this->UpdateFormField(
                    self::PROP_BRIGHTNESSAVGMINUTESSHADOWINGBYSUNPOSITION,
                    'visible',
                    $Value || $this->ReadPropertyBoolean(self::PROP_SHOWNOTUSEDELEMENTS)
                );
                $this->UpdateFormField(
                    self::PROP_BRIGHTNESSTHRESHOLDIDSHADOWINGBYSUNPOSITION,
                    'visible',
                    $Value || $this->ReadPropertyBoolean(self::PROP_SHOWNOTUSEDELEMENTS)
                );
                break;

            case self::PROP_BRIGHTNESSIDSHADOWINGBRIGHTNESS:
                $this->UpdateFormField(
                    self::PROP_BRIGHTNESSAVGMINUTESSHADOWINGBRIGHTNESS,
                    'visible',
                    $Value || $this->ReadPropertyBoolean(self::PROP_SHOWNOTUSEDELEMENTS)
                );
                break;

            case 'MoveBlindToShadowingPosition':
                $this->MoveBlindToShadowingPosition((int)$Value);
                break;

            default:
                trigger_error(sprintf('Instance %s: Unknown Ident %s', $this->InstanceID, $Ident));
        }
    }

    public function MessageSink(int $TimeStamp, int $SenderID, int $Message, array $Data): void
    {
        parent::MessageSink($TimeStamp, $SenderID, $Message, $Data);

        $this->Logger_Dbg(
            __FUNCTION__,
            sprintf(
                'ModuleVersion: %s, Timestamp: %s, SenderID: %s[%s], Message: %s, Data: %s',
                $this->getModuleVersion(),
                $TimeStamp,
                IPS_GetObject($SenderID)['ObjectName'],
                $SenderID,
                $Message,
                json_encode($Data, JSON_THROW_ON_ERROR)
            )
        );

        switch ($Message) {
            case IPS_KERNELMESSAGE:
                if ($Data[0] === KR_READY) {
                    $this->ApplyChanges();
                }
                break;

            case EM_UPDATE:
            case VM_UPDATE:
            case VM_CHANGEPROFILEACTION:

                if ($Data[1] === false) {
                    break;
                }

                $this->SetInstanceStatusAndTimerEvent();

                if ($this->GetValue(self::VAR_IDENT_ACTIVATED)) {
                    // controlBlind mit Prüfung, ob der Rollladen sofort bewegt werden soll
                    $considerDeactivationTimeAuto = !in_array(
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
                        //Skripte können nur gestartet werden, wenn der Kernel ready ist
                        IPS_RunScriptText(sprintf('BLC_ControlBlind(%s, %s);', $this->InstanceID, $considerDeactivationTimeAuto ? 'true' : 'false'));
                        //todo: kann später mal durch RegisterOnceTimer() ersetzt werden. Setzt aber 5.5 voraus.
                    }
                }

                break;
        }
    }

    public function GetConfigurationForm(): string
    {
        $form = json_decode(file_get_contents(__DIR__ . '/form.json'), true, 512, JSON_THROW_ON_ERROR);

        if (IPS_GetKernelVersion() < '5.20') {
            $elements[] = [
                'type'  => 'RowLayout',
                'items' => [
                    [
                        'type'    => 'Label',
                        'caption' => 'In this instance, all parameters for controlling a single blind are stored. The description of the individual parameters can be found in the documentation.'
                    ],
                    [
                        'type'    => 'Button',
                        'caption' => 'Show Documentation',
                        'onClick' => 'echo \'https://github.com/bumaas/BlindControl/blob/master/README.md\';',
                        'link'    => true
                    ]
                ]
            ];
        } else {
            $elements[] = [
                'type'    => 'Label',
                'caption' => 'In this instance, all parameters for controlling a single blind are stored.'
            ];
        }

        $form['elements'] = array_merge($elements, $form['elements']);

        $this->SetVisibilityOfNotUsedElements($form);

        $this->SendDebug('Form', json_encode($form, JSON_THROW_ON_ERROR), 0);
        return json_encode($form, JSON_THROW_ON_ERROR);
    }

    private function SetVisibilityOfNotUsedElements(array &$form): void
    {
        $bShow = $this->ReadPropertyBoolean(self::PROP_SHOWNOTUSEDELEMENTS);

        $form = $this->MyUpdateFormField(
            $form,
            self::PROP_DAYUSEDWHENHOLIDAY,
            'visible',
            IPS_VariableExists($this->ReadPropertyInteger(self::PROP_HOLIDAYINDICATORID)) || $bShow
        );
        $form = $this->MyUpdateFormField(
            $form,
            self::PROP_WAKEUPTIMEOFFSET,
            'visible',
            IPS_VariableExists($this->ReadPropertyInteger(self::PROP_WAKEUPTIMEID)) || $bShow
        );
        $form = $this->MyUpdateFormField(
            $form,
            self::PROP_BEDTIMEOFFSET,
            'visible',
            IPS_VariableExists($this->ReadPropertyInteger(self::PROP_BEDTIMEID)) || $bShow
        );
        $form = $this->MyUpdateFormField(
            $form,
            self::PROP_NIGHTBLINDLEVEL,
            'visible',
            $this->ReadPropertyBoolean(self::PROP_ACTIVATEDINDIVIDUALNIGHTLEVELS) || $bShow
        );
        $form = $this->MyUpdateFormField(
            $form,
            self::PROP_NIGHTSLATSLEVEL,
            'visible',
            (IPS_VariableExists($this->ReadPropertyInteger(self::PROP_SLATSLEVELID))
             && $this->ReadPropertyBoolean(
                    self::PROP_ACTIVATEDINDIVIDUALNIGHTLEVELS
                ))
            || $bShow
        );
        $form = $this->MyUpdateFormField(
            $form,
            self::PROP_DAYBLINDLEVEL,
            'visible',
            $this->ReadPropertyBoolean(self::PROP_ACTIVATEDINDIVIDUALDAYLEVELS) || $bShow
        );
        $form = $this->MyUpdateFormField(
            $form,
            self::PROP_DAYSLATSLEVEL,
            'visible',
            (IPS_VariableExists($this->ReadPropertyInteger(self::PROP_SLATSLEVELID))
             && $this->ReadPropertyBoolean(
                    self::PROP_ACTIVATEDINDIVIDUALDAYLEVELS
                ))
            || $bShow
        );
        $form = $this->MyUpdateFormField(
            $form,
            self::PROP_BRIGHTNESSAVGMINUTES,
            'visible',
            IPS_VariableExists($this->ReadPropertyInteger(self::PROP_BRIGHTNESSID)) || $bShow
        );
        $form = $this->MyUpdateFormField(
            $form,
            self::PROP_BRIGHTNESSTHRESHOLDID,
            'visible',
            IPS_VariableExists($this->ReadPropertyInteger(self::PROP_BRIGHTNESSID)) || $bShow
        );
        $form = $this->MyUpdateFormField(
            $form,
            self::PROP_BRIGHTNESSAVGMINUTESSHADOWINGBYSUNPOSITION,
            'visible',
            IPS_VariableExists($this->ReadPropertyInteger(self::PROP_BRIGHTNESSIDSHADOWINGBYSUNPOSITION)) || $bShow
        );
        $form = $this->MyUpdateFormField(
            $form,
            self::PROP_BRIGHTNESSTHRESHOLDIDSHADOWINGBYSUNPOSITION,
            'visible',
            IPS_VariableExists($this->ReadPropertyInteger(self::PROP_BRIGHTNESSIDSHADOWINGBYSUNPOSITION)) || $bShow
        );
        $form = $this->MyUpdateFormField(
            $form,
            self::PROP_LOWSUNPOSITIONSLATSLEVEL,
            'visible',
            IPS_VariableExists($this->ReadPropertyInteger(self::PROP_SLATSLEVELID)) || $bShow
        );
        $form = $this->MyUpdateFormField(
            $form,
            self::PROP_HIGHSUNPOSITIONSLATSLEVEL,
            'visible',
            IPS_VariableExists($this->ReadPropertyInteger(self::PROP_SLATSLEVELID)) || $bShow
        );
        $form = $this->MyUpdateFormField(
            $form,
            self::PROP_MINIMUMSHADERELEVANTSLATSLEVEL,
            'visible',
            IPS_VariableExists($this->ReadPropertyInteger(self::PROP_SLATSLEVELID)) || $bShow
        );
        $form = $this->MyUpdateFormField(
            $form,
            self::PROP_MAXIMUMSHADERELEVANTSLATSLEVEL,
            'visible',
            IPS_VariableExists($this->ReadPropertyInteger(self::PROP_SLATSLEVELID)) || $bShow
        );
        $form = $this->MyUpdateFormField(
            $form,
            self::PROP_BRIGHTNESSAVGMINUTESSHADOWINGBRIGHTNESS,
            'visible',
            IPS_VariableExists($this->ReadPropertyInteger(self::PROP_BRIGHTNESSIDSHADOWINGBRIGHTNESS)) || $bShow
        );
        $form = $this->MyUpdateFormField(
            $form,
            self::PROP_SLATSLEVELLESSBRIGHTNESSSHADOWINGBRIGHTNESS,
            'visible',
            IPS_VariableExists($this->ReadPropertyInteger(self::PROP_SLATSLEVELID)) || $bShow
        );
        $form = $this->MyUpdateFormField(
            $form,
            self::PROP_SLATSLEVELHIGHBRIGHTNESSSHADOWINGBRIGHTNESS,
            'visible',
            IPS_VariableExists($this->ReadPropertyInteger(self::PROP_SLATSLEVELID)) || $bShow
        );
        $form = $this->MyUpdateFormField(
            $form,
            self::PROP_CONTACTCLOSESLATSLEVEL1,
            'visible',
            IPS_VariableExists($this->ReadPropertyInteger(self::PROP_SLATSLEVELID)) || $bShow
        );
        $form = $this->MyUpdateFormField(
            $form,
            self::PROP_CONTACTCLOSESLATSLEVEL2,
            'visible',
            IPS_VariableExists($this->ReadPropertyInteger(self::PROP_SLATSLEVELID)) || $bShow
        );
        $form = $this->MyUpdateFormField(
            $form,
            self::PROP_CONTACTOPENSLATSLEVEL1,
            'visible',
            IPS_VariableExists($this->ReadPropertyInteger(self::PROP_SLATSLEVELID)) || $bShow
        );
        $form = $this->MyUpdateFormField(
            $form,
            self::PROP_CONTACTOPENSLATSLEVEL2,
            'visible',
            IPS_VariableExists($this->ReadPropertyInteger(self::PROP_SLATSLEVELID)) || $bShow
        );
        $form = $this->MyUpdateFormField(
            $form,
            'SlatsLevel',
            'visible',
            IPS_VariableExists($this->ReadPropertyInteger(self::PROP_SLATSLEVELID)) || $bShow
        );
        $form = $this->MyUpdateFormField(
            $form,
            'ShadowingPosition',
            'visible',
            ($this->ReadPropertyFloat(self::PROP_MINIMUMSHADERELEVANTBLINDLEVEL) > 0)
            || ($this->ReadPropertyFloat(
                    self::PROP_MAXIMUMSHADERELEVANTBLINDLEVEL
                ) > 0)
            || $bShow
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
        $this->profileBlindLevel = $this->GetProfileInformation(self::PROP_BLINDLEVELID);

        // $deactivationTimeAuto: Zeitraum, in dem das automatisch gesetzte Level
        // erhalten bleibt, bevor es überschrieben wird.

        if ($considerDeactivationTimeAuto) {
            $deactivationTimeAuto = $this->ReadPropertyInteger(self::PROP_DEACTIVATIONAUTOMATICMOVEMENT) * 60;
        } else {
            $deactivationTimeAuto = 0;
        }

        $Hinweis = '';

        //Blind Level ID ermitteln
        $blindLevelId = $this->ReadPropertyInteger(self::PROP_BLINDLEVELID);

        // Die aktuellen Positionen im Jalousieaktor auslesen
        $positionsAct['BlindLevel'] = (float)GetValue($blindLevelId);

        //Slats Level ID ermitteln
        $slatsLevelId = $this->ReadPropertyInteger(self::PROP_SLATSLEVELID);
        if (IPS_VariableExists($slatsLevelId)) {
            $this->profileSlatsLevel    = $this->GetProfileInformation(self::PROP_SLATSLEVELID);
            $positionsAct['SlatsLevel'] = (float)GetValue($slatsLevelId);
        } else {
            $this->profileSlatsLevel    = null;
            $positionsAct['SlatsLevel'] = null;
        }

        // -- 'tagsüber' nach Wochenplan ermitteln --
        $isDayByTimeSchedule = $this->getIsDayByTimeSchedule();

        // -- optionale Tageserkennung auswerten --
        $brightness          = null;
        $isDayByDayDetection = $this->getIsDayByDayDetection($brightness, $positionsAct['BlindLevel']);

        if ($isDayByDayDetection === null) {
            $isDay = $isDayByTimeSchedule;
        } else {
            $isDay = $isDayByTimeSchedule && $isDayByDayDetection;
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
            $this->WriteAttributeString(self::ATTR_MANUALMOVEMENT,
                                        json_encode(['timeStamp' => null, 'blindLevel' => null, 'slatsLevel' => null], JSON_THROW_ON_ERROR)
            );

            //wird keine Bewegungssperre gesetzt
            $bNoMove = false;
        } else {
            // während der Verzögerung ist die ursprüngliche Tageszeit anzunehmen
            $isDay = $this->ReadAttributeBoolean('AttrIsDay');

            // prüfen, ob der Rollladen manuell bewegt wurde und somit eine Bewegungssperre besteht
            $bNoMove = $this->isMovementLocked(
                $positionsAct['BlindLevel'],
                $positionsAct['SlatsLevel'],
                $tsBlindLastMovement,
                $isDay,
                $this->ReadAttributeInteger('AttrTimeStampIsDayChange'),
                $tsAutomatik,
                $this->profileBlindLevel['LevelClosed'],
                $this->profileBlindLevel['LevelOpened'],
                $this->profileSlatsLevel['LevelClosed'] ?? null,
                $this->profileSlatsLevel['LevelOpened'] ?? null
            );
        }

        $this->Logger_Dbg(
            __FUNCTION__,
            sprintf(
                'ModuleVersion: %s, tsAutomatik: %s, tsBlind: %s, posActBlindLevel: %.2f,  posActSlatsLevel: %s, bNoMove: %s, isDay: %s (isDayByTimeSchedule: %s, isDayByDayDetection: %s), considerDeactivationTimeAuto: %s',
                $this->getModuleVersion(),
                $this->FormatTimeStamp($tsAutomatik),
                $this->FormatTimeStamp($tsBlindLastMovement),
                $positionsAct['BlindLevel'],
                $positionsAct['SlatsLevel'] ?? 'null',
                (int)$bNoMove,
                (int)$isDay,
                (int)$isDayByTimeSchedule,
                (isset($isDayByDayDetection) ? (int)$isDayByDayDetection : 'null'),
                (int)$considerDeactivationTimeAuto
            )
        );

        if ($bNoMove) {
            $positionsNew = $positionsAct;
        } elseif ($isDay) {
            $lastManualMovement         = json_decode($this->ReadAttributeString(self::ATTR_MANUALMOVEMENT), true, 512, JSON_THROW_ON_ERROR);
            $deactivationManualMovement = $this->ReadPropertyInteger(self::PROP_DEACTIVATIONMANUALMOVEMENT);
            if (isset($lastManualMovement['timeStamp'])
                && (($deactivationManualMovement === 0)
                    || strtotime(
                           '+ ' . $deactivationManualMovement . ' minutes',
                           $lastManualMovement['timeStamp']
                       ) > time())) {
                $positionsNew['BlindLevel'] = $lastManualMovement['blindLevel'];
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
                $positionsNew['SlatsLevel'] = $this->profileSlatsLevel['LevelOpened'] ?? null;
            }

            if ($isDayByTimeSchedule !== $this->ReadAttributeBoolean(self::ATTR_LAST_ISDAYBYTIMESCHEDULE)) {
                $Hinweis = 'WP';
            } else {
                $Hinweis = 'Tag';
            }

            if ($this->ReadAttributeInteger(self::ATTR_DAYTIME_CHANGE_TIME) === 0) { //es läuft keine Verzögerung
                $this->WriteAttributeBoolean(self::ATTR_LAST_ISDAYBYTIMESCHEDULE, $isDayByTimeSchedule);
            }
        } else { //it is night
            if ($isDayByTimeSchedule !== $this->ReadAttributeBoolean(self::ATTR_LAST_ISDAYBYTIMESCHEDULE)) {
                $Hinweis = 'WP';
            } else {
                $Hinweis = 'Nacht';
            }

            if ($this->ReadAttributeInteger(self::ATTR_DAYTIME_CHANGE_TIME) === 0) { //es läuft keine Verzögerung
                $this->WriteAttributeBoolean(self::ATTR_LAST_ISDAYBYTIMESCHEDULE, $isDayByTimeSchedule);
            }

            if ($this->ReadPropertyBoolean(self::PROP_ACTIVATEDINDIVIDUALNIGHTLEVELS)) {
                $positionsNew['BlindLevel'] = $this->ReadPropertyFloat(self::PROP_NIGHTBLINDLEVEL);
                $positionsNew['SlatsLevel'] = $this->ReadPropertyFloat(self::PROP_NIGHTSLATSLEVEL);
                $Hinweis                    .= ', indiv.Pos.';
            } else {
                $positionsNew['BlindLevel'] = $this->profileBlindLevel['LevelClosed'];
                $positionsNew['SlatsLevel'] = $this->profileSlatsLevel['LevelClosed'] ?? null;
            }
        }

        if (isset($isDayByDayDetection, $brightness)) {
            $Hinweis .= ', ' . $this->GetFormattedValue($this->ReadPropertyInteger(self::PROP_BRIGHTNESSID));
        }

        // -- am Tag wird überprüft, ob das Fenster beschattet werden soll --
        if ($isDay && !$bNoMove) {
            // prüfen, ob Beschattung nach Sonnenstand gewünscht und notwendig
            $positionsShadowingBySunPosition = $this->getPositionsOfShadowingBySunPosition($positionsAct['BlindLevel']);
            if ($positionsShadowingBySunPosition !== null) {
                if ($this->profileBlindLevel['Reversed']) {
                    $positionsNew['BlindLevel'] = min($positionsNew['BlindLevel'], $positionsShadowingBySunPosition['BlindLevel']);
                } else {
                    $positionsNew['BlindLevel'] = max($positionsNew['BlindLevel'], $positionsShadowingBySunPosition['BlindLevel']);
                }

                if (IPS_VariableExists($this->ReadPropertyInteger(self::PROP_SLATSLEVELID))) {
                    if ($this->profileSlatsLevel['Reversed']) {
                        $positionsNew['SlatsLevel'] = min($positionsNew['SlatsLevel'], $positionsShadowingBySunPosition['SlatsLevel']);
                    } else {
                        $positionsNew['SlatsLevel'] = max($positionsNew['SlatsLevel'], $positionsShadowingBySunPosition['SlatsLevel']);
                    }
                }

                if ($positionsNew['BlindLevel'] === $positionsShadowingBySunPosition['BlindLevel']) {
                    if (IPS_VariableExists($this->ReadPropertyInteger(self::PROP_BRIGHTNESSIDSHADOWINGBYSUNPOSITION))) {
                        $Hinweis = 'Beschattung nach Sonnenstand, ' . $this->GetFormattedValue(
                                $this->ReadPropertyInteger(self::PROP_BRIGHTNESSIDSHADOWINGBYSUNPOSITION)
                            );
                    } else {
                        $Hinweis = 'Beschattung nach Sonnenstand';
                    }
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

                if (IPS_VariableExists(($this->ReadPropertyInteger(self::PROP_SLATSLEVELID)))) {
                    if ($this->profileSlatsLevel['Reversed']) {
                        $positionsNew['SlatsLevel'] = min($positionsNew['SlatsLevel'], $positionsShadowingBrightness['SlatsLevel']);
                    } else {
                        $positionsNew['SlatsLevel'] = max($positionsNew['SlatsLevel'], $positionsShadowingBrightness['SlatsLevel']);
                    }
                }

                if ($positionsNew['BlindLevel'] === $positionsShadowingBrightness['BlindLevel']) {
                    $Hinweis = 'Beschattung nach Helligkeit, ' . $this->GetFormattedValue(
                            $this->ReadPropertyInteger(self::PROP_BRIGHTNESSIDSHADOWINGBRIGHTNESS)
                        );
                }
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
            // wenn der Emergency Kontakt geöffnet ist dann
            // wird die Bewegungssperre aufgehoben und der Rollladen geöffnet
            $deactivationTimeAuto       = 0;
            $bNoMove                    = false;
            $positionsNew['BlindLevel'] = $levelContactEmergency;
            $Hinweis                    = 'Notfallkontakt offen';

            //im Notfall wird die Automatik deaktiviert
            $bEmergency = true;

            $this->WriteAttributeBoolean(self::ATTR_CONTACT_OPEN, true);
            $this->Logger_Dbg(
                __FUNCTION__,
                sprintf(
                    'NOTFALL: Kontakt geöffnet (posActBlindLevel: %s, posNewBlindLevel: %s)',
                    $positionsAct['BlindLevel'],
                    $positionsNew['BlindLevel']
                )
            );
        } elseif ($positionsContactOpenBlind !== null) {
            // wenn ein Kontakt geöffnet ist und der Rollladen bzw die Lamellen aktuell unter dem ContactOpen Level steht, dann
            // wird die Bewegungssperre aufgehoben und das Level auf das Mindestlevel bei geöffnetem Kontakt gesetzt
            $bNoMove = false;
            if ($this->profileBlindLevel['Reversed']) {
                if ($positionsContactOpenBlind['BlindLevel'] > $positionsNew['BlindLevel']) {
                    $positionsNew['BlindLevel'] = $positionsContactOpenBlind['BlindLevel'];
                    if ($positionsContactOpenBlind['BlindLevel'] > $positionsAct['BlindLevel']) {
                        $deactivationTimeAuto = 0;
                    }
                    $Hinweis = 'Kontakt offen';
                }
            } elseif ($positionsContactOpenBlind['BlindLevel'] < $positionsNew['BlindLevel']) {
                $positionsNew['BlindLevel'] = $positionsContactOpenBlind['BlindLevel'];
                if ($positionsContactOpenBlind['BlindLevel'] < $positionsAct['BlindLevel']) {
                    $deactivationTimeAuto = 0;
                }
                $Hinweis = 'Kontakt offen';
            }

            if (IPS_VariableExists($this->ReadPropertyInteger(self::PROP_SLATSLEVELID))) {
                if ($this->profileSlatsLevel['Reversed']) {
                    if ($positionsContactOpenBlind['SlatsLevel'] > $positionsNew['SlatsLevel']) {
                        $deactivationTimeAuto       = 0;
                        $positionsNew['SlatsLevel'] = $positionsContactOpenBlind['SlatsLevel'];
                        $Hinweis                    = 'Kontakt offen';
                    }
                } elseif ($positionsContactOpenBlind['SlatsLevel'] < $positionsNew['SlatsLevel']) {
                    $deactivationTimeAuto       = 0;
                    $positionsNew['SlatsLevel'] = $positionsContactOpenBlind['SlatsLevel'];
                    $Hinweis                    = 'Kontakt offen';
                }
            }

            $this->WriteAttributeBoolean(self::ATTR_CONTACT_OPEN, true);
            $this->Logger_Dbg(
                __FUNCTION__,
                sprintf(
                    'Kontakt geöffnet (posActBlindLevel: %s, posNewBlindLevel: %s, posActSlatsLevel: %s, posNewSlatsLevel: %s)',
                    $positionsAct['BlindLevel'],
                    $positionsNew['BlindLevel'],
                    $positionsAct['SlatsLevel'] ?? 'null',
                    $positionsNew['SlatsLevel'] ?? 'null'
                )
            );
        } elseif ($positionsContactCloseBlind !== null) {
            // wenn ein Kontakt geöffnet ist und der Rollladen bzw. die Lamellen oberhalb dem ContactClose Level steht, dann
            // wird die Bewegungssperre aufgehoben und das Level auf das Mindestlevel bei geöffnetem Kontakt gesetzt
            $bNoMove = false;
            if ($this->profileBlindLevel['Reversed']) {
                if ($positionsContactCloseBlind['BlindLevel'] < $positionsNew['BlindLevel']) {
                    $deactivationTimeAuto       = 0;
                    $positionsNew['BlindLevel'] = $positionsContactCloseBlind['BlindLevel'];
                    $Hinweis                    = 'Kontakt offen';
                }
            } elseif ($positionsContactCloseBlind['BlindLevel'] > $positionsNew['BlindLevel']) {
                $deactivationTimeAuto       = 0;
                $positionsNew['BlindLevel'] = $positionsContactCloseBlind['BlindLevel'];
                $Hinweis                    = 'Kontakt offen';
            }

            if (IPS_VariableExists($this->ReadPropertyInteger(self::PROP_SLATSLEVELID))) {
                if ($this->profileSlatsLevel['Reversed']) {
                    if ($positionsContactCloseBlind['SlatsLevel'] < $positionsNew['SlatsLevel']) {
                        $deactivationTimeAuto       = 0;
                        $positionsNew['SlatsLevel'] = $positionsContactCloseBlind['SlatsLevel'];
                        $Hinweis                    = 'Kontakt offen';
                    }
                } elseif ($positionsContactCloseBlind['SlatsLevel'] > $positionsNew['SlatsLevel']) {
                    $deactivationTimeAuto       = 0;
                    $positionsNew['SlatsLevel'] = $positionsContactCloseBlind['SlatsLevel'];
                    $Hinweis                    = 'Kontakt offen';
                }
            }

            $this->WriteAttributeBoolean(self::ATTR_CONTACT_OPEN, true);
            $this->Logger_Dbg(
                __FUNCTION__,
                sprintf(
                    'Kontakt geöffnet (posActBlindLevel: %s, posNewBlindLevel: %s, posActSlatsLevel: %s, posNewSlatsLevel: %s)',
                    $positionsAct['BlindLevel'],
                    $positionsNew['BlindLevel'],
                    $positionsAct['SlatsLevel'] ?? 'null',
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

            if (IPS_VariableExists($this->ReadPropertyInteger(self::PROP_SLATSLEVELID))) {
                $slatsLevel = $positionsNew['SlatsLevel'] / ($this->profileSlatsLevel['MaxValue'] - $this->profileSlatsLevel['MinValue']);
                if ($this->profileSlatsLevel['Reversed']) {
                    $slatsLevel = 1 - $slatsLevel;
                }
                $this->MoveBlind((int)($blindLevel * 100), (int)($slatsLevel * 100), $deactivationTimeAuto, $Hinweis);
            } else {
                $this->MoveBlind((int)($blindLevel * 100), null, $deactivationTimeAuto, $Hinweis);
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
        $this->RegisterAttributeString(self::ATTR_MANUALMOVEMENT,
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
        $this->RegisterVariableBoolean(self::VAR_IDENT_ACTIVATED, $this->Translate('Activated'), '~Switch');
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
                    $this->profileBlindLevel['MinValue'],
                    $this->profileBlindLevel['MaxValue'],
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
            $this->profileSlatsLevel = $this->GetProfileInformation(self::PROP_SLATSLEVELID);
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
                        $this->profileSlatsLevel['MinValue'],
                        $this->profileSlatsLevel['MaxValue'],
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
                if ($variable['VariableCustomAction'] != 0) {
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
            $configuration = IPS_GetConfiguration($var['VariableAction']);
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
            $this->Logger_Err(sprintf('\'%s\': %s: Wert nicht im gültigen Bereich (%s - %s)', $this->objectName, $propName, $min, $max));
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
            } catch (Exception $e) {
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
            $id     = $this->ReadPropertyInteger(constant("self::{$contactIdKey}{$i}ID"));
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
                    $blindPositions['BlindLevel'] = $this->profileBlindLevel['Reversed']
                        ? max($blindPositions['BlindLevel'], $contact['blindlevel'])
                        : min($blindPositions['BlindLevel'], $contact['blindlevel']);

                    if (isset($this->profileSlatsLevel)) {
                        $blindPositions['SlatsLevel'] = $this->profileSlatsLevel['Reversed']
                            ? max($blindPositions['SlatsLevel'], $contact['slatslevel'])
                            : min($blindPositions['SlatsLevel'], $contact['slatslevel']);
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

        if ($prof = $this->GetProfileInformation($propName)) {
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
        $contacts = [];

        if (IPS_VariableExists($this->ReadPropertyInteger(self::PROP_EMERGENCYCONTACTID))) {
            $contacts[self::PROP_EMERGENCYCONTACTID] = [
                'id'    => $this->ReadPropertyInteger(self::PROP_EMERGENCYCONTACTID),
                'level' => $this->profileBlindLevel['LevelOpened']
            ];
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
                    __FUNCTION__,
                    sprintf(
                        'emergency contact is open: #%s, value: %s, level: %s',
                        $contact['id'],
                        $this->GetFormattedValue($contact['id']),
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
        $brightnessAvgMinutes = $this->ReadPropertyInteger($propBrightnessAvgMinutes);

        $brightness = (float)GetValue($brightnessID);

        if ($brightnessAvgMinutes > 0) {
            $archiveId = IPS_GetInstanceListByModuleID('{43192F0B-135B-4CE7-A0A7-1475603F3060}')[0];
            if (AC_GetLoggingStatus($archiveId, $brightnessID)) {
                $werte = @AC_GetAggregatedValues($archiveId, $brightnessID, 6, strtotime('-' . $brightnessAvgMinutes . ' minutes'), time(), 0);
                if ($werte === false || (count($werte) === 0)) {
                    //bei der Sommer auf Winterzeitumstellung gab es eine Warning (EndTime is before StartTime) um kurz vor 3
                    return (float)GetValue($brightnessID);
                }

                $sum = 0;
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

        return (float)GetValue($brightnessID);
    }

    private function getBrightnessThreshold(int $thresholdIDBrightness, float $levelAct, float $temperature = null): float
    {
        $thresholdBrightness = (float)GetValue($thresholdIDBrightness);

        $iBrightnessHysteresis = 0.1 * $thresholdBrightness;

        if ($temperature !== null) {
            //bei Temperaturen über 24 Grad soll der Rollladen auch bei geringerer Helligkeit heruntergefahren werden (10 % Schwellwertverringerung je Grad Temperaturdifferenz zu 24 °C)
            if ($temperature > 24) {
                $thresholdBrightness -= ($temperature - 24) * 0.10 * $thresholdBrightness;
            } //bei Temperaturen unter 10 Grad soll der Rollladen auch bei höherer Helligkeit nicht heruntergefahren werden (10 % Schwellwerterhöhung je Grad Temperaturdifferenz zu 10 °C)
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

    // calculates the level according to the profile
    private function getBlindPositionsFromSunPositionSimple(float $rSunAltitude): array
    {
        $blindPositions = null;

        $blindLevelLow  = $this->ReadPropertyFloat(self::PROP_LOWSUNPOSITIONBLINDLEVEL);
        $blindLevelHigh = $this->ReadPropertyFloat(self::PROP_HIGHSUNPOSITIONBLINDLEVEL);

        $blindPositions['BlindLevel'] = $this->calcPositionSimple($blindLevelLow, $blindLevelHigh, $rSunAltitude);

        if ($this->profileBlindLevel['Reversed']) {
            $blindPositions['BlindLevel'] = min($blindPositions['BlindLevel'], $this->profileBlindLevel['LevelOpened']);
            $blindPositions['BlindLevel'] = max($blindPositions['BlindLevel'], $this->profileBlindLevel['LevelClosed']);
        } else {
            $blindPositions['BlindLevel'] = max($blindPositions['BlindLevel'], $this->profileBlindLevel['LevelOpened']);
            $blindPositions['BlindLevel'] = min($blindPositions['BlindLevel'], $this->profileBlindLevel['LevelClosed']);
        }

        $blindPositions['SlatsLevel'] = $this->calcPositionSimple(
            $this->ReadPropertyFloat(self::PROP_LOWSUNPOSITIONSLATSLEVEL),
            $this->ReadPropertyFloat(self::PROP_HIGHSUNPOSITIONSLATSLEVEL),
            $rSunAltitude
        );

        return $blindPositions;
    }

    private function calcPositionSimple(float $lowPosition, float $highPosition, float $sunAltitude): float
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

    private function getBlindPositionsFromSunPositionExact(float $degSunAltitude, float $degSunAzimuth): array
    {
        $WindowsHeight     = $this->ReadPropertyInteger(self::PROP_WINDOWSHEIGHT);
        $ParapetHeight     = $this->ReadPropertyInteger(self::PROP_PARAPETHEIGHT);
        $WindowsSlope      = $this->ReadPropertyInteger(self::PROP_WINDOWSSLOPE);
        $WindowOrientation = $this->ReadPropertyInteger(self::PROP_WINDOWORIENTATION);
        $DepthSunlight     = $this->ReadPropertyInteger(self::PROP_DEPTHSUNLIGHT);

        //-- Fenster (und Sonne) auf Ost/West ausrichten
        $degSunAzimuth_norm = ($degSunAzimuth - $WindowOrientation) + 180;

        //Sonnenvektor berechnen
        $V_Sun[0] = sin(deg2rad(90 + $degSunAltitude)) * sin(deg2rad($degSunAzimuth_norm));
        $V_Sun[1] = cos(deg2rad(90 + $degSunAltitude));
        $V_Sun[2] = sin(deg2rad(90 + $degSunAltitude)) * cos(deg2rad($degSunAzimuth_norm)) * -1;

        //-- Fensterpositionen (Brüstung + Höhe) bestimmen, geneigtes Fenster berücksichtigen
        $x1 = cos(deg2rad(90 - $WindowsSlope)) * $WindowsHeight;
        $x2 = sin(deg2rad(90 - $WindowsSlope)) * $WindowsHeight;

        //Stützvektoren H (Height) und P (Parapet)
        $H_Window = [0, $ParapetHeight + $x1, $x2];
        $P_Window = [0, $ParapetHeight, 0];

        //-- Schattenpunkte H' und P' bestimmen (siehe https://www.youtube.com/watch?v=QvV-dFlH63c&t=87s)
        $H_Shadow = $x2 + $this->Schattenpunkt_X0_X2_Ebene($H_Window, $V_Sun);
        $P_Shadow = $this->Schattenpunkt_X0_X2_Ebene($P_Window, $V_Sun);


        //-- Rollo-Position bestimmen (0 = open, 1 = closed)

        if ($DepthSunlight > $H_Shadow) {
            $degreeOfShadowing = 0;
        } elseif ($DepthSunlight < $P_Shadow) {
            $degreeOfShadowing = 1;
        } else {
            $degreeOfShadowing = 1 - ($DepthSunlight - $P_Shadow) / ($H_Shadow - $P_Shadow);
        }

        $degreeOfShadowing = max(min($degreeOfShadowing, 1), 0);

        $this->Logger_Dbg(
            __FUNCTION__,
            sprintf(
                'WindowsOrientation: %s, WindowsSlope: %s, WindowsHeigth: %s, ParapetHeigth: %s, DepthSunLight: %s => H_Shadow: %.0f, P_Shadow: %.0f, degreeOfShadowing (100%%=closed): %.0f%%',
                $WindowOrientation,
                $WindowsSlope,
                $WindowsHeight,
                $ParapetHeight,
                $DepthSunlight,
                $H_Shadow,
                $P_Shadow,
                $degreeOfShadowing * 100
            )
        );

        if ($degreeOfShadowing == 0) {
            if (isset($this->profileSlatsLevel)) {
                return ['BlindLevel' => $this->profileBlindLevel['LevelOpened'], 'SlatsLevel' => $this->profileSlatsLevel['LevelOpened']];
            }
            return ['BlindLevel' => $this->profileBlindLevel['LevelOpened'], 'SlatsLevel' => null];
        }

        return $this->GetBlindPositionsFromDegreeOfShadowing($degreeOfShadowing);
    }

    private function Schattenpunkt_X0_X2_Ebene(array $Stuetzvektor, array $Vektor): float
    {
        $r = -$Stuetzvektor[1] / $Vektor[1];
        return $r * $Vektor[2];
    }

    private function GetBlindPositionsFromDegreeOfShadowing(float $degreeOfShadowing): array
    {
        $blindPositions = null;

        $blindLevelMin  = $this->ReadPropertyFloat(self::PROP_MINIMUMSHADERELEVANTBLINDLEVEL);
        $blindLevelHalf = $this->ReadPropertyFloat(self::PROP_HALFSHADERELEVANTBLINDLEVEL);
        $blindLevelMax  = $this->ReadPropertyFloat(self::PROP_MAXIMUMSHADERELEVANTBLINDLEVEL);

        if ($blindLevelHalf === 0.0) {
            //Funktion 1.Grades mit f(x) = a * x + b
            $b                            = $blindLevelMin;
            $a                            = ($blindLevelMax - $blindLevelMin);
            $blindPositions['BlindLevel'] = $a * $degreeOfShadowing + $b;

            $this->Logger_Dbg(
                __FUNCTION__,
                sprintf(
                    'blindLevelMin: %s, blindLevelMax: %s -> BlindLevel: %.2f',
                    $blindLevelMin,
                    $blindLevelMax,
                    $blindPositions['BlindLevel']
                )
            );
        } else {
            //Funktion 2.Grades mit x(x) = a * x² + b * x + c
            $c                            = $blindLevelMin;
            $b                            = 4 * $blindLevelHalf - $blindLevelMax - 3 * $blindLevelMin;
            $a                            = $blindLevelMax - $blindLevelMin - $b;
            $blindPositions['BlindLevel'] = $a * $degreeOfShadowing ** 2 + $b * $degreeOfShadowing + $c;

            $this->Logger_Dbg(
                __FUNCTION__,
                sprintf(
                    'blindLevelMin: %s, blindLevelHalf: %s, blindLevelMax: %s -> BlindLevel: %.2f',
                    $blindLevelMin,
                    $blindLevelHalf,
                    $blindLevelMax,
                    $blindPositions['BlindLevel']
                )
            );
        }

        if ($this->profileBlindLevel['Reversed']) {
            $blindPositions['BlindLevel'] = min($blindPositions['BlindLevel'], $this->profileBlindLevel['LevelOpened']);
            $blindPositions['BlindLevel'] = max($blindPositions['BlindLevel'], $this->profileBlindLevel['LevelClosed']);
        } else {
            $blindPositions['BlindLevel'] = max($blindPositions['BlindLevel'], $this->profileBlindLevel['LevelOpened']);
            $blindPositions['BlindLevel'] = min($blindPositions['BlindLevel'], $this->profileBlindLevel['LevelClosed']);
        }

        $slatsLevelMin = $this->ReadPropertyFloat(self::PROP_MINIMUMSHADERELEVANTSLATSLEVEL);
        $slatsLevelMax = $this->ReadPropertyFloat(self::PROP_MAXIMUMSHADERELEVANTSLATSLEVEL);

        $blindPositions['SlatsLevel'] = ($slatsLevelMax - $slatsLevelMin) * $degreeOfShadowing + $slatsLevelMin;

        return $blindPositions;
    }

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

    private function isMovementLocked(
        float $blindLevelAct,
        ?float $slatsLevelAct,
        int $tsBlindLastMovement,
        bool $isDay,
        int $tsIsDayChanged,
        int $tsAutomatik,
        float $blindLevelClosed,
        float $blindLevelOpened,
        ?float $slatsLevelClosed,
        ?float $slatsLevelOpened
    ): bool {
        $this->Logger_Dbg(
            __FUNCTION__,
            sprintf(
                'Parameter: blindLevelAct: %s, slatsLevelAct: %s, tsBlindLastMovement: %s, isDay: %s, tsIsDayChanged: %s, tsAutomatik: %s, blindLevelClosed: %s, blindLevelOpened: %s, slatsLevelClosed: %s, slatsLevelOpened: %s',
                $blindLevelAct,
                $slatsLevelAct ?? 'null',
                $this->FormatTimeStamp($tsBlindLastMovement),
                (int)$isDay,
                $this->FormatTimeStamp($tsIsDayChanged),
                $this->FormatTimeStamp($tsAutomatik),
                $blindLevelClosed,
                $blindLevelOpened,
                $slatsLevelClosed ?? 'null',
                $slatsLevelOpened ?? 'null'
            )
        ); //todo: sollte wieder entfernt werden

        //zuerst prüfen, ob der Rollladen nach der letzten aut. Bewegung (+ 5 Sekunden) manuell bewegt wurde
        //die Karenzzeit von 5 Sekunden ermöglicht ein eventuelles Nachführen der Levelvariablen (z.B. beim HmIP-BROLL)
        if ($tsBlindLastMovement <= strtotime('+5 sec', $tsAutomatik)) {
            return false;
        }

        $deactivationTimeManuSecs = $this->ReadPropertyInteger(self::PROP_DEACTIVATIONMANUALMOVEMENT) * 60;

        //Zeitpunkt festhalten, sofern noch nicht geschehen
        if ($tsBlindLastMovement !== json_decode($this->ReadAttributeString(self::ATTR_MANUALMOVEMENT), true, 512, JSON_THROW_ON_ERROR)['timeStamp']) {
            $this->WriteAttributeString(
                self::ATTR_MANUALMOVEMENT,
                json_encode(['timeStamp' => $tsBlindLastMovement, 'blindLevel' => $blindLevelAct, 'slatsLevel' => $slatsLevelAct],
                            JSON_THROW_ON_ERROR)
            );

            if ($slatsLevelAct === null) {
                $txtSlatsLevelAct = 'null';
            } else {
                $txtSlatsLevelAct = sprintf('%.2f', $slatsLevelAct);
            }

            $this->Logger_Dbg(
                __FUNCTION__,
                sprintf(
                    'Rollladen wurde manuell gesetzt. blindLevelAct: %.2f, slatsLevelAct: %s, TimestampAutomatic: %s, TimestampManual: %s, deactivationTimeManuSecs: %s/%s',
                    $blindLevelAct,
                    $txtSlatsLevelAct,
                    $this->FormatTimeStamp($tsAutomatik),
                    $this->FormatTimeStamp(
                        json_decode($this->ReadAttributeString(self::ATTR_MANUALMOVEMENT), true, 512, JSON_THROW_ON_ERROR)['timeStamp']),
                    time() - $tsBlindLastMovement,
                    $deactivationTimeManuSecs
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
                            '\'%s\' wurde manuell auf %.0f%%(Höhe), %.0f%%(Lamellen) gefahren.',
                            $this->objectName,
                            100 * $blindLevelPercent,
                            100 * $slatsLevelPercent
                        )
                    );
                }
            }
        }

        $bNoMove          = false;
        $tsManualMovement = json_decode($this->ReadAttributeString(self::ATTR_MANUALMOVEMENT), true, 512, JSON_THROW_ON_ERROR)['timeStamp'];

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
                    __FUNCTION__,
                    sprintf(
                        'Rollladen wurde manuell bewegt (Tag: %s). DeactivationTimeManu: %s/%s',
                        date('H:i:s', $tsManualMovement),
                        time() - $tsManualMovement,
                        $deactivationTimeManuSecs
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
            __FUNCTION__,
            sprintf(
                'percentBlindClose: %s, percentSlatClose: %s, deactivationTimeAuto: %s, hint: %s',
                $percentBlindClose,
                $percentSlatsClosed ?? 'null',
                $deactivationTimeAuto,
                $hint
            )
        );

        if (($percentBlindClose < 0) || ($percentBlindClose > 100) || ($percentSlatsClosed < 0) || ($percentSlatsClosed > 100)) {
            return false;
        }

        // globale Instanzvariablen setzen
        $this->profileBlindLevel = $this->GetProfileInformation(self::PROP_BLINDLEVELID);

        if ($this->profileBlindLevel === null) {
            return false;
        }

        $tsAutomatic = $this->ReadAttributeInteger(self::ATTR_TIMESTAMP_AUTOMATIC);
        $moveBladeOk = $this->MoveToPosition(self::PROP_BLINDLEVELID, $percentBlindClose, $tsAutomatic, $deactivationTimeAuto, $hint);

        //gibt es Lamellen?
        if (IPS_VariableExists($this->ReadPropertyInteger(self::PROP_SLATSLEVELID))) {
            $this->profileSlatsLevel = $this->GetProfileInformation(self::PROP_SLATSLEVELID);
            $moveSlatsOk             =
                $this->MoveToPosition(self::PROP_SLATSLEVELID, $percentSlatsClosed, $tsAutomatic, $deactivationTimeAuto, $hint);

            return $moveBladeOk || $moveSlatsOk;
        }

        return $moveBladeOk;
    }

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
        $this->profileBlindLevel = $this->GetProfileInformation(self::PROP_BLINDLEVELID);

        if ($this->profileBlindLevel === null) {
            return false;
        }

        $blindPositions = $this->GetBlindPositionsFromDegreeOfShadowing($percentShadowing / 100);

        $percentCloseBlind = $blindPositions['BlindLevel'] / ($this->profileBlindLevel['MaxValue'] - $this->profileBlindLevel['MinValue']);
        if ($this->profileBlindLevel['Reversed']) {
            $percentCloseBlind = 1 - $percentCloseBlind;
        }

        $moveBladeOk =
            $this->MoveToPosition(self::PROP_BLINDLEVELID, (int)($percentCloseBlind * 100), 0, 0, sprintf('%s Beschattung', $percentCloseBlind));

        //gibt es Lamellen?
        if (IPS_VariableExists($this->ReadPropertyInteger(self::PROP_SLATSLEVELID))) {
            $this->profileSlatsLevel = $this->GetProfileInformation(self::PROP_SLATSLEVELID);
            $percentCloseSlats       = $blindPositions['SlatsLevel'] / ($this->profileSlatsLevel['MaxValue'] - $this->profileSlatsLevel['MinValue']);
            if ($this->profileSlatsLevel['Reversed']) {
                $percentCloseSlats = 1 - $percentCloseSlats;
            }
            $moveSlatsOk =
                $this->MoveToPosition(self::PROP_SLATSLEVELID, (int)($percentCloseSlats * 100), 0, 0, sprintf('%s Beschattung', $percentCloseSlats));

            return $moveBladeOk || $moveSlatsOk;
        }

        return $moveBladeOk;
    }

    private function MoveToPosition(string $propName, int $percentClose, int $tsAutomatic, int $deactivationTimeAuto, string $hint): bool
    {
        $positionID = $this->ReadPropertyInteger($propName);
        if (!IPS_VariableExists($positionID)) {
            return false;
        }

        $profile = $this->GetProfileInformation($propName);
        if ($profile === null) {
            return false;
        }

        $lastMove = json_decode($this->ReadAttributeString(self::ATTR_LASTMOVE . $propName), true, 512, JSON_THROW_ON_ERROR);

        if (((int)$lastMove['percentClose'] === $percentClose) && ($lastMove['timeStamp'] > strtotime('-' . self::IGNORE_MOVEMENT_TIME . ' secs'))) {
            //dieselbe Bewegung in den letzten 40 Sekunden
            $this->Logger_Dbg(
                __FUNCTION__,
                sprintf(
                    '#%s(%s): Move ignored! Same percentClose %s just %s s before',
                    $positionID,
                    $propName,
                    $percentClose,
                    time() - $lastMove['timeStamp']
                )
            );
            // Timestamp der Automatik merken (sonst wird die Bewegung später als manuelle Bewegung erkannt)
            $this->WriteAttributeInteger(self::ATTR_TIMESTAMP_AUTOMATIC, time());

            return false;
        }

        $this->Logger_Dbg(
            __FUNCTION__,
            sprintf('%s (#%s): percentClose %s%% after %s s', $propName, $positionID, $percentClose, time() - $lastMove['timeStamp'])
        );

        $positionNew = $profile['MinValue'] + ($percentClose / 100) * ($profile['MaxValue'] - $profile['MinValue']);

        if ($profile['Reversed']) {
            $positionNew = $profile['MaxValue'] - $positionNew;
        }

        $positionAct            = (float)GetValue($positionID); //integer and float are supported
        $positionDiffPercentage = abs($positionNew - $positionAct) / ($profile['MaxValue'] - $profile['MinValue']);
        $timeDiffAuto           = time() - $tsAutomatic;

        $this->Logger_Dbg(
            __FUNCTION__,
            sprintf(
                '#%s(%s): positionAct: %s, positionNew: %s, positionDiffPercentage: %f/%f, timeDiffAuto: %s/%s',
                $positionID,
                $propName,
                $positionAct,
                $positionNew,
                $positionDiffPercentage,
                $this->ReadPropertyFloat(self::PROP_MINMOVEMENT) / 100,
                $timeDiffAuto,
                $deactivationTimeAuto
            )
        );

        $ret = false;

        $minMovement              = $this->ReadPropertyFloat(self::PROP_MINMOVEMENT) / 100;
        $minMovementAtEndPosition = $this->ReadPropertyFloat(self::PROP_MINMOVEMENTATENDPOSITION) / 100;

        if ($timeDiffAuto < $deactivationTimeAuto) {
            $this->Logger_Dbg(
                __FUNCTION__,
                sprintf(
                    '#%s(%s): No Movement! DeactivationTimeAuto of %s not reached (%s).',
                    $positionID,
                    $propName,
                    $deactivationTimeAuto,
                    $timeDiffAuto
                )
            );
        } elseif ($positionDiffPercentage <= (self::ALLOWED_TOLERANCE_MOVEMENT / 100)) {
            $this->Logger_Dbg(
                __FUNCTION__,
                sprintf('#%s(%s): No Movement! Position %s already reached.', $positionID, $propName, $positionAct)
            );
        } elseif (($positionDiffPercentage < $minMovement) && !in_array($positionNew, [$profile['MinValue'], $profile['MaxValue']], false)) {
            $this->Logger_Dbg(
                __FUNCTION__,
                sprintf(
                    '#%s(%s): No Movement! Movement less than %s percent (%.3f).',
                    $positionID,
                    $propName,
                    $minMovement * 100,
                    $positionDiffPercentage
                )
            );
        } elseif (($positionDiffPercentage < $minMovementAtEndPosition / 100)) {
            $this->Logger_Dbg(
                __FUNCTION__,
                sprintf(
                    '#%s(%s): No Movement! End position already reached. Difference less than %s percent (%.3f).',
                    $positionID,
                    $propName,
                    $minMovementAtEndPosition * 100,
                    $positionDiffPercentage
                )
            );
        } else {
            //Position setzen
            //Wert übertragen
            //$ret = $this->moveAndWait($propName, $positionID, $positionNew);
            if (RequestAction($positionID, $positionNew)) {
                // warten, bis die Zielposition erreicht ist
                $ret = $this->waitUntilBlindLevelIsReached($propName, $positionNew);

                // Timestamp der Automatik merken (sonst wird die Bewegung später als manuelle Bewegung erkannt)
                $this->WriteAttributeInteger(self::ATTR_TIMESTAMP_AUTOMATIC, time());
                $this->WriteAttributeString(
                    self::ATTR_LASTMOVE . $propName,
                    json_encode(['timeStamp' => time(), 'percentClose' => $percentClose, 'hint' => $hint], JSON_THROW_ON_ERROR)
                );
                $this->Logger_Dbg(
                    __FUNCTION__,
                    "$this->objectName: TimestampAutomatik: " . $this->FormatTimeStamp(
                        $this->ReadAttributeInteger(self::ATTR_TIMESTAMP_AUTOMATIC)
                    )
                );
            } else {
                $this->Logger_Err(
                    sprintf(
                        '\'%s\': ID %s (%s): Fehler beim Setzen des Wertes. (Value = %s, Parent: "%s").',
                        $this->objectName,
                        $positionID,
                        $propName,
                        $percentClose,
                        IPS_GetName(IPS_GetParent($positionID))
                    )
                );
            }
            $this->Logger_Dbg(__FUNCTION__, sprintf('#%s(%s): %s to %s', $positionID, $propName, $positionAct, $positionNew));

            if ($ret) {
                $this->WriteInfo($propName, $positionNew, $hint);
            }
        }


        return $ret;
    }

    private function waitUntilBlindLevelIsReached(string $propName, $positionNew): bool
    {
        $levelID                  = $this->ReadPropertyInteger($propName);
        $minMovementAtEndPosition = $this->ReadPropertyFloat(self::PROP_MINMOVEMENTATENDPOSITION);

        $profile         = $this->GetProfileInformation($propName);
        $percentCloseNew = ($positionNew - $profile['MinValue']) / ($profile['MaxValue'] - $profile['MinValue']) * 100;
        if ($profile['Reversed']) {
            $percentCloseNew = 100 - $percentCloseNew;
        }

        for ($i = 0; $i < self::MOVEMENT_WAIT_TIME; $i++) { //wir warten maximal 90 Sekunden
            $currentValue        = GetValue($levelID);
            $percentCloseCurrent = ($currentValue - $profile['MinValue']) / ($profile['MaxValue'] - $profile['MinValue']) * 100;

            if ($profile['Reversed']) {
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

        if ($profile['Reversed']) {
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
        if ($propName === self::PROP_BLINDLEVELID) {
            if ($rLevelneu === (float)$this->profileBlindLevel['LevelClosed']) {
                $logMessage = sprintf('\'%s\' wurde geschlossen.', $this->objectName);
            } elseif ($rLevelneu === (float)$this->profileBlindLevel['LevelOpened']) {
                $logMessage = sprintf('\'%s\' wurde geöffnet.', $this->objectName);
            } else {
                $levelPercent = ($rLevelneu - $this->profileBlindLevel['MinValue']) / ($this->profileBlindLevel['MaxValue']
                                                                                       - $this->profileBlindLevel['MinValue']);
                $logMessage   = sprintf('\'%s\' wurde auf %.0f%% gefahren.', $this->objectName, 100 * $levelPercent);
            }
        } elseif ($rLevelneu === (float)$this->profileSlatsLevel['LevelClosed']) {
            $logMessage = sprintf('Die Lamellen \'%s\' wurden geschlossen.', $this->objectName);
        } elseif ($rLevelneu === (float)$this->profileSlatsLevel['LevelOpened']) {
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
     * @return array|null Returns an associative array containing profile information if the property exists and has a valid profile; otherwise, returns null.
     */
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

        $reversed = strcasecmp('reversed', end($profileNameParts)) === 0; //Groß-/Kleinschreibung wird ignoriert
        switch ($propName) {
            case self::PROP_BLINDLEVELID:
            case self::PROP_SLATSLEVELID:
                return [
                    'Name'        => $profileName,
                    'ProfileType' => $profile['ProfileType'],
                    'MinValue'    => $profile['MinValue'],
                    'MaxValue'    => $profile['MaxValue'],
                    'Reversed'    => $reversed,
                    'LevelOpened' => $reversed ? (float)$profile['MaxValue'] : (float)$profile['MinValue'],
                    'LevelClosed' => $reversed ? (float)$profile['MinValue'] : (float)$profile['MaxValue']
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
