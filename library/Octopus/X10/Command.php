<?php

/**
 * X10 Command
 * sending x10 command to mochad
 *
 * @author milan
 * @package X10
 * @subpackage Command
 */
class Octopus_X10_Command {

    /**
     *
     * @var Octopus_X10_Command
     */
    protected static $_instance = null;

    /**
     *
     * @var array
     */
    protected static $config = array();




    /**
     * constructor
     *
     * @param array $config
     */
    private function __construct($config) {
        $this->config = $config;
    }

    /**
     * returns singleton instance of Octopus_X10_Command
     *
     * @param array $config
     * @return Octopus_X10_Command
     */
    public static function getInstance(array $config = null)
    {
        if(self::$_instance === null)
        {
            self::$_instance = new self($config);
        }
        return self::$_instance;
    }


    /**
     * Execute x10 Command
     * 
     * @param string $function on|off|dim|bright|xdim|all_lights_on|all_lights_off|all_units_off
     * @param string $deviceAddress [A..Z|1-16]
     * @param string $param
     * @return array
     */
    public function execute($function,$deviceAddress,$param = null,$echo = true) {
        $function = strtolower($function);
        $deviceAddress = strtolower($deviceAddress);
        if($function == "st"){
            $command = "st";
        }
        else{
            $command = "pl $deviceAddress $function";
            if(isset ($param)){
                $command .= " " . $param;
            }
        }        

        $host = $this->config['mochad']['host'];
        $port = $this->config['mochad']['port'];

        if($echo){
            //echo 'x10 executing: ' . "echo \"$command\" | nc $host $port \n\n";
            echo "x10 executing: $command\n\n";
        }

        $out = array();
        //exec("\"echo $command | nc $host $port\"",$out);

        //echo "open socket\n";
        $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        if ($socket === false) {
            error_log("socket_create() failed: reason: " . socket_strerror(socket_last_error()));
            return false;
        }
        //echo "connect socket\n";
        $result = socket_connect($socket, $host, $port);
        if ($result === false) {
            error_log("socket_connect() failed.\nReason: ($result) " . socket_strerror(socket_last_error($socket)));
            return false;
        }
        //echo "write socket\n";
        $result = socket_write($socket, "$command\n");
        if ($result === false) {
            error_log("socket_write() failed.\nReason: ($result) " . socket_strerror(socket_last_error($socket)));
            return false;
        }
        //echo "read socket\n";
        $result = socket_read($socket, 2048);
        if ($result === false) {
            error_log("socket_read() failed.\nReason: ($result) " . socket_strerror(socket_last_error($socket)));
            return false;
        }
        echo "command result:\n$result\n\n";
        $out = explode("\n", trim($result));

        socket_close($socket);
        return $out;
    }

    public function getStatus(){
        $result = array(
            'selected'  => array(),
            'on'    => array()
        );
        $outLines = $this->execute('st', '');
        $section = '';
        foreach ($outLines as $line) {
            $matches = array();
            if(preg_match("/([0-9]{2})\/([0-9]{2}) ([0-9]{2})\:([0-9]{2})\:([0-9]{2}) (.*)/", $line, $matches)){
                $meat = $matches[6];
            }
            else{
                continue;;
            }
            //check section
            if($meat == "Device selected"){
                $section = "selected";
                continue;
            }
            elseif ($meat == "Device status") {
                $section = "status";
                continue;
            }
            elseif ($meat == "Security sensor status") {
                $section = "security";
                continue;
            }

            switch ($section) {
                case "selected":
                    $this->parseStatusSelected($meat, $result);
                    break;
                case "status":
                    $this->parseStatusOn($meat, $result);
                    break;
                default:
                    break;
            }
        }

        return $result;
    }

    public function parseStatusSelected($s,&$result){
        $matches = array();
        if(preg_match("/House ([A-Z]{1}): ([0-9]{1,2})/", $s, $matches)){
            $result['selected'][$matches[1]] = $matches[2];
        }
    }

    public function parseStatusOn($s,&$result){
        $matches = array();
        if(preg_match("/House ([A-Z]{1}): (.*)/", $s, $matches)){
            $house = $matches[1];
            $sections = explode(",", $matches[2]);
            foreach ($sections as $section) {
                list($device,$status) = explode("=", $section);
                $result['on'][$house . $device] = $status;
            }
        }
    }

}