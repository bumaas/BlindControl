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

    // -- property names --
    private const PROP_BLINDLEVELID                      = 'BlindLevelID';
    private const PROP_SLATSLEVELID                      = 'SlatsLevelID';
    private const PROP_HOLIDAYINDICATORID                = 'HolidayIndicatorID';
    private const PROP_DAYUSEDWHENHOLIDAY                = 'DayUsedWhenHoliday';
    private const PROP_WAKEUPTIMEID                      = 'WakeUpTimeID';
    private const PROP_WAKEUPTIMEOFFSET                  = 'WakeUpTimeOffset';
    private const PROP_BEDTIMEID                         = 'BedTimeID';
    private const PROP_BEDTIMEOFFSET                     = 'BedTimeOffset';
    private const PROP_CONTACTCLOSE1ID                   = 'ContactClose1ID';
    private const PROP_CONTACTCLOSE2ID                   = 'ContactClose2ID';
    private const PROP_CONTACTCLOSELEVEL1                = 'ContactCloseLevel1';
    private const PROP_CONTACTCLOSELEVEL2                = 'ContactCloseLevel2';
    private const PROP_CONTACTCLOSESLATSLEVEL1           = 'ContactCloseSlatsLevel1';
    private const PROP_CONTACTCLOSESLATSLEVEL2           = 'ContactCloseSlatsLevel2';
    private const PROP_CONTACTOPEN1ID                    = 'ContactOpen1ID';
    private const PROP_CONTACTOPEN2ID                    = 'ContactOpen2ID';
    private const PROP_CONTACTOPENLEVEL1                 = 'ContactOpenLevel1';
    private const PROP_CONTACTOPENLEVEL2                 = 'ContactOpenLevel2';
    private const PROP_CONTACTOPENSLATSLEVEL1            = 'ContactOpenSlatsLevel1';
    private const PROP_CONTACTOPENSLATSLEVEL2            = 'ContactOpenSlatsLevel2';
    private const PROP_EMERGENCYCONTACTID                = 'EmergencyContactID';
    private const PROP_CONTACTSTOCLOSEHAVEHIGHERPRIORITY = 'ContactsToCloseHaveHigherPriority';

    //shadowing according to sun position
    private const PROP_ACTIVATORIDSHADOWINGBYSUNPOSITION           = 'ActivatorIDShadowingBySunPosition';
    private const PROP_AZIMUTHID                                   = 'AzimuthID';
    private const PROP_ALTITUDEID                                  = 'AltitudeID';
    private const PROP_AZIMUTHFROM                                 = 'AzimuthFrom';
    private const PROP_AZIMUTHTO                                   = 'AzimuthTo';
    private const PROP_ALTITUDEFROM                                = 'AltitudeFrom';
    private const PROP_ALTITUDETO                                  = 'AltitudeTo';
    private const PROP_BRIGHTNESSIDSHADOWINGBYSUNPOSITION          = 'BrightnessIDShadowingBySunPosition';
    private const PROP_BRIGHTNESSAVGMINUTESSHADOWINGBYSUNPOSITION  = 'BrightnessAvgMinutesShadowingBySunPosition';
    private const PROP_BRIGHTNESSTHRESHOLDIDSHADOWINGBYSUNPOSITION = 'BrightnessThresholdIDShadowingBySunPosition';
    private const PROP_LOWSUNPOSITIONBLINDLEVEL                    = 'LowSunPositionBlindLevel';
    private const PROP_HIGHSUNPOSITIONBLINDLEVEL                   = 'HighSunPositionBlindLevel';
    private const PROP_LOWSUNPOSITIONSLATSLEVEL                    = 'LowSunPositionSlatsLevel';
    private const PROP_HIGHSUNPOSITIONSLATSLEVEL                   = 'HighSunPositionSlatsLevel';
    private const PROP_DEPTHSUNLIGHT                               = 'DepthSunLight';
    private const PROP_WINDOWORIENTATION                           = 'WindowOrientation';
    private const PROP_WINDOWSSLOPE                                = 'WindowsSlope';
    private const PROP_WINDOWSHEIGHT                               = 'WindowHeight';
    private const PROP_PARAPETHEIGHT                               = 'ParapetHeight';
    private const PROP_MINIMUMSHADERELEVANTBLINDLEVEL              = 'MinimumShadeRelevantBlindLevel';
    private const PROP_HALFSHADERELEVANTBLINDLEVEL                 = 'HalfShadeRelevantBlindLevel';
    private const PROP_MAXIMUMSHADERELEVANTBLINDLEVEL              = 'MaximumShadeRelevantBlindLevel';
    private const PROP_MINIMUMSHADERELEVANTSLATSLEVEL              = 'MinimumShadeRelevantSlatsLevel';
    private const PROP_MAXIMUMSHADERELEVANTSLATSLEVEL              = 'MaximumShadeRelevantSlatsLevel';


    //shadowing according to brightness
    private const PROP_BRIGHTNESSIDSHADOWINGBRIGHTNESS         = 'BrightnessIDShadowingBrightness';
    private const PROP_BRIGHTNESSAVGMINUTESSHADOWINGBRIGHTNESS = 'BrightnessAvgMinutesShadowingBrightness';

    private const PROP_ACTIVATEDINDIVIDUALDAYLEVELS                = 'ActivatedIndividualDayLevels';
    private const PROP_DAYBLINDLEVEL                               = 'DayBlindLevel';
    private const PROP_DAYSLATSLEVEL                               = 'DaySlatsLevel';
    private const PROP_ACTIVATEDINDIVIDUALNIGHTLEVELS              = 'ActivatedIndividualNightLevels';
    private const PROP_NIGHTBLINDLEVEL                             = 'NightBlindLevel';
    private const PROP_NIGHTSLATSLEVEL                             = 'NightSlatsLevel';
    private const PROP_ISDAYINDICATORID                            = 'IsDayIndicatorID';
    private const PROP_BRIGHTNESSID                                = 'BrightnessID';
    private const PROP_BRIGHTNESSAVGMINUTES                        = 'BrightnessAvgMinutes';
    private const PROP_BRIGHTNESSTHRESHOLDID                       = 'BrightnessThresholdID';
    private const PROP_BLINDLEVELLESSBRIGHTNESSSHADOWINGBRIGHTNESS = 'BlindLevelLessBrightnessShadowingBrightness';
    private const PROP_SLATSLEVELLESSBRIGHTNESSSHADOWINGBRIGHTNESS = 'SlatsLevelLessBrightnessShadowingBrightness';
    private const PROP_BLINDLEVELHIGHBRIGHTNESSSHADOWINGBRIGHTNESS = 'BlindLevelHighBrightnessShadowingBrightness';
    private const PROP_SLATSLEVELHIGHBRIGHTNESSSHADOWINGBRIGHTNESS = 'SlatsLevelHighBrightnessShadowingBrightness';
    private const PROP_DAYSTARTID                                  = 'DayStartID';
    private const PROP_DAYENDID                                    = 'DayEndID';
    private const PROP_UPDATEINTERVAL                              = 'UpdateInterval';
    private const PROP_DELAYTIMEDAYNIGHTCHANGE                     = 'DelayTimeDayNightChange';
    private const PROP_DELAYTIMEDAYNIGHTCHANGEISRANDOMLY           = 'DelayTimeDayNightChangeIsRandomly';
    private const PROP_SHOWNOTUSEDELEMENTS                         = 'ShowNotUsedElements';

    //attribute names
    private const ATTR_MANUALMOVEMENT           = 'manualMovement';
    private const ATTR_LASTMOVE                 = 'lastMovement';
    private const ATTR_TIMESTAMP_AUTOMATIC      = 'TimeStampAutomatic';
    private const ATTR_CONTACT_OPEN             = 'AttrContactOpen';
    private const ATTR_DAYTIME_CHANGE_TIME      = 'DaytimeChangeTime';
    private const ATTR_LAST_ISDAYBYTIMESCHEDULE = 'LastIsDayByTimeSchedule';

    //timer names
    private const TIMER_UPDATE           = 'Update';
    private const TIMER_DELAYED_MOVEMENT = 'DelayedMovement';


    //variable names
    private const VAR_IDENT_LAST_MESSAGE = 'LAST_MESSAGE';
    private const VAR_IDENT_ACTIVATED    = 'ACTIVATED';

    private $objectName;

    private $profileBlindLevel;

    private $profileSlatsLevel;


    // die folgenden Funktionen überschreiben die interne IPS_() Funktionen
    public function __construct($InstanceID)
    {
        $this->objectName = IPS_GetObject($InstanceID)['ObjectName'];
        //echo $this->objectName . PHP_EOL;
        parent::__construct($InstanceID);
    }

    public function Create()
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

    public function ApplyChanges()
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

        $this->ShowNotUsedElements($this->ReadPropertyBoolean(self::PROP_SHOWNOTUSEDELEMENTS));
    }

    public function RequestAction($Ident, $Value): bool
    {
        if (is_bool($Value)) {
            $this->Logger_Dbg(__FUNCTION__, sprintf('Ident: %s, Value: %s', $Ident, (int)$Value));
        } else {
            $this->Logger_Dbg(__FUNCTION__, sprintf('Ident: %s, Value: %s', $Ident, $Value));
        }

        switch ($Ident) {
            case self::VAR_IDENT_ACTIVATED:
                if ($Value) {
                    //reset manual movement
                    $this->resetManualMovement();
                } else {
                    $this->Logger_Inf(sprintf('\'%s\' wurde deaktiviert.', IPS_GetObject($this->InstanceID)['ObjectName']));
                }
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
                return false;

            case self::PROP_HOLIDAYINDICATORID:
                $this->UpdateFormField(
                    self::PROP_DAYUSEDWHENHOLIDAY,
                    'visible',
                    ($Value > 0) || $this->ReadPropertyBoolean(self::PROP_SHOWNOTUSEDELEMENTS)
                );
                return false;

            case self::PROP_WAKEUPTIMEID:
                $this->UpdateFormField(
                    self::PROP_WAKEUPTIMEOFFSET,
                    'visible',
                    ($Value > 0) || $this->ReadPropertyBoolean(self::PROP_SHOWNOTUSEDELEMENTS)
                );
                return false;

            case self::PROP_BEDTIMEID:
                $this->UpdateFormField(
                    self::PROP_BEDTIMEOFFSET,
                    'visible',
                    ($Value > 0) || $this->ReadPropertyBoolean(self::PROP_SHOWNOTUSEDELEMENTS)
                );
                return false;

            case self::PROP_ACTIVATEDINDIVIDUALDAYLEVELS:
                $this->UpdateFormField(self::PROP_DAYBLINDLEVEL, 'visible', $Value || $this->ReadPropertyBoolean(self::PROP_SHOWNOTUSEDELEMENTS));
                $this->UpdateFormField(
                    self::PROP_DAYSLATSLEVEL,
                    'visible',
                    (($this->ReadPropertyInteger(self::PROP_SLATSLEVELID) > 0) && $Value)
                    || $this->ReadPropertyBoolean(
                        self::PROP_SHOWNOTUSEDELEMENTS
                    )
                );
                return false;

            case self::PROP_ACTIVATEDINDIVIDUALNIGHTLEVELS:
                $this->UpdateFormField(self::PROP_NIGHTBLINDLEVEL, 'visible', $Value || $this->ReadPropertyBoolean(self::PROP_SHOWNOTUSEDELEMENTS));
                $this->UpdateFormField(
                    self::PROP_NIGHTSLATSLEVEL,
                    'visible',
                    (($this->ReadPropertyInteger(self::PROP_SLATSLEVELID) > 0) && $Value)
                    || $this->ReadPropertyBoolean(
                        self::PROP_SHOWNOTUSEDELEMENTS
                    )
                );
                return false;

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
                return false;

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
                return false;

            case self::PROP_BRIGHTNESSIDSHADOWINGBRIGHTNESS:
                $this->UpdateFormField(
                    self::PROP_BRIGHTNESSAVGMINUTESSHADOWINGBRIGHTNESS,
                    'visible',
                    $Value || $this->ReadPropertyBoolean(self::PROP_SHOWNOTUSEDELEMENTS)
                );
                return false;

            default:
                trigger_error(sprintf('Instance %s: Unknown Ident %s', $this->InstanceID, $Ident));
                return false;
        }

        if ($this->SetValue($Ident, $Value)) {
            $this->SetInstanceStatusAndTimerEvent();
            IPS_RunScriptText(sprintf('BLC_ControlBlind(%s, %s);', $this->InstanceID, 'false'));
            return true;
        }

        return false;
    }

    public function MessageSink($TimeStamp, $SenderID, $Message, $Data)
    {
        parent::MessageSink($TimeStamp, $SenderID, $Message, $Data);

        if (json_decode($this->GetBuffer('LastMessage'), true) === [$SenderID, $Message, $Data]) {
            $this->Logger_Dbg(
                __FUNCTION__,
                sprintf(
                    'Duplicate Message: Timestamp: %s, SenderID: %s, Message: %s, Data: %s',
                    $TimeStamp,
                    $SenderID,
                    $Message,
                    json_encode($Data)
                )
            );
            return;
        }

        $this->SetBuffer('LastMessage', json_encode([$SenderID, $Message, $Data]));

        $this->Logger_Dbg(
            __FUNCTION__,
            sprintf(
                'ModuleVersion: %s, Timestamp: %s, SenderID: %s[%s], Message: %s, Data: %s',
                $this->ReadAttributeString('Version'),
                $TimeStamp,
                IPS_GetObject($SenderID)['ObjectName'],
                $SenderID,
                $Message,
                json_encode($Data)
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
                            $this->ReadPropertyInteger('ActivatorIDShadowingBrightness'),
                            $this->ReadPropertyInteger(self::PROP_ACTIVATORIDSHADOWINGBYSUNPOSITION)
                        ],
                        true
                    );

                    if (IPS_GetKernelRunlevel() === KR_READY) {
                        //Skripte können nur gestartet werden, wenn der Kernel ready ist
                        IPS_RunScriptText(sprintf('BLC_ControlBlind(%s, %s);', $this->InstanceID, $considerDeactivationTimeAuto ? 'true' : 'false'));
                    }
                }

                break;
        }
    }

    public function GetConfigurationForm()
    {
        $form = json_decode(file_get_contents(__DIR__ . '/form.json'), true);

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

        $this->SendDebug('Form', json_encode($form), 0);
        return json_encode($form);
    }

    private function SetVisibilityOfNotUsedElements(array &$form): void
    {
        $bShow = $this->ReadPropertyBoolean(self::PROP_SHOWNOTUSEDELEMENTS);

        $form = $this->MyUpdateFormField(
            $form,
            self::PROP_DAYUSEDWHENHOLIDAY,
            'visible',
            (($this->ReadPropertyInteger(self::PROP_HOLIDAYINDICATORID) > 0) || $bShow)
        );
        $form = $this->MyUpdateFormField(
            $form,
            self::PROP_WAKEUPTIMEOFFSET,
            'visible',
            ($this->ReadPropertyInteger(self::PROP_WAKEUPTIMEID) > 0) || $bShow
        );
        $form =
            $this->MyUpdateFormField($form, self::PROP_BEDTIMEOFFSET, 'visible', ($this->ReadPropertyInteger(self::PROP_BEDTIMEID) > 0) || $bShow);
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
            ((($this->ReadPropertyInteger(self::PROP_SLATSLEVELID) > 0) && $this->ReadPropertyBoolean(self::PROP_ACTIVATEDINDIVIDUALNIGHTLEVELS))
             || $bShow)
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
            ((($this->ReadPropertyInteger(self::PROP_SLATSLEVELID) > 0) && $this->ReadPropertyBoolean(self::PROP_ACTIVATEDINDIVIDUALDAYLEVELS))
             || $bShow)
        );
        $form = $this->MyUpdateFormField(
            $form,
            self::PROP_BRIGHTNESSAVGMINUTES,
            'visible',
            ($this->ReadPropertyInteger(self::PROP_BRIGHTNESSID) > 0) || $bShow
        );
        $form = $this->MyUpdateFormField(
            $form,
            self::PROP_BRIGHTNESSTHRESHOLDID,
            'visible',
            ($this->ReadPropertyInteger(self::PROP_BRIGHTNESSID) > 0) || $bShow
        );
        $form = $this->MyUpdateFormField(
            $form,
            self::PROP_BRIGHTNESSAVGMINUTESSHADOWINGBYSUNPOSITION,
            'visible',
            ($this->ReadPropertyInteger(self::PROP_BRIGHTNESSIDSHADOWINGBYSUNPOSITION) > 0) || $bShow
        );
        $form = $this->MyUpdateFormField(
            $form,
            self::PROP_BRIGHTNESSTHRESHOLDIDSHADOWINGBYSUNPOSITION,
            'visible',
            ($this->ReadPropertyInteger(self::PROP_BRIGHTNESSIDSHADOWINGBYSUNPOSITION) > 0) || $bShow
        );
        $form = $this->MyUpdateFormField(
            $form,
            self::PROP_LOWSUNPOSITIONSLATSLEVEL,
            'visible',
            (($this->ReadPropertyInteger(self::PROP_SLATSLEVELID) > 0) || $bShow)
        );
        $form = $this->MyUpdateFormField(
            $form,
            self::PROP_HIGHSUNPOSITIONSLATSLEVEL,
            'visible',
            (($this->ReadPropertyInteger(self::PROP_SLATSLEVELID) > 0) || $bShow)
        );
        $form = $this->MyUpdateFormField(
            $form,
            self::PROP_MINIMUMSHADERELEVANTSLATSLEVEL,
            'visible',
            (($this->ReadPropertyInteger(self::PROP_SLATSLEVELID) > 0) || $bShow)
        );
        $form = $this->MyUpdateFormField(
            $form,
            self::PROP_MAXIMUMSHADERELEVANTSLATSLEVEL,
            'visible',
            (($this->ReadPropertyInteger(self::PROP_SLATSLEVELID) > 0) || $bShow)
        );
        $form = $this->MyUpdateFormField(
            $form,
            self::PROP_BRIGHTNESSAVGMINUTESSHADOWINGBRIGHTNESS,
            'visible',
            ($this->ReadPropertyInteger(self::PROP_BRIGHTNESSIDSHADOWINGBRIGHTNESS) > 0) || $bShow
        );
        $form = $this->MyUpdateFormField(
            $form,
            self::PROP_SLATSLEVELLESSBRIGHTNESSSHADOWINGBRIGHTNESS,
            'visible',
            (($this->ReadPropertyInteger(self::PROP_SLATSLEVELID) > 0) || $bShow)
        );
        $form = $this->MyUpdateFormField(
            $form,
            self::PROP_SLATSLEVELHIGHBRIGHTNESSSHADOWINGBRIGHTNESS,
            'visible',
            (($this->ReadPropertyInteger(self::PROP_SLATSLEVELID) > 0) || $bShow)
        );
        $form = $this->MyUpdateFormField(
            $form,
            self::PROP_CONTACTCLOSESLATSLEVEL1,
            'visible',
            (($this->ReadPropertyInteger(self::PROP_SLATSLEVELID) > 0) || $bShow)
        );
        $form = $this->MyUpdateFormField(
            $form,
            self::PROP_CONTACTCLOSESLATSLEVEL2,
            'visible',
            (($this->ReadPropertyInteger(self::PROP_SLATSLEVELID) > 0) || $bShow)
        );
        $form = $this->MyUpdateFormField(
            $form,
            self::PROP_CONTACTOPENSLATSLEVEL1,
            'visible',
            (($this->ReadPropertyInteger(self::PROP_SLATSLEVELID) > 0) || $bShow)
        );
        $form = $this->MyUpdateFormField(
            $form,
            self::PROP_CONTACTOPENSLATSLEVEL2,
            'visible',
            (($this->ReadPropertyInteger(self::PROP_SLATSLEVELID) > 0) || $bShow)
        );
        $form = $this->MyUpdateFormField(
            $form,
            'SlatsLevel',
            'visible',
            (($this->ReadPropertyInteger(self::PROP_SLATSLEVELID) > 0) || $bShow)
        );
    }

    public function ReceiveData($JSONString)
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
        $positionsAct['BlindLevel'] = (float)GetValue($blindLevelId);

        //Slats Level ID ermitteln
        $slatsLevelId = $this->ReadPropertyInteger(self::PROP_SLATSLEVELID);
        if ($slatsLevelId !== 0) {
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
            $this->WriteAttributeString(self::ATTR_MANUALMOVEMENT, json_encode(['timeStamp' => null, 'blindLevel' => null, 'slatsLevel' => null]));

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
                $this->ReadAttributeString('Version'),
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
            $lastManualMovement         = json_decode($this->ReadAttributeString(self::ATTR_MANUALMOVEMENT), true);
            $deactivationManualMovement = $this->ReadPropertyInteger('DeactivationManualMovement');
            if (isset($lastManualMovement['timeStamp'])
                && (($deactivationManualMovement === 0)
                    || strtotime(
                           '+ ' . $deactivationManualMovement . ' minutes',
                           $lastManualMovement['timeStamp']
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

                if ($this->ReadPropertyInteger(self::PROP_SLATSLEVELID) !== 0) {
                    if ($this->profileSlatsLevel['Reversed']) {
                        $positionsNew['SlatsLevel'] = min($positionsNew['SlatsLevel'], $positionsShadowingBySunPosition['SlatsLevel']);
                    } else {
                        $positionsNew['SlatsLevel'] = max($positionsNew['SlatsLevel'], $positionsShadowingBySunPosition['SlatsLevel']);
                    }
                }

                if ($positionsNew['BlindLevel'] === $positionsShadowingBySunPosition['BlindLevel']) {
                    if ($this->ReadPropertyInteger(self::PROP_BRIGHTNESSIDSHADOWINGBYSUNPOSITION) > 0) {
                        $Hinweis = 'Beschattung nach Sonnenstand,' . $this->GetFormattedValue(
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

                if ($this->ReadPropertyInteger(self::PROP_SLATSLEVELID) !== 0) {
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
                __FUNCTION__,
                sprintf(
                    'NOTFALL: Kontakt geöffnet (posActBlindLevel: %s, posNewBlindLevel: %s)',
                    $positionsAct['BlindLevel'],
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

            if ($this->ReadPropertyInteger(self::PROP_SLATSLEVELID) !== 0) {
                if ($this->profileSlatsLevel['Reversed']) {
                    if ($positionsContactOpenBlind['SlatsLevel'] > $positionsNew['SlatsLevel']) {
                        $positionsNew['SlatsLevel'] = $positionsContactOpenBlind['SlatsLevel'];
                        $Hinweis                    = 'Kontakt offen';
                    }
                } elseif ($positionsContactOpenBlind['SlatsLevel'] < $positionsNew['SlatsLevel']) {
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

            if ($this->ReadPropertyInteger(self::PROP_SLATSLEVELID) !== 0) {
                if ($this->profileSlatsLevel['Reversed']) {
                    if ($positionsContactCloseBlind['SlatsLevel'] < $positionsNew['SlatsLevel']) {
                        $positionsNew['SlatsLevel'] = $positionsContactCloseBlind['SlatsLevel'];
                        $Hinweis                    = 'Kontakt offen';
                    }
                } elseif ($positionsContactCloseBlind['SlatsLevel'] > $positionsNew['SlatsLevel']) {
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

            if ($this->ReadPropertyInteger(self::PROP_SLATSLEVELID) !== 0) {
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

    private function resetManualMovement(): void
    {
        $this->WriteAttributeString(
            self::ATTR_MANUALMOVEMENT,
            json_encode(['timeStamp' => null, 'blindLevel' => null, 'slatsLevel' => null])
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
        $this->RegisterPropertyInteger('WeeklyTimeTableEventID', 0);
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
        $this->RegisterPropertyInteger('TemperatureIDShadowingBySunPosition', 0);
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
        $this->RegisterPropertyInteger('ActivatorIDShadowingBrightness', 0);
        $this->RegisterPropertyInteger(self::PROP_BRIGHTNESSIDSHADOWINGBRIGHTNESS, 0);
        $this->RegisterPropertyInteger(self::PROP_BRIGHTNESSAVGMINUTESSHADOWINGBRIGHTNESS, 0);
        $this->RegisterPropertyInteger('ThresholdIDLessBrightness', 0);
        $this->RegisterPropertyFloat(self::PROP_BLINDLEVELLESSBRIGHTNESSSHADOWINGBRIGHTNESS, 0);
        $this->RegisterPropertyFloat(self::PROP_SLATSLEVELLESSBRIGHTNESSSHADOWINGBRIGHTNESS, 0);
        $this->RegisterPropertyInteger('ThresholdIDHighBrightness', 0);
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
        $this->RegisterPropertyInteger('DeactivationAutomaticMovement', 20);
        $this->RegisterPropertyInteger('DeactivationManualMovement', 120);
        $this->RegisterPropertyInteger(self::PROP_DELAYTIMEDAYNIGHTCHANGE, 0);
        $this->RegisterPropertyBoolean(self::PROP_DELAYTIMEDAYNIGHTCHANGEISRANDOMLY, false);
        $this->RegisterPropertyBoolean(self::PROP_SHOWNOTUSEDELEMENTS, false);

        $this->RegisterPropertyBoolean('WriteLogInformationToIPSLogger', false);
        $this->RegisterPropertyBoolean('WriteDebugInformationToLogfile', false);
        $this->RegisterPropertyBoolean('WriteDebugInformationToIPSLogger', false);
    }

    private function RegisterReferences(): void
    {
        $objectIDs = [
            $this->ReadPropertyInteger(self::PROP_BLINDLEVELID),
            $this->ReadPropertyInteger('WeeklyTimeTableEventID'),
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
            $this->ReadPropertyInteger('TemperatureIDShadowingBySunPosition'),

            $this->ReadPropertyInteger('ActivatorIDShadowingBrightness'),
            $this->ReadPropertyInteger(self::PROP_BRIGHTNESSIDSHADOWINGBRIGHTNESS),
            $this->ReadPropertyInteger('ThresholdIDLessBrightness'),
            $this->ReadPropertyInteger('ThresholdIDHighBrightness')
        ];

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
            'TemperatureIDShadowingBySunPosition'                  => $this->ReadPropertyInteger('TemperatureIDShadowingBySunPosition'),
            'ActivatorIDShadowingBrightness'                       => $this->ReadPropertyInteger('ActivatorIDShadowingBrightness'),
            self::PROP_BRIGHTNESSIDSHADOWINGBRIGHTNESS             => $this->ReadPropertyInteger(self::PROP_BRIGHTNESSIDSHADOWINGBRIGHTNESS),
            'ThresholdIDHighBrightness'                            => $this->ReadPropertyInteger('ThresholdIDHighBrightness'),
            'ThresholdIDLessBrightness'                            => $this->ReadPropertyInteger('ThresholdIDLessBrightness')
        ];

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
            self::ATTR_LASTMOVE . self::PROP_BLINDLEVELID,
            json_encode(['timeStamp' => null, 'percentClose' => null, 'hint' => null])
        );

        $this->RegisterAttributeString(
            self::ATTR_LASTMOVE . self::PROP_SLATSLEVELID,
            json_encode(['timeStamp' => null, 'percentClose' => null, 'hint' => null])
        );
        $this->RegisterAttributeInteger(self::ATTR_DAYTIME_CHANGE_TIME, 0);
        $this->RegisterAttributeBoolean(self::ATTR_LAST_ISDAYBYTIMESCHEDULE, false);

        $library       = json_decode(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'library.json'), true);
        $moduleVersion = sprintf('%s.%s', $library['version'], $library['build']);
        $this->RegisterAttributeString('Version', $moduleVersion);
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
            self::STATUS_INST_SLATSLEVEL_ID_IS_INVALID
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

        if ($ret = $this->checkVariableId(self::PROP_WAKEUPTIMEID, true, [VARIABLETYPE_STRING], self::STATUS_INST_WAKEUPTIME_ID_IS_INVALID)) {
            $this->SetStatus($ret);
            return;
        }

        if ($ret = $this->checkVariableId(self::PROP_BEDTIMEID, true, [VARIABLETYPE_STRING], self::STATUS_INST_SLEEPTIME_ID_IS_INVALID)) {
            $this->SetStatus($ret);
            return;
        }

        if ($ret =
            $this->checkVariableId(self::PROP_HOLIDAYINDICATORID, true, [VARIABLETYPE_BOOLEAN], self::STATUS_INST_HOLYDAY_INDICATOR_ID_IS_INVALID)) {
            $this->SetStatus($ret);
            return;
        }

        if ($ret = $this->checkVariableId(
            self::PROP_BRIGHTNESSID,
            true,
            [VARIABLETYPE_INTEGER, VARIABLETYPE_FLOAT],
            self::STATUS_INST_BRIGHTNESS_ID_IS_INVALID
        )) {
            $this->SetStatus($ret);
            return;
        }

        if ($ret = $this->checkVariableId(
            self::PROP_DAYSTARTID,
            true,
            [VARIABLETYPE_STRING],
            self::STATUS_INST_DAYSTART_ID_IS_INVALID
        )) {
            $this->SetStatus($ret);
            return;
        }

        if ($ret = $this->checkVariableId(
            self::PROP_DAYENDID,
            true,
            [VARIABLETYPE_STRING],
            self::STATUS_INST_DAYEND_ID_IS_INVALID
        )) {
            $this->SetStatus($ret);
            return;
        }

        if ($ret = $this->checkVariableId(
            self::PROP_BRIGHTNESSTHRESHOLDID,
            true,
            [VARIABLETYPE_INTEGER, VARIABLETYPE_FLOAT],
            self::STATUS_INST_BRIGHTNESS_THRESHOLD_ID_IS_INVALID
        )) {
            $this->SetStatus($ret);
            return;
        }

        if ($ret =
            $this->checkVariableId(self::PROP_ISDAYINDICATORID, true, [VARIABLETYPE_BOOLEAN], self::STATUS_INST_ISDAY_INDICATOR_ID_IS_INVALID)) {
            $this->SetStatus($ret);
            return;
        }

        if ($ret = $this->checkVariableId(
            self::PROP_CONTACTOPEN1ID,
            true,
            [VARIABLETYPE_BOOLEAN, VARIABLETYPE_INTEGER, VARIABLETYPE_FLOAT],
            self::STATUS_INST_CONTACT1_ID_IS_INVALID
        )) {
            $this->SetStatus($ret);
            return;
        }

        if ($ret = $this->checkVariableId(
            self::PROP_CONTACTOPEN2ID,
            true,
            [VARIABLETYPE_BOOLEAN, VARIABLETYPE_INTEGER, VARIABLETYPE_FLOAT],
            self::STATUS_INST_CONTACT2_ID_IS_INVALID
        )) {
            $this->SetStatus($ret);
            return;
        }

        if ($ret = $this->checkVariableId(
            self::PROP_EMERGENCYCONTACTID,
            true,
            [VARIABLETYPE_BOOLEAN, VARIABLETYPE_INTEGER, VARIABLETYPE_FLOAT],
            self::STATUS_INST_EMERGENCY_CONTACT_ID_IS_INVALID
        )) {
            $this->SetStatus($ret);
            return;
        }

        if ($ret = $this->checkVariableId(
            self::PROP_ACTIVATORIDSHADOWINGBYSUNPOSITION,
            true,
            [VARIABLETYPE_BOOLEAN, VARIABLETYPE_INTEGER, VARIABLETYPE_FLOAT],
            self::STATUS_INST_ACTIVATORIDSHADOWINGBYSUNPOSITION_IS_INVALID
        )) {
            $this->SetStatus($ret);
            return;
        }

        if ($ret = $this->checkVariableId(
            self::PROP_AZIMUTHID,
            $this->ReadPropertyInteger(self::PROP_ACTIVATORIDSHADOWINGBYSUNPOSITION) === 0,
            [VARIABLETYPE_FLOAT],
            self::STATUS_INST_AZIMUTHID_IS_INVALID
        )) {
            $this->SetStatus($ret);
            return;
        }

        if ($ret = $this->checkVariableId(
            self::PROP_ALTITUDEID,
            $this->ReadPropertyInteger(self::PROP_ACTIVATORIDSHADOWINGBYSUNPOSITION) === 0,
            [VARIABLETYPE_FLOAT],
            self::STATUS_INST_ALTITUDEID_IS_INVALID
        )) {
            $this->SetStatus($ret);
            return;
        }

        if ($ret = $this->checkVariableId(
            self::PROP_BRIGHTNESSIDSHADOWINGBYSUNPOSITION,
            true,
            [VARIABLETYPE_INTEGER, VARIABLETYPE_FLOAT],
            self::STATUS_INST_BRIGTHNESSIDSHADOWINGBYSUNPOSITION_IS_INVALID
        )) {
            $this->SetStatus($ret);
            return;
        }

        if ($ret = $this->checkVariableId(
            self::PROP_BRIGHTNESSTHRESHOLDIDSHADOWINGBYSUNPOSITION,
            true,
            [VARIABLETYPE_INTEGER, VARIABLETYPE_FLOAT],
            self::STATUS_INST_BRIGHTNESSTHRESHOLDIDSHADOWINGBYSUNPOSITION_IS_INVALID
        )) {
            $this->SetStatus($ret);
            return;
        }

        if ($ret = $this->checkVariableId(
            'TemperatureIDShadowingBySunPosition',
            true,
            [VARIABLETYPE_INTEGER, VARIABLETYPE_FLOAT],
            self::STATUS_INST_ROOMTEMPERATUREID_IS_INVALID
        )) {
            $this->SetStatus($ret);
            return;
        }

        if ($ret = $this->checkVariableId(
            'ActivatorIDShadowingBrightness',
            true,
            [VARIABLETYPE_BOOLEAN, VARIABLETYPE_INTEGER, VARIABLETYPE_FLOAT],
            self::STATUS_INST_ACTIVATORIDSHADOWINGBRIGHTNESS_IS_INVALID
        )) {
            $this->SetStatus($ret);
            return;
        }

        if ($ret = $this->checkVariableId(
            self::PROP_BRIGHTNESSIDSHADOWINGBRIGHTNESS,
            true,
            [VARIABLETYPE_INTEGER, VARIABLETYPE_FLOAT],
            self::STATUS_INST_BRIGHTNESSIDSHADOWINGBRIGHTNESS_IS_INVALID
        )) {
            $this->SetStatus($ret);
            return;
        }

        if ($ret = $this->checkVariableId(
            'ThresholdIDHighBrightness',
            true,
            [VARIABLETYPE_INTEGER, VARIABLETYPE_FLOAT],
            self::STATUS_INST_THRESHOLDIDHIGHBRIGHTNESS_IS_INVALID
        )) {
            $this->SetStatus($ret);
            return;
        }

        if ($ret = $this->checkVariableId(
            'ThresholdIDLessBrightness',
            true,
            [VARIABLETYPE_INTEGER, VARIABLETYPE_FLOAT],
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

        if ($this->ReadPropertyInteger(self::PROP_SLATSLEVELID) !== 0) {
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
            $this->SetTimerInterval(self::TIMER_UPDATE, $this->ReadPropertyInteger(self::PROP_UPDATEINTERVAL) * 60 * 1000);
        } else {
            $this->SetTimerInterval(self::TIMER_UPDATE, 0);
            $this->SetTimerInterval(self::TIMER_DELAYED_MOVEMENT, 0);
            $this->SetStatus(IS_INACTIVE);
            return;
        }

        $this->SetStatus(IS_ACTIVE);
    }

    private function checkVariableId(string $propName, bool $optional, array $variableTypes, int $errStatus): int
    {
        $variableID = $this->ReadPropertyInteger($propName);

        if (!$optional && $variableID === 0) {
            $this->Logger_Err(sprintf('\'%s\': ID nicht gesetzt: %s', $this->objectName, $propName));
            return $errStatus;
        }

        if ($variableID !== 0) {
            if (!$variable = @IPS_GetVariable($variableID)) {
                $this->Logger_Err(sprintf('\'%s\': falsche Variablen ID (#%s) für "%s"', $this->objectName, $variableID, $propName));
                return $errStatus;
            }

            if (!in_array($variable['VariableType'], $variableTypes, true)) {
                $this->Logger_Err(
                    sprintf('\'%s\': falscher Variablentyp für "%s" - nur %s erlaubt', $this->objectName, $propName, implode(', ', $variableTypes))
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
            $this->Logger_Err(sprintf('\'%s\': ID nicht gesetzt: %s', $this->objectName, $propName));
            return $errStatus;
        }

        if ($eventID !== 0) {
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

    private function getPositionsOfOpenBlindContact(): ?array
    {
        $contacts = [];

        if ($this->ReadPropertyInteger(self::PROP_CONTACTOPEN1ID) !== 0) {
            $contacts[self::PROP_CONTACTOPEN1ID] = [
                'id'         => $this->ReadPropertyInteger(self::PROP_CONTACTOPEN1ID),
                'blindlevel' => $this->ReadPropertyFloat(self::PROP_CONTACTOPENLEVEL1),
                'slatslevel' => $this->ReadPropertyFloat(self::PROP_CONTACTOPENSLATSLEVEL1)
            ];
        }
        if ($this->ReadPropertyInteger(self::PROP_CONTACTOPEN2ID) !== 0) {
            $contacts[self::PROP_CONTACTOPEN2ID] = [
                'id'         => $this->ReadPropertyInteger(self::PROP_CONTACTOPEN2ID),
                'blindlevel' => $this->ReadPropertyFloat(self::PROP_CONTACTOPENLEVEL2),
                'slatslevel' => $this->ReadPropertyFloat(self::PROP_CONTACTOPENSLATSLEVEL2)
            ];
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
                    if (isset($this->profileSlatsLevel) && $this->profileSlatsLevel['Reversed']) {
                        $blindPositions['SlatsLevel'] = max($blindPositions['SlatsLevel'], $contact['slatslevel']);
                    } else {
                        $blindPositions['SlatsLevel'] = min($blindPositions['SlatsLevel'], $contact['slatslevel']);
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
                'slatslevel' => $this->ReadPropertyFloat(self::PROP_CONTACTCLOSESLATSLEVEL1)
            ];
        }
        if ($this->ReadPropertyInteger(self::PROP_CONTACTCLOSE2ID) !== 0) {
            $contacts[self::PROP_CONTACTCLOSE2ID] = [
                'id'         => $this->ReadPropertyInteger(self::PROP_CONTACTCLOSE2ID),
                'blindlevel' => $this->ReadPropertyFloat(self::PROP_CONTACTCLOSELEVEL2),
                'slatslevel' => $this->ReadPropertyFloat(self::PROP_CONTACTCLOSESLATSLEVEL2)
            ];
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

        if ($contactOpen) {
            return $blindPositions;
        }

        return null;
    }

    private function isContactOpen(string $propName): bool
    {
        $contactId = $this->ReadPropertyInteger($propName);
        if ($contactId === 0) {
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

        if ($this->ReadPropertyInteger(self::PROP_EMERGENCYCONTACTID) !== 0) {
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

        if (($activatorID === 0) || !GetValue($activatorID)) {
            // keine Beschattung nach Sonnenstand gewünscht bzw. nicht notwendig
            return null;
        }

        $temperatureID = $this->ReadPropertyInteger('TemperatureIDShadowingBySunPosition');
        if ($temperatureID === 0) {
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
        if (isset($brightness)) {
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

        /** @noinspection ProperNullCoalescingOperatorUsageInspection */
        $this->Logger_Dbg(
            __FUNCTION__,
            sprintf(
                'active: %d, brightness(act/thresh): %.1f/%.1f, azimuth: %.1f (%.1f - %.1f), altitude: %.1f (%.1f - %.1f), temperature: %s',
                (int)GetValue($activatorID),
                $brightness,
                $thresholdBrightness,
                $rSunAzimuth,
                $azimuthFrom,
                $azimuthTo,
                $rSunAltitude,
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
        if ($brightnessID === 0) {
            return null;
        }
        $brightnessAvgMinutes = $this->ReadPropertyInteger($propBrightnessAvgMinutes);

        $brightness = (float)GetValue($brightnessID);

        if ($brightnessAvgMinutes > 0) {
            $archiveId = IPS_GetInstanceListByModuleID('{43192F0B-135B-4CE7-A0A7-1475603F3060}')[0];
            if (AC_GetLoggingStatus($archiveId, $brightnessID)) {
                $werte = @AC_GetAggregatedValues($archiveId, $brightnessID, 6, strtotime('-' . $brightnessAvgMinutes . ' minutes'), time(), 0);
                if ($werte === false || (count($werte) === 0)) {
                    //bei der Sommer auf Winterzeitumstellung gab es eine Warning'EndTime is before StartTime) um kurz vor 3
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
        $WindowsHeigth     = $this->ReadPropertyInteger(self::PROP_WINDOWSHEIGHT);
        $ParapetHeigth     = $this->ReadPropertyInteger(self::PROP_PARAPETHEIGHT);
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
        $x1 = cos(deg2rad(90 - $WindowsSlope)) * $WindowsHeigth;
        $x2 = sin(deg2rad(90 - $WindowsSlope)) * $WindowsHeigth;

        //Stützvektoren H (Heigth) und P (Parapet)
        $H_Window = [0, $ParapetHeigth + $x1, $x2];
        $P_Window = [0, $ParapetHeigth, 0];

        //-- Schattenpunkte H' und B' bestimmen (siehe https://www.youtube.com/watch?v=QvV-dFlH63c&t=87s)
        $H_Shadow = $this->Schattenpunkt_X0_X2_Ebene($H_Window, $V_Sun);
        $P_Shadow = $this->Schattenpunkt_X0_X2_Ebene($P_Window, $V_Sun);


        //-- Rollo-Position bestimmen (0 = open, 1 = closed)

        if ($DepthSunlight > $H_Shadow) {
            $degreeOfClosing = 0;
        } elseif ($DepthSunlight < $P_Shadow) {
            $degreeOfClosing = 1;
        } else {
            $additionalDepth = 0;
            if ($P_Shadow < 0) {
                $additionalDepth = abs($P_Shadow);
            }
            $degreeOfClosing = 1 - ($DepthSunlight + $additionalDepth) / ($H_Shadow - $P_Shadow);
        }

        $degreeOfClosing = max(min($degreeOfClosing, 1), 0);

        $this->Logger_Dbg(
            __FUNCTION__,
            sprintf(
                'WindowsOrientation: %s, WindowsSlope: %s, WindowsHeigth: %s, ParapetHeigth: %s, DepthSunLight: %s => H_Shadow: %.0f, P_Shadow: %.0f, degreeOfClosing (100%%=closed): %.0f%%',
                $WindowOrientation,
                $WindowsSlope,
                $WindowsHeigth,
                $ParapetHeigth,
                $DepthSunlight,
                $H_Shadow,
                $P_Shadow,
                $degreeOfClosing * 100
            )
        );


        $blindPositions = null;


        $blindLevelMin = $this->ReadPropertyFloat(self::PROP_MINIMUMSHADERELEVANTBLINDLEVEL);
        $blindLevelHalf =  $this->ReadPropertyFloat(self::PROP_HALFSHADERELEVANTBLINDLEVEL);
        $blindLevelMax = $this->ReadPropertyFloat(self::PROP_MAXIMUMSHADERELEVANTBLINDLEVEL);

        if ($blindLevelHalf === 0.0){
            //Funktion 1.Grades mit f(x) = a * x + b
            $b = $blindLevelMin;
            $a = ($blindLevelMax-$blindLevelMin);
            $blindPositions['BlindLevel'] = $a * $degreeOfClosing + $b;

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
            $c = $blindLevelMin;
            $b = 4 * $blindLevelHalf - $blindLevelMax - 3 * $blindLevelMin;
            $a = $blindLevelMax - $blindLevelMin - $b;
            $blindPositions['BlindLevel'] = $a * $degreeOfClosing**2 + $b * $degreeOfClosing + $c;

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

        $blindPositions['SlatsLevel'] = ($slatsLevelMax - $slatsLevelMin) * $degreeOfClosing + $slatsLevelMin;;

        return $blindPositions;
    }

    private function Schattenpunkt_X0_X2_Ebene(array $Stuetzvektor, array $Vektor): float
    {
        $r = -$Stuetzvektor[1] / $Vektor[1];
        return $r * $Vektor[2];
    }

    private function getPositionsOfShadowingByBrightness(float $levelAct): ?array
    {
        $activatorID = $this->ReadPropertyInteger('ActivatorIDShadowingBrightness');

        if (($activatorID === 0) || !GetValue($activatorID)) {
            // keine Beschattung bei Helligkeit gewünscht bzw. nicht notwendig
            return null;
        }

        $brightnessID = $this->ReadPropertyInteger(self::PROP_BRIGHTNESSIDSHADOWINGBRIGHTNESS);
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
        $brightness =
            $this->GetBrightness(self::PROP_BRIGHTNESSIDSHADOWINGBRIGHTNESS, self::PROP_BRIGHTNESSAVGMINUTESSHADOWINGBRIGHTNESS, $levelAct, true);

        if (!isset($brightness)) {
            return null;
        }

        if ($thresholdIDHighBrightness > 0) {
            $thresholdLessBrightness = GetValue($thresholdIDHighBrightness);
            if ($brightness > $thresholdLessBrightness) {
                $positions['BlindLevel'] = $this->ReadPropertyFloat(self::PROP_BLINDLEVELHIGHBRIGHTNESSSHADOWINGBRIGHTNESS);
                $positions['SlatsLevel'] = $this->ReadPropertyFloat(self::PROP_SLATSLEVELHIGHBRIGHTNESSSHADOWINGBRIGHTNESS);
                $this->Logger_Dbg(
                    __FUNCTION__,
                    sprintf(
                        'Beschattung bei hoher Helligkeit (%s/%s): BlindLevel: %s, SlatsLevel: %s',
                        $brightness,
                        $thresholdLessBrightness,
                        $positions['BlindLevel'],
                        $positions['SlatsLevel']
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
                    __FUNCTION__,
                    sprintf(
                        'Beschattung bei niedriger Helligkeit (%s/%s): BlindLevel: %s, SlatsLevel: %s',
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

        $deactivationTimeManuSecs = $this->ReadPropertyInteger('DeactivationManualMovement') * 60;

        //Zeitpunkt festhalten, sofern noch nicht geschehen
        if ($tsBlindLastMovement !== json_decode($this->ReadAttributeString(self::ATTR_MANUALMOVEMENT), true)['timeStamp']) {
            $this->WriteAttributeString(
                self::ATTR_MANUALMOVEMENT,
                json_encode(
                    ['timeStamp' => $tsBlindLastMovement, 'blindLevel' => $blindLevelAct, 'slatsLevel' => $slatsLevelAct]
                )
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
                    $this->FormatTimeStamp(json_decode($this->ReadAttributeString(self::ATTR_MANUALMOVEMENT), true)['timeStamp']),
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

        $moveBladeOk = $this->MoveToPosition(self::PROP_BLINDLEVELID, $percentBlindClose, $deactivationTimeAuto, $hint);

        //gibt es Lamellen?
        if ($this->ReadPropertyInteger(self::PROP_SLATSLEVELID) !== 0) {
            $this->profileSlatsLevel = $this->GetProfileInformation(self::PROP_SLATSLEVELID);
            $moveSlatsOk             = $this->MoveToPosition(self::PROP_SLATSLEVELID, $percentSlatsClosed, $deactivationTimeAuto, $hint);

            return $moveBladeOk || $moveSlatsOk;
        }

        return $moveBladeOk;
    }

    private function MoveToPosition(string $propName, int $percentClose, int $deactivationTimeAuto, string $hint): bool
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

        if (((int)$lastMove['percentClose'] === $percentClose) && ($lastMove['timeStamp'] > strtotime('-40 secs'))) {
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
        $timeDiffAuto           = time() - $this->ReadAttributeInteger(self::ATTR_TIMESTAMP_AUTOMATIC);

        $this->Logger_Dbg(
            __FUNCTION__,
            sprintf(
                '#%s(%s): positionAct: %s, positionNew: %s, positionDiffPercentage: %.3f/0,05, timeDiffAuto: %s/%s',
                $positionID,
                $propName,
                $positionAct,
                $positionNew,
                $positionDiffPercentage,
                $timeDiffAuto,
                $deactivationTimeAuto
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
                    self::ATTR_LASTMOVE . $propName,
                    json_encode(['timeStamp' => time(), 'percentClose' => $percentClose, 'hint' => $hint])
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
                        '\'%s\': #%s (%s): Fehler beim Setzen des Wertes. (Value = %s, Parent: "%s").',
                        $this->objectName,
                        $positionID,
                        $propName,
                        $percentClose,
                        IPS_GetName(IPS_GetParent($positionID))
                    )
                );
            }
            $this->Logger_Dbg(__FUNCTION__, sprintf('#%s(%s): %s to %s', $positionID, $propName, $positionAct, $positionNew));
        } elseif ($positionDiffPercentage < 0.01) {
            $this->Logger_Dbg(__FUNCTION__, sprintf('#%s(%s): No Movement! Position %s already reached.', $positionID, $propName, $positionAct));
        } elseif ($positionDiffPercentage < 0.05) {
            $this->Logger_Dbg(
                __FUNCTION__,
                sprintf('#%s(%s): No Movement! Movement less than 5 percent (%.2f).', $positionID, $propName, $positionDiffPercentage)
            );
        } else {
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
            $currentValue        = GetValue($levelID);
            $percentCloseCurrent = ($currentValue - $profile['MinValue']) / ($profile['MaxValue'] - $profile['MinValue']) * 100;

            if ($profile['Reversed']) {
                $percentCloseCurrent = 100 - $percentCloseCurrent;
            }

            if (abs($percentCloseNew - $percentCloseCurrent) > 5) {
                if ((float)IPS_GetKernelVersion() < 5.6) {
                    set_time_limit(30);
                }
                sleep(1);
            } else {
                $this->Logger_Dbg(
                    __FUNCTION__,
                    sprintf(
                        '#%s(%s): Position reached (Value: %s, Diff: %.2f).',
                        $levelID,
                        $propName,
                        $currentValue,
                        $percentCloseNew - $percentCloseCurrent
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
        $eventScheduleGroups = IPS_GetEvent($this->ReadPropertyInteger('WeeklyTimeTableEventID'))['ScheduleGroups'];

        foreach ($eventScheduleGroups as $scheduleGroup) {
            $countID1 = $this->CountNumberOfPointsWithActionId($scheduleGroup['Points'], 1); //down
            $countID2 = $this->CountNumberOfPointsWithActionId($scheduleGroup['Points'], 2); //up

            if (($countID1 + $countID2) === 0) {
                $this->Logger_Dbg(
                    __FUNCTION__,
                    sprintf(
                        'Invalid TimeTable: No Points with ActionID 1 or 2 found. (ScheduleGroup: %s)',
                        json_encode($scheduleGroup)
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

        $isDayIndicatorID = $this->ReadPropertyInteger(self::PROP_ISDAYINDICATORID);

        if (($isDayIndicatorID === 0)
            && (($this->ReadPropertyInteger(self::PROP_BRIGHTNESSID) === 0) || $this->ReadPropertyInteger(self::PROP_BRIGHTNESSTHRESHOLDID) === 0)) {
            return null;
        }

        if ($isDayIndicatorID > 0) {
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

        if ($this->ReadPropertyInteger(self::PROP_DAYSTARTID) > 0) {
            $dayStart    = GetValueString($this->ReadPropertyInteger(self::PROP_DAYSTARTID));
            $dayStart_ts = strtotime($dayStart);
            if ($dayStart_ts === false) {
                $this->Logger_Dbg(__FUNCTION__, sprintf('No valid DayStart found: \'%s\' (ignored)', $dayStart));
            } else {
                $this->Logger_Dbg(__FUNCTION__, sprintf('DayStart found: %s', $dayStart));
            }
        }

        if ($this->ReadPropertyInteger(self::PROP_DAYENDID) > 0) {
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
        if (($holidayIndicatorID !== 0) && ($this->ReadPropertyInteger(self::PROP_DAYUSEDWHENHOLIDAY) !== 0)
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
        if ($idWakeUpTime > 0) {
            $heute_auf_ts = strtotime(GetValueString($idWakeUpTime)) + $this->ReadPropertyInteger(self::PROP_WAKEUPTIMEOFFSET) * 60;
            if ($heute_auf_ts === false) {
                $this->Logger_Dbg(__FUNCTION__, sprintf('No valid WakeUpTime found: \'%s\' (ignored)', GetValueString($idWakeUpTime)));
            } else {
                // es wurde eine gültige Zeit gefunden
                $heute_auf = date('H:i', $heute_auf_ts);
                $this->Logger_Dbg(__FUNCTION__, sprintf('WakeUpTime found: %s', $heute_auf));
            }
        }

        $idBedTime = $this->ReadPropertyInteger(self::PROP_BEDTIMEID);
        if ($idBedTime > 0) {
            $heute_ab_ts = strtotime(GetValueString($idBedTime)) + $this->ReadPropertyInteger(self::PROP_BEDTIMEOFFSET) * 60;
            if ($heute_ab_ts === false) {
                $this->Logger_Dbg(__FUNCTION__, sprintf('No valid BedTime found: \'%s\' (ignored)', GetValueString($idBedTime)));
            } else {
                // es wurde eine gültige Zeit gefunden
                $heute_ab = date('H:i', $heute_ab_ts);
                $this->Logger_Dbg(__FUNCTION__, sprintf('BedTime: %s', $heute_ab));
            }
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
        $this->SendDebug(__FUNCTION__, sprintf('event: %s', json_encode($event)), 0);
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

    private function ShowNotUsedElements(bool $bShow): void
    {
        $this->Logger_Dbg(
            __FUNCTION__,
            sprintf(
                'bShow: %s, PROP_HOLIDAYINDICATORID: %s',
                (int)$bShow,
                $this->ReadPropertyInteger(self::PROP_HOLIDAYINDICATORID)
            )
        );
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
            $ret = (string)$val;
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
            IPSLogger_Dbg(__CLASS__ . '.' . $this->objectName . '.' . $message, $data);
        }
        if ($this->ReadPropertyBoolean('WriteDebugInformationToLogfile')) {
            $this->LogMessage(sprintf('%s: %s', $message, $data), KL_DEBUG);
        }
    }
}
