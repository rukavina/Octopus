<?php

/**
 * X10 Event Publisher Service
 * reading from Mochad writting to RabbitMQ
 *
 * @author milan
 * @package X10
 * @subpackage Event
 */
class Octopus_X10_Event_Publisher_Service {

    /**
     *
     * @var Octopus_X10_Event_Publisher_Service
     */
    protected static $_instance = null;
    /**
     *
     * @var array
     */
    protected $config = array();

    /**
     * All device Statuses
     * @var array
     */
    protected $status = array();

    /**
     * house => device
     * 
     * @var array
     */
    protected $selectedDevices = array();

    /**
     *
     * @var resource
     */
    protected $socket = null;

    /**
     *
     * @var AMQPChannel
     */
    protected $amqChannel = null;

    /**
     *
     * @var $amqConn
     */
    protected $amqConn = null;

    /**
     *
     * @var Octopus_X10_Command
     */
    protected $commander = null;

    /**
     *
     * @var boolean
     */
    protected $stop = false;


    /**
     * constructor
     *
     * @param array $config
     */
    private function __construct($config) {
        require_once('amqplib/amqp.inc');
        $this->config = $config;
        $this->commander = Octopus_X10_Command::getInstance($config);
    }

    /**
     * returns singleton instance of Octopus_X10_Event_Publisher_Service
     *
     * @param array $config
     * @return Octopus_X10_Event_Publisher_Service
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
        if($this->socket){
            echo "Closing socket...";
            socket_close($this->socket);
            echo "OK.\n\n";
        }
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

    public function stop(){
        $this->stop = true;
    }

    /**
     * start service
     */
    public function run() {
        echo "X10.Event.Publisher is starting...\n================\n";
        // Create Rabbit MQ channel
        $this->amqConn = new AMQPConnection($this->config['amqp']['host'], $this->config['amqp']['port'], $this->config['amqp']['user'], $this->config['amqp']['pass']);
        $this->amqChannel = $this->amqConn->channel();
        $this->amqChannel->access_request($this->config['amqp']['vhost'], false, false, true, true);

        /* Create a TCP/IP socket. */
        $this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        if ($this->socket === false) {
            echo "socket_create() failed: reason: " . socket_strerror(socket_last_error()) . "\n";
        } else {
            echo "OK.\n";
        }

        //get status
        echo "Reading devices status ...\n\n";
        $this->status = $this->commander->getStatus();
        foreach($this->status['selected'] as $house => $device){
            $this->selectedDevices[$house] = $device;
        }

        echo "Attempting to connect to '" . $this->config['mochad']['host'] . "' on port '" . $this->config['mochad']['port'] . "'...\n";
        $result = socket_connect($this->socket, $this->config['mochad']['host'], $this->config['mochad']['port']);
        if ($result === false) {
            echo "socket_connect() failed.\nReason: ($result) " . socket_strerror(socket_last_error($this->socket)) . "\n";
        } else {
            echo "OK.\n";
        }

        echo "Reading socket:\n\n";

        while (!$this->stop && ($out = socket_read($this->socket, 2048))) {
            echo "\nSocket Text:\n$out\n=====================\n";
            $events = $this->parseEvents($out);
            foreach ($events as $event) {
                $this->processEvent($event);
            }
        }
    }

    /**
     * parse read string into events array
     *
     * @param string $text
     * @return array
     */
    protected function parseEvents($text){
        $text .= "\n";
        $events = array();
        $matches = array();
        $regex = "/([0-9]{2})\/([0-9]{2}) ([0-9]{2})\:([0-9]{2})\:([0-9]{2}) ([T,R]x) ([PL|RF]{2}) House(Unit)*: ([A-F]{1})([0-9]{0,2})( Func: (.*))*/";
        $matchResult = preg_match_all($regex, $text, $matches, PREG_SET_ORDER);
        echo "\nMatch result: $matchResult\n";
        if($matchResult > 0){
            foreach ($matches as $match) {
                $type = isset($match['12'])? $match['12']:"";
                $house = $match['9'];
                $device = $match['10'];
                $time = mktime($match['3'], $match['4'], $match['5'], $match['1'], $match['2'], date("Y"));

                if($type == ''){
                    //address selector - not function
                    $this->selectedDevices[$house] = $device;
                }
                else{
                    $fParts = array();
                    if(preg_match("/(.*)\(([0-9]+)\)/", $type, $fParts) > 0){
                        $type = $fParts[1];
                        $param = $fParts[2];
                    }
                    else{
                        $param = null;
                    }
                    //function generate event
                    $event = array(
                        "type"      => $type,
                        "time"      => $time,
                        "direction" => $match['6'],
                        "medium"    => $match['7'],
                        "house"     => $house,
                        "device"    => ($device != "")?$device:$this->selectedDevices[$house]
                    );
                    if($param != null){
                        $event['parameter'] = $param;
                    }
                    //attach status
                    $event['status'] = $this->commander->getStatus();
                    $this->status = $event['status'];
                    
                    echo "Found Event: \n==============\n";
                    print_r($event); echo "\n\n";
                    $events[] = $event;
                }
            }
        }
        return $events;
    }

    /**
     * Process event - send AMQ message
     *
     * @param array $event
     */
    protected function processEvent(&$event){
        $msg = new AMQPMessage(json_encode($event), array('content_type' => 'text/json'));
        $this->amqChannel->basic_publish($msg, $this->config['amqp']['exchange']);
    }

}
