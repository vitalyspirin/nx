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
 *  The `Data` class helps typecast data.
 *
 *  @package lib
 */
class Data {

   /**
    *  Typecasts input.
    *
    *  @param mixed $data      The data to be typecasted.
    *  @param string $type     The type.
    *  @access public
    *  @return mixed
    */
    public static function typecast($data, $type) {
        switch ( $type ) {
            case 'b':
                $data = (boolean) filter_var($data, FILTER_SANITIZE_NUMBER_INT);
                break;
            case 'f':
                $data = floatval(filter_var(
                    $data,
                    FILTER_SANITIZE_NUMBER_FLOAT,
                    FILTER_FLAG_ALLOW_FRACTION
                ));
                break;
            case 'i':
                $data = intval(filter_var($data, FILTER_SANITIZE_NUMBER_INT));
                break;
            case 's':
                $data = trim(strval($data));
                break;
        }
        return $data;
    }

}

?>
