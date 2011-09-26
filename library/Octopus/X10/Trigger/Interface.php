<?php

/**
 * Trigger Interface
 * 
 * @package X10
 * @author milan
 */
interface Octopus_X10_Trigger_Interface {

    /**
     * Execute on Event
     *
     * @param array $event
     * @param Application_Model_Trigger $triggerObject
     */
    public function execute($event,$triggerObject);
}
