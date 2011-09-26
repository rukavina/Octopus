<?php

/**
 * Device Interface
 * 
 * @package Web
 * @author milan
 */
interface Octopus_Web_Device_Interface {
    
    public function execute($command,$device,$parameters);
}
