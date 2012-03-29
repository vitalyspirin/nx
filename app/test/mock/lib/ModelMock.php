<?php

namespace app\test\mock\lib;

class ModelMock {
    protected $id;
    protected $test_name = 'test value';

    public function __get($property) {
        return $this->$property;
    }

    public function classname() {
        return 'ModelMock';
    }

    public function get_primary_key() {
        return $this->id;
    }

    public function set_id($val) {
        $this->id = $val;
    }

}

?>
