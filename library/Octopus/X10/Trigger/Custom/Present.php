<?php

/**
 * Custom Present Trigger
 * 
 * @package X10
 * @author milan
 */
class Octopus_X10_Trigger_Custom_Present implements Octopus_X10_Trigger_Interface {

    /**
     * Execute on Event
     *
     * @param array $event
     * @param Application_Model_Trigger $triggerObject
     */
    public function execute($event,$triggerObject){
        $address = $triggerObject->settings_data['address'];
        $cameraAddress = $triggerObject->settings_data['camera'];
        
        $eventAddress = $event['house'] . $event['device'];
        if($eventAddress != $address || ($event['type'] != 'On' && $event['type'] != 'Off') ||
            $event['direction'] != 'Tx' || $event['medium'] != 'PL'){
            return false;
        }
        $present = ($event['type'] == 'On')? true: false;

        echo "Running trigger $triggerObject->name\n";
        //save flag to db
        Application_Model_VariableMapper::getInstance()->set('present',(string)$present);

        $commander = Octopus_X10_Command::getInstance(Zend_Registry::get('config')->toArray());
        
        if($present){
            //if present turn off camera
            $commander->execute('off', $cameraAddress);
        }
        else{
            //if not present turn on camera
            $commander->execute('on', $cameraAddress);
        }
    }
}
