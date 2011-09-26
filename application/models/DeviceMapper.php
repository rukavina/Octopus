<?php

class Application_Model_DeviceMapper
{

    /**
     *
     * @var Application_Model_DeviceMapper
     */
    protected static $_instance = null;

    /**
     *
     * @var Application_Model_DbTable_Device
     */
    private $deviceTable = null;

    private function __construct() {
        $this->deviceTable = new Application_Model_DbTable_Device();
    }

    /**
     *
     * @return Application_Model_DeviceMapper
     */
    public static function getInstance()
    {
        if(self::$_instance === null)
        {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Get Device Model
     * 
     * @param int $deviceId
     * @return Application_Model_Device
     */
    public function getDevice($deviceId){
        $result = new Application_Model_Device();

        $deviceRS = $this->deviceTable->find($deviceId);
        $device = $deviceRS->current();
        if(!isset ($device)){
            return false;
        }

        $result->device_id = $device->device_id;
        $result->type = $device->type;
        $result->address = $device->address;
        $result->name = $device->name;
        $result->widget_class = $device->widget_class;
        $result->settings_data = json_decode($device->settings_data, true);
        $result->status_data = json_decode($device->status_data, true);

        return $result;
    }

    /**
     * Get Device Model by address
     *
     * @param string $type
     * @param string $address
     * 
     * @return Application_Model_Device
     */
    public function getDeviceByAddress($type,$address){
        $result = new Application_Model_Device();

        $deviceRS = $this->deviceTable->fetchAll($this->deviceTable->select()->where('type = ?', $type)->where('address = ?',$address));
        if(!$deviceRS){
            return false;
        }
        
        $device = $deviceRS->current();
        if(!isset ($device)){
            return false;
        }

        $result->device_id = $device->device_id;
        $result->type = $device->type;
        $result->address = $device->address;
        $result->name = $device->name;
        $result->widget_class = $device->widget_class;
        $result->settings_data = json_decode($device->settings_data, true);
        $result->status_data = json_decode($device->status_data, true);

        return $result;
    }

    /**
     * Update device
     * 
     * @param Application_Model_Device $deviceObj
     */
    public function updateDevice($deviceObj){
        $deviceRS = $this->deviceTable->find($deviceObj->device_id);
        $device = $deviceRS->current();
        if(!isset ($device)){
            return false;
        }

        $device->type = $deviceObj->type;
        $device->address = $deviceObj->address;
        $device->name = $deviceObj->name;
        $device->widget_class = $deviceObj->widget_class;
        $device->settings_data = json_encode($deviceObj->settings_data);
        $device->status_data = json_encode($deviceObj->status_data);

        $device->save();
    }

}

