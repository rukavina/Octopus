<?php
/**
 * Device Factory
 *
 * @package Web
 * @author milan
 */
class Octopus_Web_Device {
    /**
     * Create widget engine
     * 
     * @param string $widgetClass
     * @return Octopus_Web_Device_Interface
     */
    public static function factory($widgetClass){
        $engineClass = str_replace(".", "_", $widgetClass);
        if(!class_exists($engineClass)){
            return false;
        }
        
        $engine = new $engineClass();
        return $engine;
    }
}
