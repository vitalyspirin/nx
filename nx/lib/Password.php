<?php

/**
 * NX
 *
 * @author    Nick Sinopoli <NSinopoli@gmail.com>
 * @copyright Copyright (c) 2011-2012, Nick Sinopoli
 * @license   http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace nx\lib;

/*
 *  Adopted from PHPass, the `Password` class is
 *  used to create and check password hashes.
 *
 *  @package lib
 *  @see http://www.openwall.com/phpass/
 */
class Password {

   /**
    *  Checks a password against a stored hash.
    *
    *  @param string $password       The password to be checked.
    *  @param string $stored_hash    The stored hash to be checked against.
    *  @access public
    *  @return bool
    */
    public static function check($password, $stored_hash) {
        $hash = crypt($password, $stored_hash);
        return ( $hash == $stored_hash );
    }

   /**
    *  Generates a salt based on an provided input.
    *
    *  @param string $input    The input.
    *  @access public
    *  @return string
    */
    public static function gensalt_blowfish($input) {
        $itoa64 = './ABCDEFGHIJKLMNOPQRSTUVWXYZ'
            . 'abcdefghijklmnopqrstuvwxyz0123456789';
        $iteration_count_log2 = 8;

        $output = '$2a$';
        $output .= chr(ord('0') + $iteration_count_log2 / 10);
        $output .= chr(ord('0') + $iteration_count_log2 % 10);
        $output .= '$';

        $i = 0;
        do {
            $c1 = ord($input[$i++]);
            $output .= $itoa64[$c1 >> 2];
            $c1 = ($c1 & 0x03) << 4;
            if ( $i >= 16 ) {
                $output .= $itoa64[$c1];
                break;
            }

            $c2 = ord($input[$i++]);
            $c1 |= $c2 >> 4;
            $output .= $itoa64[$c1];
            $c1 = ($c2 & 0x0f) << 2;

            $c2 = ord($input[$i++]);
            $c1 |= $c2 >> 6;
            $output .= $itoa64[$c1];
            $output .= $itoa64[$c2 & 0x3f];
        } while ( true );

        return $output;
    }

   /**
    *  Gets the hash of a password.
    *
    *  @param string $password    The password to be hashed.
    *  @access public
    *  @return string
    */
    public static function get_hash($password) {
        $random = self::get_random_bytes(16);
        return crypt($password, self::gensalt_blowfish($random));
    }

   /**
    *  Gets random bytes from a provided source if available, falls back
    *  to hashing microtime otherwise.
    *
    *  @param int $count        The number of bytes to generate.
    *  @param string $source    The source of entropy.
    *  @access public
    *  @return string
    */
    public static function get_random_bytes($count, $source = '/dev/urandom') {
        $output = '';
        if ( is_readable($source) && ($fh = @fopen($source, 'rb')) ) {
            $output = fread($fh, $count);
            fclose($fh);
            return $output;
        }

        $output = '';
        $random_state = microtime() . getmypid();
        for ( $i = 0; $i < $count; $i += 16 ) {
            $random_state = hash('sha256', microtime() . $random_state);
            $output .= pack('H*', hash('sha256', $random_state));
        }
        return substr($output, 0, $count);
    }

}

?>
