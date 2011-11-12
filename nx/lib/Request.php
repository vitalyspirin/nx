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
 *  The `Request` class is used to handle all data
 *  pertaining to an incoming HTTP request.
 *
 *  @package lib
 */
class Request {

   /**
    *  Extracts $_POST data and returns it as a collection of objects (if an
    *  object was bound to it via the form) and `key` => `value` pairs (if no
    *  object is bound).
    *
    *  @param array $data      The $_POST data.
    *  @param array $model     The namespace of the application models.
    *  @access public
    *  @return mixed
    */
    public static function extract_post($data, $model_location) {
        $collection = array();
        foreach ( $data as $child_key => $child ) {
            if ( !is_array($child) ) { // name = 'username'
                $collection[$child_key] = $child;
                continue;
            }

            $loc = strrpos($child_key, '|');
            if ( $loc !== false ) { // name = 'User|id[username]'
                $id = substr($child_key, $loc + 1);
                $class_name = substr($child_key, 0, $loc);
                $class = $model_location . $class_name;
                $obj = new $class(array('id' => $id));
                foreach ( $child as $key => $value ) {
                    $obj->$key = $value;
                }
                $collection[$class_name][] = $obj;
                continue;
            }

            // name = 'User[][username]'
            foreach ( $child as $grandchild_array ) {
                $obj_name = $model_location . $child_key;
                $obj = new $obj_name();
                foreach ( $grandchild_array as $key => $value ) {
                    $obj->$key = $value;
                }
                $collection[$child_key][] = $obj;
            }
        }

        return $collection;
    }

}

?>
