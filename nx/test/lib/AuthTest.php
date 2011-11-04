<?php

namespace nx\test\lib;

use nx\lib\Auth;

class AuthTest extends \PHPUnit_Framework_TestCase {

    public function test_ValidateToken_ReturnsBool() {
        $token = Auth::create_token();
        $this->assertTrue(Auth::is_token_valid($token));

        // Create another token
        $token = Auth::create_token();
        $this->assertTrue(Auth::is_token_valid($token));

        // reset session vars
        $_SESSION = array();

        $real_token = Auth::create_token();
        $fake_token = 'random';
        $this->assertFalse(Auth::is_token_valid($fake_token));
    }

    public function test_GarbageCollect_ReturnsCorrectTokens() {
        $batch = array();
        for ( $i = 0; $i < 5; $i++ ) {
            $batch[(string) microtime(true)] = Auth::create_token();
        }
        sleep(2);
        $last = array(
            (string) microtime(true) => Auth::create_token()
        );
        $batch += $last;
        $tokens = Auth::garbage_collect($batch, 1);
        $this->assertTrue($tokens === $last);

    }


}
?>
