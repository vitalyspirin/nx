<?php

namespace nx\test\lib;

use nx\lib\String;

class StringTest extends \PHPUnit_Framework_TestCase {

    public function test_Between_ReturnsString() {
        $test = 'This is a gloriously galumphing test string.';
        $first = 'This';
        $last = 'glorious';
        $between = 'is a';
        $check = String::between($first, $last, $test);
        $this->assertEquals($check, $between);

        $test = 'This is a gloriously galumphing test string.';
        $first = 'This';
        $last = 'no';
        $between = '';
        $check = String::between($first, $last, $test);
        $this->assertEquals($check, $between);

        $test = 'This is a gloriously galumphing test string.';
        $first = 'is a';
        $last = 'no';
        $between = '';
        $check = String::between($first, $last, $test);
        $this->assertEquals($check, $between);

        $test = 'This is a gloriously galumphing test string.';
        $first = 'test';
        $last = 'This';
        $between = '';
        $check = String::between($first, $last, $test);
        $this->assertEquals($check, $between);

    }

}
