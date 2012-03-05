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
 *  The `Form` class is used to generate common HTML elements.
 *
 *  @package lib
 */
class Form {

   /**
    *  The form binding.
    *
    *  @var object
    *  @access protected
    */
    protected static $_binding;

   /**
    * Checks whether the supplied property exists within the the form binding
    * and has the supplied value.
    *
    * @param string $property    The property.
    * @param mixed $value        The value.
    * @access public
    * @return string
    */
    protected static function _binding_has_value($property, $value) {
        return (
            !is_null(self::$_binding)
            && property_exists(self::$_binding, $property)
            && self::$_binding->$property == $value
        );
    }

   /**
    * Creates a checkbox.
    *
    * @param array $attributes    The HTML attributes.
    * @access public
    * @return string
    */
    public static function checkbox($attributes, $checked = null) {
        if (
            !is_null(self::$_binding)
            && property_exists(self::$_binding, $attributes['name'])
        ) {
            $attributes['value'] = self::$_binding->$attributes['name'];
        }

        $html = "<input type='checkbox' ";
        $html .= self::_parse_attributes($attributes + compact('value'));
        if ( self::_binding_has_value($attributes['name'], $checked) ) {
            $html .= "checked='checked' ";
        }
        $html .= "/>";

        return $html;
    }

   /**
    * Creates an email textbox.
    *
    * @param array $attributes    The HTML attributes.
    * @access public
    * @return string
    */
    public static function email($attributes) {
        return self::_input(array('type' => __FUNCTION__) + $attributes);
    }

   /**
    * Escapes a value for output in an HTML context.
    *
    * @param mixed $value    The value to escape.
    * @access public
    * @return string
    */
    public static function escape($value) {
        if ( is_array($value) ) {
            return array_map(array(__CLASS__, __FUNCTION__), $value);
        }
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }

   /**
    * Creates a form.
    *
    * @param array $attributes    The HTML attributes.
    * @param obj $binding         The object to which the form will be mapped.
    * @access public
    * @return string
    */
    public static function form($attributes, $binding = null) {
        $html = "<form ";
        self::$_binding = $binding;

        if ( !is_null($binding) ) {
            if ( $id = $binding->get_primary_key() ) {
                $attributes += array(
                    'action' => '/' . strtolower($binding->classname())
                        . '/' . $id,
                    'method' => 'put'
                );
            } else {
                $attributes += array(
                    'action' => '/' . strtolower($binding->classname()),
                    'method' => 'post'
                );
            }
        }

        $html .= self::_parse_attributes($attributes);
        $html .= "/>";

        return $html;
    }

   /**
    * Creates a hidden input.
    *
    * @param array $attributes    The HTML attributes.
    * @access public
    * @return string
    */
    public static function hidden($attributes) {
        return self::_input(array('type' => __FUNCTION__) + $attributes);
    }

   /**
    * Creates an input field.
    *
    * @param array $attributes    The HTML attributes.
    * @access protected
    * @return string
    */
    protected static function _input($attributes) {
        if (
            !is_null(self::$_binding)
            && property_exists(self::$_binding, $attributes['name'])
        ) {
            $attributes['value'] = self::$_binding->$attributes['name'];
        }

        $html = "<input ";
        $html .= self::_parse_attributes($attributes);
        $html .= "/>";

        return $html;
    }

   /**
    * Creates a number textbox.
    *
    * @param array $attributes    The HTML attributes.
    * @access public
    * @return string
    */
    public static function number($attributes) {
        return self::_input(array('type' => __FUNCTION__) + $attributes);
    }

   /**
    * Parses and escapes HTML attributes.
    *
    * @param array $attributes    The HTML attributes.
    * @access protected
    * @return string
    */
    protected static function _parse_attributes($attributes) {
        $html = '';
        foreach ( $attributes as $key => $setting ) {
            // An attribute passed alone without a key (e.g., array('autofocus'))
            // will be assigned a numeric key by PHP
            if ( is_numeric($key) ) {
                $key = $setting;
            }
            $html .= self::escape($key) . "='" . self::escape($setting) . "' ";
        }
        return $html;
    }

   /**
    * Creates a set of radio buttons.
    *
    * @param array $attributes    The HTML attributes.
    * @param array $values        The values to use for the radios.  Can be in
    *                             the format of:
    *                             'value' => 'display'
    *                             (radio value will be set to 'value', and the
    *                             text to the right of the radio will be set
    *                             to 'display')
    *                             or as an array without keys
    *                             (both radio value and the text to the right of
    *                             the radio will be set to the passed value)
    * @access public
    * @return string
    */
    public static function radios($attributes, $values = array()) {
        $html = '';
        // Array is not associative
        if ( $values === array_values($values) ) {
            $values = array_combine($values, $values);
        }
        foreach ( $values as $value => $display ) {
            $html .= "<input type='radio' ";
            $id = $attributes['name'] . '__' . str_replace(' ', '_', $value);
            $attributes['id'] = strtolower($id);
            $html .= self::_parse_attributes($attributes);
            $html .= "value='" . self::escape($value) . "' ";
            if ( self::_binding_has_value($attributes['name'], $value) ) {
                $html .= "checked='checked' ";
            }
            $html .= "/> ";
            $html .= "<label for='" . $attributes['id'] . "'>";
            $html .= $display . "</label>";
        }

        return $html;
    }

   /**
    * Creates a search textbox.
    *
    * @param array $attributes    The HTML attributes.
    * @access public
    * @return string
    */
    public static function search($attributes) {
        return self::_input(array('type' => __FUNCTION__) + $attributes);
    }

   /**
    * Creates a dropdown list.
    *
    * @param array $attributes    The HTML attributes.
    * @param array $options       The options with which to populate the
    *                             dropdown list.  Can be in the format of:
    *                             'value' => 'display'
    *                             (option value will be set to 'value', and the
    *                             text within the option will be set to
    *                             'display')
    *                             or as an array without keys
    *                             (both option value and the text within the
    *                             option will be set to the passed value)
    * @access public
    * @return string
    */
    public static function select($attributes, $options = array()) {
        $html = "<select ";
        $html .= self::_parse_attributes($attributes);
        $html = rtrim($html) . ">";
        // Array is not associative
        if ( $options === array_values($options) ) {
            $options = array_combine($options, $options);
        }
        foreach( $options as $value => $display ) {
            $html .= "<option value='" . self::escape($value) . "' ";

            if ( self::_binding_has_value($attributes['name'], $value) ) {
                $html .= "selected='selected' ";
            }
            $html = rtrim($html) . ">" . self::escape($display) . "</option>";
        }
        $html .= "</select>";

        return $html;
    }

   /**
    * Creates a textbox.
    *
    * @param array $attributes    The HTML attributes.
    * @access public
    * @return string
    */
    public static function text($attributes) {
        return self::_input(array('type' => __FUNCTION__) + $attributes);
    }

   /**
    * Creates a textarea.
    *
    * @param array $attributes    The HTML attributes.
    * @param string $default      The default text.
    * @access public
    * @return string
    */
    public static function textarea($attributes, $default = null) {
        $html = "<textarea ";
        $html .= self::_parse_attributes($attributes);
        $html = rtrim($html) . '>';
        if ( isset($default) ) {
            $html .= self::escape($default);
        } elseif (
            !is_null(self::$_binding)
            && property_exists(self::$_binding, $attributes['name'])
        ) {
            $html .= self::escape(self::$_binding->$attributes['name']);
        }
        $html .= "</textarea>";

        return $html;
    }

}

?>
