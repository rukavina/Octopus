<?php

class Application_Model_DbTable_Device extends Zend_Db_Table_Abstract
{

    protected $_name = 'device';
    protected $_id = "device_id";

    protected $_dependentTables = array('Application_Model_DbTable_DeviceScene');
}

