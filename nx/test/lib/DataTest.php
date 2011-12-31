<?php

namespace nx\test\lib;

use nx\lib\Data;

class DataTest extends \PHPUnit_Framework_TestCase {

    public function test_Typecast_ReturnsTypecastedData() {
        // Bool to bool
        $data = true;
        $type = 'bool';
        $check = true;
        $casted = Data::typecast($data, $type);
        $this->assertEquals($casted, $check);

        // Bool to float
        $data = true;
        $type = 'float';
        $check = 1;
        $casted = Data::typecast($data, $type);
        $this->assertEquals($casted, $check);

        // Bool to int
        $data = false;
        $type = 'int';
        $check = 0;
        $casted = Data::typecast($data, $type);
        $this->assertEquals($casted, $check);

        // Bool to string
        $data = true;
        $type = 'string';
        $check = '1';
        $casted = Data::typecast($data, $type);
        $this->assertEquals($casted, $check);

        // Bool to string
        $data = false;
        $type = 'string';
        $check = '';
        $casted = Data::typecast($data, $type);
        $this->assertEquals($casted, $check);

        // Float to bool
        $data = 1.234;
        $type = 'bool';
        $check = true;
        $casted = Data::typecast($data, $type);
        $this->assertEquals($casted, $check);

        // Float to float
        $data = 1.234;
        $type = 'float';
        $check = 1.234;
        $casted = Data::typecast($data, $type);
        $this->assertEquals($casted, $check);

        // Float to int
        $data = 1.928;
        $type = 'int';
        $check = 1928;
        $casted = Data::typecast($data, $type);
        $this->assertEquals($casted, $check);

        // Float to string
        $data = 1.928;
        $type = 'string';
        $check = '1.928';
        $casted = Data::typecast($data, $type);
        $this->assertEquals($casted, $check);

        // Int to bool
        $data = 0;
        $type = 'bool';
        $check = false;
        $casted = Data::typecast($data, $type);
        $this->assertEquals($casted, $check);

        // Int to bool
        $data = 1;
        $type = 'bool';
        $check = true;
        $casted = Data::typecast($data, $type);
        $this->assertEquals($casted, $check);

        // Int to float
        $data = 2;
        $type = 'float';
        $check = 2;
        $casted = Data::typecast($data, $type);
        $this->assertEquals($casted, $check);

        // Int to int
        $data = 3;
        $type = 'int';
        $check = 3;
        $casted = Data::typecast($data, $type);
        $this->assertEquals($casted, $check);

        // Int to string
        $data = 3;
        $type = 'string';
        $check = '3';
        $casted = Data::typecast($data, $type);
        $this->assertEquals($casted, $check);

        // String to bool
        $data = 'true';
        $type = 'bool';
        $check = false;
        $casted = Data::typecast($data, $type);
        $this->assertEquals($casted, $check);

        // String to bool
        $data = '1';
        $type = 'bool';
        $check = true;
        $casted = Data::typecast($data, $type);
        $this->assertEquals($casted, $check);

        // String to float
        $data = "1.928";
        $type = 'float';
        $check = 1.928;
        $casted = Data::typecast($data, $type);
        $this->assertEquals($casted, $check);

        // String to int
        $data = "1";
        $type = 'int';
        $check = 1;
        $casted = Data::typecast($data, $type);
        $this->assertEquals($casted, $check);

        // String to int
        $data = "1.928";
        $type = 'int';
        $check = 1.928;
        $casted = Data::typecast($data, $type);
        $this->assertNotEquals($casted, $check);

        // String to string
        $data = 'test';
        $type = 'string';
        $check = 'test';
        $casted = Data::typecast($data, $type);
        $this->assertEquals($casted, $check);
    }
}
?>
