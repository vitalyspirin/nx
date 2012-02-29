<?php

namespace nx\test\unit\core;

use nx\test\mock\core\ObjectMock;

class ObjectTest extends \PHPUnit_Framework_TestCase {

    public function test_ConfigSettings_AreAccessible() {
        $object = new ObjectMock(array('test' => true, 'hello' => 'world'));
        $this->assertTrue($object->get_config('test'));
        $this->assertEquals('world', $object->get_config('hello'));
    }

    public function test_ConfigInitTrue_RunsInitializeMethod() {
        $object = new ObjectMock(array('init' => true));
        $this->assertTrue($object->is_initialized);
    }

    public function test_NoConfigInit_RunsInitializeMethod() {
        $object = new ObjectMock();
        $this->assertTrue($object->is_initialized);
    }

    public function test_ConfigInitFalse_DoesNotRunInitializeMethod() {
        $object = new ObjectMock(array('init' => false));
        $this->assertFalse($object->is_initialized);
    }

    public function test_Classname_ReturnsClassnameWithoutNamespace() {
        $object = new ObjectMock();
        $this->assertEquals('ObjectMock', $object->classname());
    }


}

?>
