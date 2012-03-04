<?php

namespace nx\test\unit\lib;

use nx\lib\Password;

class PasswordTest extends \PHPUnit_Framework_TestCase {

    public function test_Hash_ReturnsUniqueValues() {
        $correct = 'test1234';
        $hash = Password::get_hash($correct);
        $check = Password::check($correct, $hash);
        $this->assertTrue($check);

        $wrong = 'wrong!';
        $check = Password::check($wrong, $hash);
        $this->assertFalse($check);
    }

    public function test_GetRandomBytes_ReturnsRandomBytes() {
        $check = 16;
        $input = strlen(Password::get_random_bytes($check));
        $this->assertEquals($check, $input);

        // Use invalid entropy source
        $check = 16;
        $input = strlen(Password::get_random_bytes($check, '?'));
        $this->assertEquals($check, $input);
    }

}

?>
