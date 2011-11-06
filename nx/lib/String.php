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
 *  The `String` class contains methods that help with
 *  string manipulation.
 *
 *  @package lib
 */
class String {

   /**
    *  Returns the string between two delimiters in a body of text.
    *
    *  @param string $start         The beginning delimiter.
    *  @param string $end           The ending delimiter.
    *  @param string $body          The text containing the delimiters.
    *  @access public
    *  @return string
    */
    public static function between($start, $end, $body) {
        $start_pos = strpos($body, $start);
        if ( $start_pos === false ) {
            return '';
        }
        $start_pos += strlen($start);
        $end_pos = strpos($body, $end);
        if ( $end_pos === false ) {
            return '';
        }
        $between = substr($body, $start_pos, $end_pos - $start_pos);
        return ( $between ) ? trim($between) : '';
    }

}

?>
