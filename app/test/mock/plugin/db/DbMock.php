<?php

namespace app\test\mock\plugin\db;

class DbMock extends \nx\core\Object {

    public function __construct(array $config = array()) {
        $defaults = array(
            'database' => '',
            'host'     => 'localhost',
            'port'     => 3306,
            'username' => 'root',
            'password' => 'admin'
        );
        parent::__construct($config + $defaults);
    }

    protected function _init() {
        $this->connect($this->_config);
    }

    public function connect($config) {
        return true;
    }

}

?>
