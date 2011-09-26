<?php
/**
 * Trigger Factory
 *
 * @package X10
 * @author milan
 */
class Octopus_X10_Trigger {
    /**
     * Create trigger engine
     * 
     * @param string $class
     * @return Octopus_X10_Trigger_Interface
     */
    public static function factory($class){
        $engineClass = str_replace(".", "_", $class);
        if(!class_exists($engineClass)){
            return false;
        }
        
        $engine = new $engineClass();
        return $engine;
    }
}
