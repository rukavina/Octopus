<?php

class Application_Model_SceneMapper
{

    /**
     *
     * @var Application_Model_SceneMapper
     */
    protected static $_instance = null;

    /**
     *
     * @var Application_Model_DbTable_Scene
     */
    private $sceneTable = null;

    private function __construct() {
        $this->sceneTable = new Application_Model_DbTable_Scene();
    }

    /**
     *
     * @return Application_Model_SceneMapper
     */
    public static function getInstance()
    {
        if(self::$_instance === null)
        {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    function getScene($sceneId){
        $result = new Application_Model_Scene();

        $sceneRS = $this->sceneTable->find($sceneId);
        $scene = $sceneRS->current();

        $result->scene_id = $scene->scene_id;
        $result->name = $scene->name;
        $result->settings_data = json_decode($scene->settings_data, true);

        $deviceRS = $scene->findManyToManyRowset('Application_Model_DbTable_Device','Application_Model_DbTable_DeviceScene');
        foreach ($deviceRS as $device) {
            $deviceObj = new Application_Model_Device();
            $deviceObj->device_id = $device->device_id;
            $deviceObj->name = $device->name;
            $deviceObj->type = $device->type;
            $deviceObj->address = $device->address;
            $deviceObj->widget_class = $device->widget_class;
            $deviceObj->settings_data = json_decode($device->settings_data, true);
            $deviceObj->status_data = json_decode($device->status_data, true);
            $result->devices[$device->device_id] = $deviceObj;
        }

        return $result;
    }

}

