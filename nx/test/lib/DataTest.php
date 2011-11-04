<?php

namespace nx\test\lib;

use nx\lib\Data;

class DataTest extends \PHPUnit_Framework_TestCase {

    public function test_Typecast_ReturnsTypecastedData() {
        // Bool to bool
        $data = true;
        $type = 'b';
        $check = true;
        $casted = Data::typecast($data, $type);
        $this->assertEquals($casted, $check);

        // Bool to float
        $data = true;
        $type = 'f';
        $check = 1;
        $casted = Data::typecast($data, $type);
        $this->assertEquals($casted, $check);

        // Bool to int
        $data = false;
        $type = 'i';
        $check = 0;
        $casted = Data::typecast($data, $type);
        $this->assertEquals($casted, $check);

        // Bool to string
        $data = true;
        $type = 's';
        $check = '1';
        $casted = Data::typecast($data, $type);
        $this->assertEquals($casted, $check);

        // Bool to string
        $data = false;
        $type = 's';
        $check = '';
        $casted = Data::typecast($data, $type);
        $this->assertEquals($casted, $check);

        // Float to bool
        $data = 1.234;
        $type = 'b';
        $check = true;
        $casted = Data::typecast($data, $type);
        $this->assertEquals($casted, $check);

        // Float to float
        $data = 1.234;
        $type = 'f';
        $check = 1.234;
        $casted = Data::typecast($data, $type);
        $this->assertEquals($casted, $check);

        // Float to int
        $data = 1.928;
        $type = 'i';
        $check = 1928;
        $casted = Data::typecast($data, $type);
        $this->assertEquals($casted, $check);

        // Float to string
        $data = 1.928;
        $type = 's';
        $check = '1.928';
        $casted = Data::typecast($data, $type);
        $this->assertEquals($casted, $check);

        // Int to bool
        $data = 0;
        $type = 'b';
        $check = false;
        $casted = Data::typecast($data, $type);
        $this->assertEquals($casted, $check);

        // Int to bool
        $data = 1;
        $type = 'b';
        $check = true;
        $casted = Data::typecast($data, $type);
        $this->assertEquals($casted, $check);

        // Int to float
        $data = 2;
        $type = 'f';
        $check = 2;
        $casted = Data::typecast($data, $type);
        $this->assertEquals($casted, $check);

        // Int to int
        $data = 3;
        $type = 'i';
        $check = 3;
        $casted = Data::typecast($data, $type);
        $this->assertEquals($casted, $check);

        // Int to string
        $data = 3;
        $type = 's';
        $check = '3';
        $casted = Data::typecast($data, $type);
        $this->assertEquals($casted, $check);

        // String to bool
        $data = 'true';
        $type = 'b';
        $check = false;
        $casted = Data::typecast($data, $type);
        $this->assertEquals($casted, $check);

        // String to bool
        $data = '1';
        $type = 'b';
        $check = true;
        $casted = Data::typecast($data, $type);
        $this->assertEquals($casted, $check);

        // String to float
        $data = "1.928";
        $type = 'f';
        $check = 1.928;
        $casted = Data::typecast($data, $type);
        $this->assertEquals($casted, $check);

        // String to int
        $data = "1";
        $type = 'i';
        $check = 1;
        $casted = Data::typecast($data, $type);
        $this->assertEquals($casted, $check);

        // String to int
        $data = "1.928";
        $type = 'i';
        $check = 1.928;
        $casted = Data::typecast($data, $type);
        $this->assertNotEquals($casted, $check);

        // String to string
        $data = 'test';
        $type = 's';
        $check = 'test';
        $casted = Data::typecast($data, $type);
        $this->assertEquals($casted, $check);
    }
}
?>
