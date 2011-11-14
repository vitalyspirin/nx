<?php

namespace nx\test\lib;

use nx\lib\Meta;

class MetaMock {
    protected $id = 42;
    protected $test_name = 'test value';
    protected $_no_access;

    public function do_nothing() {
        return false;
    }

    protected function _set_id($val) {
        $this->id = $val;
    }
}

class MetaMock2 {
    protected $id;
    protected $test_name = 'test value';
    protected $_allow_access;

    public function do_something() {
        return true;
    }

    protected function _get_id($val) {
        $this->id = $val;
    }
}

class MetaTest extends \PHPUnit_Framework_TestCase {

    public function test_Classname_ReturnsClassnameWithoutNamepsace() {
        $model = new MetaMock();
        $class = 'MetaMock';
        $check = Meta::classname_only($model);
        $this->assertEquals($class, $check);

        $model = new MetaMock2();
        $class = 'MetaMock2';
        $check = Meta::classname_only($model);
        $this->assertEquals($class, $check);

        $class = 'MetaTest';
        $check = Meta::classname_only($this);
        $this->assertEquals($class, $check);
    }

    public function test_GetColumns_ReturnsArray() {
        $model = new MetaMock();
        $columns = array(
            'id'        => 42,
            'test_name' => 'test value'
        );
        $check = Meta::get_columns($model);
        $this->assertEquals($columns, $check);

        $model = new MetaMock2();
        $columns = array(
            'id'        => null,
            'test_name' => 'test value'
        );
        $check = Meta::get_columns($model);
        $this->assertEquals($columns, $check);
    }

    public function test_GetProtectedMethods_ReturnsArray() {
        $model = new MetaMock();
        $methods = array('_set_id');
        $check = Meta::get_protected_methods($model);
        $this->assertEquals($methods, $check);

        $model = new MetaMock2();
        $methods = array('_get_id');
        $check = Meta::get_protected_methods($model);
        $this->assertEquals($methods, $check);
    }

}
