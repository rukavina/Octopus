<?php

class Application_Model_DbTable_DeviceScene extends Zend_Db_Table_Abstract {

    protected $_name = 'device_scene';
    
    protected $_referenceMap = array(
        'Device' => array(
            'columns'       => array('device_id'),
            'refTableClass' => 'Application_Model_DbTable_Device',
            'refColumns'    => array('device_id')
        ),
        'Scene' => array(
            'columns'       => array('scene_id'),
            'refTableClass' => 'Application_Model_DbTable_Scene',
            'refColumns'    => array('scene_id')
        )
    );

}

