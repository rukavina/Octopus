<?php

/**
 * X10 Event Subscriber Service
 * reading from RabbitMQ exchange x10.event
 *
 * @author milan
 * @package X10
 * @subpackage Event
 */
class Octopus_X10_Event_Subscriber_Service {

    /**
     *
     * @var Octopus_X10_Event_Subscriber_Service
     */
    protected static $_instance = null;

    /**
     *
     * @var array
     */
    protected static $config = array();


    /**
     *
     * @var AMQPChannel
     */
    protected static $amqChannel = null;

    /**
     *
     * @var $amqConn
     */
    protected static $amqConn = null;

    public function stop(){
        //$this->amqConn->stop();
    }


    /**
     * constructor
     *
     * @param array $config
     */
    private function __construct($config) {
        require_once('amqplib/amqp.inc');
        $this->config = $config;
    }

    /**
     * returns singleton instance of Octopus_X10_Event_Subscriber_Service
     *
     * @param array $config
     * @return Octopus_X10_Event_Subscriber_Service
     */
    public static function getInstance($config = null)
    {
        if(self::$_instance === null)
        {
            self::$_instance = new self($config);
        }
        return self::$_instance;
    }

    /**
     * destructor
     */
    public function __destruct() {
        if($this->amqChannel){
            echo "Closing AMQ Channel...";
            $this->amqChannel->close();
            echo "OK.\n\n";
        }
        if($this->amqConn){
            echo "Closing AMQ Connection...";
            $this->amqConn->close();
            echo "OK.\n\n";
        }
    }

    /**
     * start service
     */
    public function run() {
        echo "X10.Event.Subscriber is starting...\n================\n";
        // Create Rabbit MQ channel
        $this->amqConn = new AMQPConnection($this->config['amqp']['host'], $this->config['amqp']['port'], $this->config['amqp']['user'], $this->config['amqp']['pass']);
        $this->amqChannel = $this->amqConn->channel();
        $this->amqChannel->access_request($this->config['amqp']['vhost'], false, false, true, true);

        $this->amqChannel->queue_declare($this->config['amqp']['queue']);
        $this->amqChannel->exchange_declare($this->config['amqp']['exchange'], 'direct', false, false, false);
        $this->amqChannel->queue_bind($this->config['amqp']['queue'], $this->config['amqp']['exchange']);

        $this->amqChannel->basic_consume($this->config['amqp']['queue'],
                $this->config['amqp']['tag'], false, false, false, false,
                'Octopus_X10_Event_Subscriber_Service::processMessage');

        // Loop as long as the channel has callbacks registered
        while(count($this->amqChannel->callbacks)) {
            $this->amqChannel->wait();
        }

    }


    /**
     * Process AMQ Message
     *
     * @param stdClass $message
     */
    public static function processMessage($message){
        $msgObject = json_decode($message->body, true);
        echo "\n--------\n";
        print_r($msgObject);
        echo "\n--------\n";

        $subscriber = self::$_instance;

        $subscriber->amqChannel->basic_ack($message->delivery_info['delivery_tag']);

        // Cancel callback
        if ($message->body === 'quit') {
            $subscriber->amqChannel->basic_cancel($subscriber->config['amqp']['tag']);
        }

        //update divace statuses in DB
        $subscriber->updateStatus($msgObject);
        //update lamp intesity
        $subscriber->processLamp($msgObject);
        //process triggers
        $subscriber->runTriggers($msgObject);
    }

    protected function updateStatus($eventObject){
        if(isset ($eventObject['status']['on'])){
            $model = Application_Model_DeviceMapper::getInstance();
            foreach ($eventObject['status']['on'] as $address => $value) {
                echo "Updating $address with status: $value \n";
                $deviceObj = $model->getDeviceByAddress('X10', $address);
                if($deviceObj){
                    $deviceObj->status_data['on'] = $value;
                    $model->updateDevice($deviceObj);
                }
                else{
                    echo "Device $address not found \n";
                }
            }
        }
    }

    protected function processLamp($eventObject){
        if(($eventObject['type'] == 'Bright' || $eventObject['type'] == 'Dim') &&
                $eventObject['direction'] == 'Tx' && $eventObject['medium'] == 'PL'){
            $model = Application_Model_DeviceMapper::getInstance();
            $address = $eventObject['house'] . $eventObject['device'];
            $deviceObj = $model->getDeviceByAddress('X10', $address);
            if($deviceObj){
                $intesity = (int)$deviceObj->status_data['intesity'];
                $diff = (int)$eventObject['parameter'];
                echo "Old $address intesity is : $intesity, and curr diff is $diff\n";
                if($eventObject['type'] == 'Bright'){
                    $intesity += $diff;
                }
                else{
                    $intesity -= $diff;
                }
                $deviceObj->status_data['intesity'] = $intesity;
                echo "Updating $address intesity: $intesity \n";
                $model->updateDevice($deviceObj);
            }
            else{
                echo "Device $address not found \n";
            }
        }
    }

    protected function runTriggers($eventObject){
        $triggers = Application_Model_TriggerMapper::getInstance()->fetchAllEnabled();
        /* @var $trigger Application_Model_Trigger*/
        foreach ($triggers as $triggerObj) {            
            $trigger = Octopus_X10_Trigger::factory($triggerObj->class);
            $trigger->execute($eventObject, $triggerObj);
        }
    }

}