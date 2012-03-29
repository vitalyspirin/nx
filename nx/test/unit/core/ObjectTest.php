<?php

namespace nx\test\unit\core;

use nx\test\mock\core\ObjectMock;

class ObjectTest extends \PHPUnit_Framework_TestCase {

    public function test_Classname_ReturnsClassnameWithoutNamespace() {
        $object = new ObjectMock();
        $this->assertEquals('ObjectMock', $object->classname());
    }


}

?>
