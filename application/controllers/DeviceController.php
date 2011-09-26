<?php

class DeviceController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        // action body
    }

    public function commandAction(){
        $deviceId = $this->_getParam('device_id');
        $command = $this->_getParam('command_name');

        $device = Application_Model_DeviceMapper::getInstance()->getDevice($deviceId);

        $engine = Octopus_Web_Device::factory($device->widget_class);
        if(!$engine){
            return $this->_helper->json(false);
        }

        return $this->_helper->json($engine->execute($command, $device, $this->_request->getParams()));
    }


}

