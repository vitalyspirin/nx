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
 *  The `Meta` class is used to return information
 *  about objects by way of reflection.
 *
 *  @package lib
 */
class Meta {

   /**
    *  Returns all of the public properties of a given object.
    *
    *  @param object $object         The object from which to
    *                                retrieve the properties.
    *  @access public
    *  @return array
    */
    public static function get_public_properties($object) {
        $reflection = new \ReflectionClass($object);
        $properties = $reflection->getProperties(\ReflectionProperty::IS_PUBLIC);
        $collection = array();
        foreach ( $properties as $property ) {
            $name = $property->getName();
            $collection[$name] = $property->getValue($object);
        }
        return $collection;
    }

}

?>
