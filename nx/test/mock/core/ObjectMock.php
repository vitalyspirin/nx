<?php

namespace nx\test\mock\core;

class ObjectMock extends \nx\core\Object {

    public $is_initialized = false;

    protected function _init() {
        $this->is_initialized = true;
    }

    public function get_config($key) {
        return $this->_config[$key];
    }

}

?>
