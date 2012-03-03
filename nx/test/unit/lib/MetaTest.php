<?php

namespace nx\test\lib;

use nx\lib\Meta;

class MetaMock {
    public $id = 42;
    public $test_name = 'test value';
    protected $_no_access;

    public function do_nothing() {
        return false;
    }

    protected function _set_id($val) {
        $this->id = $val;
    }
}

class MetaMock2 {
    public $id;
    public $test_name = 'test value';
    protected $_allow_access;

    public function do_something() {
        return true;
    }

    protected function _get_id($val) {
        return $this->id;
    }
}

class MetaTest extends \PHPUnit_Framework_TestCase {

    public function test_GetPublicProperties_ReturnsArray() {
        $model = new MetaMock();
        $properties = array(
            'id'        => 42,
            'test_name' => 'test value'
        );
        $check = Meta::get_public_properties($model);
        $this->assertEquals($properties, $check);

        $model = new MetaMock2();
        $properties = array(
            'id'        => null,
            'test_name' => 'test value'
        );
        $check = Meta::get_public_properties($model);
        $this->assertEquals($properties, $check);
    }

}
