<?php

namespace app\lib;

class Application {

   /**
    *  Determines the environment (development, test, or production).
    *
    *  @access public
    *  @return string
    */
    public static function environment() {
        $local = array('::1', '127.0.0.1');
        $is_local = in_array($_SERVER['SERVER_ADDR'], $local);
        $uri = $_SERVER['REQUEST_URI'];
        $is_test = (preg_match('/^test\//', $uri) && $is_local)
            || preg_match('/^test/', $_SERVER['HTTP_HOST']);

        if ( $is_test ) {
            return 'test';
        } elseif ( $is_local ) {
            return 'development';
        }
        return 'production';
    }

}

?>
