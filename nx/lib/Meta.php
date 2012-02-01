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
    *  Returns the classname without the namespace.
    *
    *  @param object|string $object  The object or class name from which
    *                                to retrieve the classname.
    *  @access public
    *  @return string
    */
    public static function classname_only($object) {
        if ( !is_object($object) && !is_string($object) ) {
            return false;
        }

        $class = ( is_string($object) ) ? $object : get_class($object);
        $class = explode('\\', $class);
        return array_pop($class);
    }

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

   /**
    *  Returns all of the protected methods in a given class.
    *
    *  @param object $object         The object from which to
    *                                retrieve the methods.
    *  @access public
    *  @return array
    */
    public static function get_protected_methods($object) {
        $reflection = new \ReflectionClass($object);
        $methods = $reflection->getMethods(\ReflectionMethod::IS_PROTECTED);
        $collection = array();
        foreach ( $methods as $method ) {
            $collection[] = $method->getName();
        }
        return $collection;
    }

}

?>
