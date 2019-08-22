<?php

declare(strict_types=1);
/** @noinspection AutoloadingIssuesInspection */
class BlindControlGroupMaster extends IPSModule
{
    /** @noinspection PhpUnused */
    public function Create()
    {
        //Never delete this line!
        parent::Create();

        //These lines are parsed on Symcon Startup or Instance creation
        //You cannot use variables here. Just static values.

        $this->RegisterPropertyString('Blinds', '');

    }

    /** @noinspection PhpUnused */
    public function ApplyChanges()
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
    public function GetConfigurationForm()
    {
        $allBlindInstances = IPS_GetInstanceListByModuleID('{538F6461-5410-4F4C-91D3-B39122152D56}');
        if (empty($allBlindInstances)) {
            $form['elements'][] = [
                'type'    => 'Label',
                'caption' => 'The group master can only be used if at least one blind has been created. Please create a blind.'];
            return json_encode($form);
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
        foreach (json_decode(IPS_GetConfiguration($defaultInstance), true) as $property => $propertyValue) {
            $properties[] = ['value' => $property, 'caption' => $property];
        }

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

        $form['elements'][] = [
            'type'     => 'List',
            'name'     => 'Blinds',
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
                ['caption' => 'ObjectID', 'name' => 'ObjectID', 'width' => '60px', 'add' => ''],
                ['caption' => 'Location', 'name' => 'Location', 'width' => '400px', 'add' => ''],
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

        return json_encode($form);
    }

    private function GetListValues(): array
    {
        $listValues = [];
        if ($this->ReadPropertyString('Blinds') !== '') {
            //Annotate existing elements
            $shutters = json_decode($this->ReadPropertyString('Blinds'), true);
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
        $blinds = json_decode($this->ReadPropertyString('Blinds'), true);
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
    public function SetPropertyOfBlinds(string $Property, $Value): bool
    {
        $this->LogMessage(__CLASS__ . '::' . __FUNCTION__ . ': ' . $this->InstanceID . ' ' . $Property . ' ' . $Value, KL_DEBUG);

        $shutters = $this->GetBlinds();

        if (!empty($shutters)) {
            $conf = json_decode(IPS_GetConfiguration($shutters[0]['instanceID']), true);
            if (!array_key_exists($Property, $conf)) {
                return false;
            }
        } else {
            return false;
        }

        foreach ($shutters as $shutter) {
            $ID        = $shutter['instanceID'];
            $old_value = IPS_GetProperty($ID, $Property);

            $this->LogMessage(
                sprintf('%s: Blind #%s, oldValue: %s, newValue: %s', __CLASS__ . '::' . __FUNCTION__, $ID, $old_value, $Value), KL_DEBUG
            );

            try {
                if (!settype($Value, gettype(IPS_GetProperty($ID, $Property)))) {
                    return false;
                }
                if (IPS_SetProperty($ID, $Property, $Value) && !IPS_ApplyChanges($ID)) {
                    return false;
                }

            } catch (Exception $e) {
                return false;
            }
        }
        return true;
    }

    /** @noinspection PhpUnused */
    public function GetPropertyOfBlinds(string $Property): ?array
    {
        $this->LogMessage(__CLASS__ . '::' . __FUNCTION__ . ': ' . $this->InstanceID . ' ' . $Property, KL_DEBUG);

        $shutters = $this->GetBlinds();

        if (!empty($shutters)) {
            $conf = json_decode(IPS_GetConfiguration($shutters[0]['instanceID']), true);
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
        $this->LogMessage(__CLASS__ . '::' . __FUNCTION__ . ': ' . $this->InstanceID . ' Active' . (int) $active, KL_DEBUG);

        $shutters = $this->GetBlinds();

        foreach ($shutters as $shutter) {
            $ID = $shutter['instanceID'];
            $this->LogMessage(__CLASS__ . '::' . __FUNCTION__ . ': Shutter: ' . $ID, KL_DEBUG);
            IPS_RequestAction($ID, 'ACTIVATED', $active);
        }
    }
}
