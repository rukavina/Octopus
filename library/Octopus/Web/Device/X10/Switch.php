<?php
/**
 * X10 Switch
 *
 * @package Web
 * @subpackage X10
 * @author milan
 */
class Octopus_Web_Device_X10_Switch implements Octopus_Web_Device_Interface{
    /**
     * Execute device command
     * 
     * @param string $command
     * @param Application_Model_Device $device
     * @param array $parameters
     */
    public function execute($command, $device, $parameters) {
        //get application config
        $bootstrap = Zend_Controller_Front::getInstance()->getParam('bootstrap');
        if (null === $bootstrap) {
            throw new Exception('Unable to find bootstrap');
        }

        $this->_internalExecute($command,$bootstrap, $device, $parameters);

        return true;
    }

    /**
     * Execute hook
     *
     * @param string $command
     * @param mixed $bootstrap
     * @param Application_Model_Device $device
     * @param array $parameters
     */
    protected function _internalExecute($command,$bootstrap,$device,$parameters){
        return Octopus_X10_Command::getInstance($bootstrap->getOptions())
                ->execute($command, $device->address,null,false);
    }
}
