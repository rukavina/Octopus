<?php

class Application_Model_DbTable_Scene extends Zend_Db_Table_Abstract
{

    protected $_name = 'scene';
    protected $_id = "scene_id";

    protected $_dependentTables = array('Application_Model_DbTable_DeviceScene');

}

