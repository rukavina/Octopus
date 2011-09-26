<?php

class Application_Model_VariableMapper
{
    /**
     *
     * @var Application_Model_VariableMapper
     */
    protected static $_instance = null;

    /**
     *
     * @var Application_Model_DbTable_Variable
     */
    private $table = null;

    private function __construct() {
        $this->table = new Application_Model_DbTable_Variable();
    }

    /**
     *
     * @return Application_Model_VariableMapper
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
     * Get value by name
     *
     * @param string $name
     * @return string|null
     */
    public function get($name){
        $result = $this->table->find($name);
        if (0 == count($result)) {
            return null;
        }
        $row = $result->current();        
        return $row->value;
    }

    /**
     * Save var value
     *
     * @param string $name
     * @param string $value
     */
    public function set($name,$value){        
        $data = array(
            'name'          => $name,
            'value'         => $value
        );
        $existing = $this->get($name);
        if (null === $existing) {
            $this->table->insert($data);
        } else {            
            $this->table->update($data,array('name = ?' => $name));
        }
    }

}

