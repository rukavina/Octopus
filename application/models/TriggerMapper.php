<?php

class Application_Model_TriggerMapper
{
    /**
     *
     * @var Application_Model_TriggerMapper
     */
    protected static $_instance = null;

    /**
     *
     * @var Application_Model_DbTable_Trigger
     */
    private $table = null;

    private function __construct() {
        $this->table = new Application_Model_DbTable_Trigger();
    }

    /**
     *
     * @return Application_Model_TriggerMapper
     */
    public static function getInstance()
    {
        if(self::$_instance === null)
        {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     *
     * @param string|array|Zend_Db_Table_Select $where
     * @return array
     */
    public function fetchAll($where = null){
        $resultSet = $this->table->fetchAll();
        $triggers   = array();
        foreach ($resultSet as $row) {
            $trigger = new Application_Model_Trigger();
            $this->_row2Entity($row, $trigger);
            
            $triggers[] = $trigger;
        }
        return $triggers;
    }

    /**
     * Fetch only enabled triggers
     * 
     * @return array
     */
    public function fetchAllEnabled(){
        return $this->fetchAll("enabled='yes'");
    }

    /**
     * Save trigger entity
     * 
     * @param Application_Model_Trigger $trigger
     */
    public function save(Application_Model_Trigger $trigger){

        $data = array(
            'class'         => $trigger->class,
            'name'          => $trigger->name,
            'settings_data' => json_encode($trigger->settings_data),
            'status_data'   => json_encode($trigger->status_data),
            'trigger_id'    => $trigger->trigger_id,
            'type'          => $trigger->type
        );

        if (null === ($id = $trigger->trigger_id)) {
            unset($data['trigger_id']);
            $this->table->insert($data);
        } else {
            $this->table->update($data, array('trigger_id = ?' => $id));
        }
    }

    /**
     * Get trigger by id
     * 
     * @param int $id
     * @param Application_Model_Trigger $trigger
     * @return Application_Model_Trigger|null
     */
    public function find($id, Application_Model_Trigger $trigger){
        $result = $this->table->find($id);
        if (0 == count($result)) {
            return null;
        }
        $row = $result->current();
        $this->_row2Entity($row, $trigger);

        return $trigger;
    }

    protected function _row2Entity(&$row,Application_Model_Trigger &$trigger){
        $trigger->trigger_id = $row->trigger_id;
        $trigger->class = $row->class;
        $trigger->name = $row->name;
        $trigger->settings_data = json_decode($row->settings_data, true);
        $trigger->status_data = json_decode($row->status_data, true);
        $trigger->type = $row->type;
    }

}

