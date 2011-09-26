<?php

/**
 * Custom Present Trigger
 * 
 * @package X10
 * @author milan
 */
class Octopus_X10_Trigger_Custom_Sensor implements Octopus_X10_Trigger_Interface {

    /**
     * Execute on Event
     *
     * @param array $event
     * @param Application_Model_Trigger $triggerObject
     */
    public function execute($event,$triggerObject){
        $address = $triggerObject->settings_data['address'];
        $lampAddress = $triggerObject->settings_data['lamp'];

        $eventAddress = $event['house'] . $event['device'];
        if($eventAddress != $address || $event['type'] != 'On' ||
            $event['direction'] != 'Tx' || $event['medium'] != 'PL'){
            return false;
        }
        $present = Application_Model_VariableMapper::getInstance()->get('present');
        if(!isset ($present)){
            $present = false;
        }
        else{
            $present = (bool)$present;
        }
        //do nothing if present
        if($present){
            return false;
        }

        $now = time();
        $lastExecuted = isset ($triggerObject->status_data['executed'])?$triggerObject->status_data['executed']:0;
        //do nothing in next 5 minutes
        if($now < ($lastExecuted + 5 * 60)){
            return false;
        }
        $triggerObject->status_data['executed'] = $now;
        Application_Model_TriggerMapper::getInstance()->save($triggerObject);

        $config = Zend_Registry::get('config')->toArray();

        echo "Running trigger $triggerObject->name\n";
        //turn on lamp to see them better
        $commander = Octopus_X10_Command::getInstance($config);
        $commander->execute('on', $lampAddress);
        //wait 2 seconds to make a shoot
        sleep(2);
        $img1 = file_get_contents($config['camera']['url']);
        sleep(1);
        $img2 = file_get_contents($config['camera']['url']);
        //send email
        $smtpConnection = new Zend_Mail_Transport_Smtp($config['mail']['smtp']['server'],
                $config['mail']['smtp']['params']);
        $mail = new Zend_Mail();
        $at1 = $mail->createAttachment($img1);
        $at1->type        = 'image/jpg';
        $at1->filename    = 'shoot1.jpg';
        $at2 = $mail->createAttachment($img2);
        $at2->type        = 'image/jpg';
        $at2->filename    = 'shoot2.jpg';

        $mail->setBodyText('Octopus Notification sensor detection')
            ->setFrom('no-reply@octopus.rukavina.dyndns.org', 'Octopus')
            ->addTo('rukavinamilan@gmail.com', 'Milan Rukavina')
            ->setSubject('Octopus Notification sensor detection');
         //Send
         try {
            $mail->send($smtpConnection);
            echo "trigger $triggerObject->name email sent! \n";
         }
         catch (Exception $e) {
            echo "Error sending email: " . $e->getMessage() . "\n";
         }
    }
}
