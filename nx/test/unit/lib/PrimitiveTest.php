<?php

namespace nx\test\unit\lib;

use nx\lib\Primitive;

class PrimitiveTest extends \PHPUnit_Framework_TestCase {

    public function test_StrBetween_ReturnsString() {
        $test = 'This is a gloriously galumphing test string.';
        $first = 'This';
        $last = 'glorious';
        $check = 'is a';
        $input = Primitive::str_between($first, $last, $test);
        $this->assertEquals($check, $input);

        $test = 'This is a gloriously galumphing test string.';
        $first = 'This';
        $last = 'no';
        $check = '';
        $input = Primitive::str_between($first, $last, $test);
        $this->assertEquals($check, $input);

        $test = 'This is a gloriously galumphing test string.';
        $first = 'is a';
        $last = 'no';
        $check = '';
        $input = Primitive::str_between($first, $last, $test);
        $this->assertEquals($check, $input);

        $test = 'This is a gloriously galumphing test string.';
        $first = 'test';
        $last = 'This';
        $check = '';
        $input = Primitive::str_between($first, $last, $test);
        $this->assertEquals($check, $input);
    }

    public function test_FlattenArray_ReturnsFlattenedArray() {
        $test = array(
            'some stuff' => array(1, 2, 3),
            'other stuff' => array(4, 5, 6),
            'deep stuff' => array(
                array(7, 8, array(9, 10))
            )
        );

        $input = Primitive::flatten_array($test);
        $check = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10);
        $this->assertEquals($input, $check);

    }

}
