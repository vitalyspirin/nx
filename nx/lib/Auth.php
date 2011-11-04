<?php

/**
 * NX
 *
 * @author    Nick Sinopoli <NSinopoli@gmail.com>
 * @copyright Copyright (c) 2011, Nick Sinopoli
 * @license   http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace nx\lib;

/*
 *  The `Auth` class is used to create and validate
 *  form tokens.  These tokens help to ensure that
 *  any remote data sent to the server comes from the
 *  actual sender, and not via a CSRF.
 *
 *  @package lib
 */
class Auth {

   /**
    *  The configuration settings.
    *
    *  @var array
    *  @access protected
    */
    protected static $_config = array(
        'gc_collect' => 3600
    );

   /**
    *  Creates a unique token for a given session.
    *
    *  @param string $salt          The token salt.
    *  @access public
    *  @return string
    */
    public static function create_token($salt = 'KU7*[lI^7iC)') {
        if ( !isset($_SESSION['token']) ) {
            $_SESSION['token'] = array();
        }

        $token = sha1(microtime() . $salt);
        $_SESSION['token'] = self::garbage_collect($_SESSION['token']) + array(
            (string) microtime(true) => $token
        );

        return $token;
    }

   /**
    *  Cleans out any tokens that have expired.
    *
    *  @param array $tokens         The tokens to check for expiration.
    *  @param int $expiration       The number of seconds after which
    *                               the tokens should expire.
    *  @access public
    *  @return string
    */
    public static function garbage_collect($tokens, $expiration = null) {
        if ( is_null($expiration) ) {
            $expiration = self::$_config['gc_collect'];
        }

        foreach ( $tokens as $timestamp => $token ) {
            if ( time() - (int) $timestamp > $expiration ) {
                unset($tokens[$timestamp]);
            }
        }
        return $tokens;
    }

   /**
    *  Checks that the supplied token is valid for a given request.
    *
    *  @param string $token         The token.
    *  @access public
    *  @return bool
    */
    public static function is_token_valid($token) {
        return ( in_array($token, $_SESSION['token']) );
    }

}

?>
