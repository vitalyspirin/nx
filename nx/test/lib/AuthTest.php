<?php

namespace nx\test\lib;

use nx\lib\Auth;

class AuthTest extends \PHPUnit_Framework_TestCase {

    public function test_ValidateToken_ReturnsBool() {
        $token = Auth::create_token();
        $this->assertTrue(Auth::is_token_valid($token));

        // reset session vars
        $_SESSION = array();

        $real_token = Auth::create_token();
        $fake_token = 'random';
        $this->assertFalse(Auth::is_token_valid($fake_token));
    }

}
?>
