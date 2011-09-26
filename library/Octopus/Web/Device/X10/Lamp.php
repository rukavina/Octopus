<?php
/**
 * X10 Lamp
 *
 * @package Web
 * @subpackage X10
 * @author milan
 */
class Octopus_Web_Device_X10_Lamp extends Octopus_Web_Device_X10_Switch{
    /**
     * Execute hook
     * 
     * @param string $command
     * @param mixed $bootstrap
     * @param Application_Model_Device $device
     * @param array $parameters
     */
    protected function _internalExecute($command,$bootstrap,$device,$parameters){
        if($command == 'xdim'){
            if(!isset ($parameters['intesity']) || $parameters['intesity'] == 0){
                $command = 'off';
            }
            else{
                $intesity = round(($parameters['intesity'] / 100) * 63);

                if(!$device->status_data['on']){
                    //fire on first
                    Octopus_X10_Command::getInstance($bootstrap->getOptions())
                                    ->execute("on", $device->address,null,false);

                    $device->status_data['on'] = 1;
                }
                $device->status_data['intesity'] = $parameters['intesity'];
                
                //update in DB
                Application_Model_DeviceMapper::getInstance()->updateDevice($device);

                return Octopus_X10_Command::getInstance($bootstrap->getOptions())
                    ->execute('xdim', $device->address,$intesity,false);
            }
        }

        return Octopus_X10_Command::getInstance($bootstrap->getOptions())
                ->execute($command, $device->address,null,false);
    }
}
