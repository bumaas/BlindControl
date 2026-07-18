<?php

declare(strict_types=1);
/** @noinspection AutoloadingIssuesInspection */
class BlindControlGroupMaster extends IPSModuleStrict
{
    private const string PROP_BLINDS = 'Blinds';

    /** @noinspection PhpUnused */
    public function Create(): void
    {
        //Never delete this line!
        parent::Create();

        //These lines are parsed on Symcon Startup or Instance creation
        //You cannot use variables here. Just static values.

        $this->RegisterPropertyString(self::PROP_BLINDS, '');
        $this->RegisterPropertyBoolean('WriteDebugInformationToLogfile', false);

    }

    /** @noinspection PhpUnused */
    public function ApplyChanges(): void
    {
        //Never delete this line!
        parent::ApplyChanges();

        $this->RegisterReferences();
    }

    private function RegisterReferences(): void
    {
        $objectIDs = [];
        foreach ($this->GetBlinds() as $blind) {
            $objectIDs[] = $blind['instanceID'];
        }

        foreach ($this->GetReferenceList() as $ref) {
            $this->UnregisterReference($ref);
        }

        foreach ($objectIDs as $id) {
            if ($id !== 0) {
                $this->RegisterReference($id);
            }
        }
    }

    /** @noinspection PhpUnused */
    public function GetConfigurationForm(): string
    {
        $allBlindInstances = IPS_GetInstanceListByModuleID('{538F6461-5410-4F4C-91D3-B39122152D56}');
        if (empty($allBlindInstances)) {
            $form['elements'][] = [
                'type'    => 'Label',
                'caption' => 'The group master can only be used if at least one blind has been created. Please create a blind.'];
            return json_encode($form, JSON_THROW_ON_ERROR);
        }

        $options = [];
        foreach ($allBlindInstances as $blindId) {
            $options[] = ['caption' => IPS_GetName($blindId), 'value' => $blindId];
        }

        //sort
        $caption = array_column($options, 'caption');
        array_multisort($caption, SORT_ASC, $options);

        $defaultInstance = $options[0]['value'];

        $properties[] = ['value' => -1, 'caption' => '- please select -'];
        foreach (json_decode(IPS_GetConfiguration($defaultInstance), true, 512, JSON_THROW_ON_ERROR) as $property => $propertyValue) {
            $properties[] = ['value' => $property, 'caption' => $property];
        }

        if (IPS_GetKernelVersion() < '5.20') {
            $form['elements'][] = [
                'type'  => 'RowLayout',
                'items' => [
                    [
                        'type'    => 'Label',
                        'caption' => 'In this instance, the parameters for a group of blinds can be read and changed. The description of the individual parameters can be found in the documentation.'],
                    [
                        'type'    => 'Button',
                        'caption' => 'Show Documentation',
                        'onClick' => "echo 'https://github.com/bumaas/BlindControl/blob/master/README.md';",
                        'link'    => true]]];
        } else {
            $form['elements'][] = [
                'type'    => 'Label',
                'caption' => 'In this instance, the parameters for a group of blinds can be read and changed.'];
        }

        $form['elements'][] = [
            'type'     => 'List',
            'name'     => self::PROP_BLINDS,
            'caption'  => 'Blinds',
            'rowCount' => '15',
            'add'      => true,
            'delete'   => true,
            'sort'     => ['column' => 'InstanceID', 'direction' => 'ascending'],
            'columns'  => [
                [
                    'caption' => 'Name',
                    'name'    => 'InstanceID',
                    'width'   => '250px',
                    'add'     => $defaultInstance,
                    'edit'    => ['type' => 'Select', 'options' => $options]],
                ['caption' => 'ObjectID', 'name' => 'ObjectID', 'width' => '70px', 'add' => ''],
                ['caption' => 'Location', 'name' => 'Location', 'width' => '450px', 'add' => ''],
                [
                    'caption' => 'selected',
                    'name'    => 'Selected',
                    'width'   => 'auto',
                    'add'     => true,
                    'edit'    => ['type' => 'CheckBox', 'caption' => 'selected']]],
            'values'   => $this->GetListValues()];

        $form['actions'] = [
            ['type' => 'Label', 'caption' => 'The following function can be used to determine a property of the selected blinds.'],
            [
                'type'  => 'RowLayout',
                'items' => [
                    ['type' => 'Select', 'name' => 'Property1', 'caption' => 'Property', 'options' => $properties],
                    [
                        'type'    => 'Button',
                        'caption' => 'GetProperty',
                        'onClick' => '
                        $prop = BLCGM_GetPropertyOfBlinds($id, $Property1);
                        if ($prop === null){
                            $module = new IPSModule($id); 
                            echo $module->Translate("Please select a property");
                        } elseif (empty($prop)) {
                            $module = new IPSModule($id); 
                            echo $module->Translate("Please select a shutter");
                        } else {
                            echo $Property1 . ": " . PHP_EOL . PHP_EOL;
                            print_r($prop);
                        }        
                        ']]],
            ['type' => 'Label', 'caption' => 'The following function can be used to set a property of the selected blinds to a specified value.'],
            [
                'type'  => 'RowLayout',
                'items' => [
                    ['type' => 'Select', 'name' => 'Property2', 'caption' => 'Property', 'options' => $properties],
                    ['type' => 'ValidationTextBox', 'name' => 'Value', 'caption' => 'Value'],
                    [
                        'type'    => 'Button',
                        'caption' => 'SetProperty',
                        'onClick' => '
                        $ret = BLCGM_SetPropertyOfBlinds($id, $Property2, $Value);
                        $module = new IPSModule($id); 
                        if ($ret) {
                            echo $module->Translate("Finished!");
                        } else {
                            echo $module->Translate("Function failed! Please check property and value.");
                        }        
                     ']]]];

        return json_encode($form, JSON_THROW_ON_ERROR);
    }

    private function GetListValues(): array
    {
        $listValues = [];
        if ($this->ReadPropertyString(self::PROP_BLINDS) !== '') {
            //Annotate existing elements
            $shutters = json_decode($this->ReadPropertyString(self::PROP_BLINDS), true, 512, JSON_THROW_ON_ERROR);
            foreach ($shutters as $shutter) {
                //We only need to add annotations. Remaining data is merged from persistance automatically.
                //Order is determinted by the order of array elements
                if (IPS_InstanceExists($shutter['InstanceID'])) {
                    $listValues[] = [
                        'InstanceID' => $shutter['InstanceID'],
                        'ObjectID'   => $shutter['InstanceID'],
                        'Location'   => IPS_GetLocation($shutter['InstanceID']),
                        'Selected'   => $shutter['Selected']];
                } else {
                    $listValues[] = [
                        'InstanceID' => $shutter['InstanceID'],
                        'ObjectID'   => $shutter['InstanceID'],
                        'Location'   => 'Not found!',
                        'Selected'   => $shutter['Selected']];
                }
            }
        }

        return $listValues;
    }

    public function GetBlinds(): array
    {
        $arr    = [];
        $blindsJson = $this->ReadPropertyString(self::PROP_BLINDS);
        if ($blindsJson === '') {
            return [];
        }
        $blinds = json_decode($blindsJson, true, 512, JSON_THROW_ON_ERROR);
        if (empty($blinds)) {
            return [];
        }

        foreach ($blinds as $blind) {
            if ($blind['Selected']) {
                if (IPS_InstanceExists($blind['InstanceID'])) {
                    $arr[] = [
                        'instanceID' => $blind['InstanceID'],
                        'Location'   => IPS_GetName($blind['InstanceID'])];
                } else {
                    $arr[] = [
                        'instanceID' => $blind['InstanceID'],
                        'Location'   => 'Not found!'];
                }
            }
        }
        return $arr;
    }

    /** @noinspection PhpUnused */
    public function SetPropertyOfBlinds(string $Property, mixed $Value): bool
    {
        $this->Logger_Dbg(__FUNCTION__, sprintf('%s %s %s', $this->InstanceID, $Property, $Value));

        $shutters = $this->GetBlinds();

        if (!empty($shutters)) {
            $conf = json_decode(IPS_GetConfiguration($shutters[0]['instanceID']), true, 512, JSON_THROW_ON_ERROR);
            if (!array_key_exists($Property, $conf)) {
                return false;
            }
        } else {
            return false;
        }

        foreach ($shutters as $shutter) {
            $ID        = $shutter['instanceID'];
            $old_value = IPS_GetProperty($ID, $Property);

            $this->Logger_Dbg(__FUNCTION__, sprintf('Blind #%s, oldValue: %s, newValue: %s', $ID, $old_value, $Value));

            try {
                if (gettype($old_value) === 'boolean' && is_string($Value)) {
                    //settype würde jeden nicht-leeren String (auch "false") zu true machen
                    $Value = filter_var($Value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
                    if ($Value === null) {
                        return false;
                    }
                } elseif (!settype($Value, gettype($old_value))) {
                    return false;
                }
                if (!IPS_SetProperty($ID, $Property, $Value) || !IPS_ApplyChanges($ID)) {
                    return false;
                }

            } catch (Exception) {
                return false;
            }
        }
        return true;
    }

    /** @noinspection PhpUnused */
    public function GetPropertyOfBlinds(string $Property): ?array
    {
        $this->Logger_Dbg(__FUNCTION__, sprintf('%s %s', $this->InstanceID, $Property));

        $shutters = $this->GetBlinds();

        if (!empty($shutters)) {
            $conf = json_decode(IPS_GetConfiguration($shutters[0]['instanceID']), true, 512, JSON_THROW_ON_ERROR);
            if (!array_key_exists($Property, $conf)) {
                return null;
            }
        }

        $prop = [];
        foreach ($shutters as $shutter) {
            $ID        = $shutter['instanceID'];
            $prop[$ID] = @IPS_GetProperty($ID, $Property);
        }
        return $prop;
    }

    /** @noinspection PhpUnused */
    public function SetBlindsActive(bool $active): void
    {
        $this->Logger_Dbg(__FUNCTION__, sprintf('%s Active%s', $this->InstanceID, (int) $active));

        $shutters = $this->GetBlinds();

        foreach ($shutters as $shutter) {
            $ID = $shutter['instanceID'];
            $this->Logger_Dbg(__FUNCTION__, 'Shutter: ' . $ID);
            IPS_RequestAction($ID, 'ACTIVATED', $active);
        }
    }

    private function Logger_Err(string $message): void
    {
        $this->SendDebug('LOG_ERR', $message, 0);
        $this->LogMessage($message, KL_ERROR);
    }

    private function Logger_Inf(string $message): void
    {
        $this->SendDebug('LOG_INFO', $message, 0);
        $this->LogMessage($message, KL_NOTIFY);
    }

    private function Logger_Dbg(string $message, string $data): void
    {
        $this->SendDebug($message, $data, 0);
        if ($this->ReadPropertyBoolean('WriteDebugInformationToLogfile')) {
            $this->LogMessage(sprintf('%s: %s', $message, $data), KL_DEBUG);
        }
    }
}
