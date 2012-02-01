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
 *  The `Form` class is used to generate common HTML elements.
 *  All helper creation methods accept an optional `$binding` parameter,
 *  which can be used to autopopulate an instance of that object
 *  with the element's values upon form submission.
 *
 *  @see /nx/lib/Request::extract_post()
 *  @package lib
 */
class Form {

    /**
     *  The configuration settings.
     *
     *  @var array
     *  @access protected
     */
    protected $_config = array();

   /**
    *  Loads the configuration settings.
    *
    *  @param array $config         The configuration options.
    *  @access public
    *  @return void
    */
    public function __construct(array $config = array()) {
        $defaults = array('version' => null);
        $this->_config = $config + $defaults;
    }

   /**
    * Creates a checkbox.
    *
    * @param array $attributes          The HTML attributes.
    * @param obj $binding               The object to which the value of
    *                                   the checkbox should be mapped.
    * @access public
    * @return string
    */
    public function checkbox($attributes, $binding = null) {
        $html = "<input type='checkbox' ";
        $html .= $this->_parse_attributes($attributes, $binding);

        if (
            !is_null($binding)
            && isset($attributes['name'])
            && isset($attributes['value'])
            && property_exists($binding, $attributes['name'])
            && $binding->$attributes['name'] == $attributes['value']
        ) {
            $html .= "checked='checked' ";
        }
        $html .= "/>";

        return $html;
    }

   /**
    * Creates a link relation for CSS.  Note that this method appends
    * the version number (if it was supplied to the constructor) to
    * the href (so as to prevent browser caching).
    *
    * @param array $attributes          The HTML attributes.
    * @access public
    * @return string
    */
    public function css($attributes) {
        $html = "<link rel='stylesheet' ";
        if ( !is_null($this->_config['version']) && isset($attributes['href']) ) {
            $attributes['href'] .= '?v=' . $this->_config['version'];
        }
        $html .= $this->_parse_attributes($attributes);
        $html .= "/>";

        return $html;
    }

   /**
    * Creates an email textbox.
    *
    * @param array $attributes          The HTML attributes.
    * @param obj $binding               The object to which the value of the
    *                                   textbox should be mapped.
    * @access public
    * @return string
    */
    public function email($attributes, $binding = null) {
        return $this->_input(array('type' => __FUNCTION__) + $attributes, $binding);
    }

   /**
    * Escapes a value for output in an HTML context.
    *
    * @param mixed $value
    * @access public
    * @return mixed
    */
    public function escape($value) {
        if ( is_array($value) ) {
            return array_map(array($this, __FUNCTION__), $value);
        }
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }

   /**
    * Creates a form.
    *
    * @param array $attributes          The HTML attributes.
    * @param obj $binding               The object to which the form will
    *                                   be mapped.
    * @access public
    * @return string
    */
    public function form($attributes, $binding = null) {
        $html = "<form ";

        if ( !is_null($binding) ) {
            if ( !is_null($binding->get_primary_key()) ) {
                $id = $binding->get_primary_key();
                $attributes += array(
                    'action' => '/' . strtolower($binding->classname()) . '/' . $id,
                    'method' => 'put'
                );
            } else {
                $attributes += array(
                    'action' => '/' . strtolower($binding->classname()),
                    'method' => 'post'
                );
            }
        }

        $html .= $this->_parse_attributes($attributes, $binding);
        $html .= "/>";

        return $html;
    }

   /**
    * Creates a hidden input.
    *
    * @param array $attributes          The HTML attributes.
    * @param obj $binding               The object to which the value of the
    *                                   hidden input should be mapped.
    * @access public
    * @return string
    */
    public function hidden($attributes, $binding = null) {
        return $this->_input(array('type' => __FUNCTION__) + $attributes, $binding);
    }

   /**
    * Creates an input field.
    *
    * @param array $attributes          The HTML attributes.
    * @param obj $binding               The object to which the value of the
    *                                   input should be mapped.
    * @access protected
    * @return string
    */
    protected function _input($attributes, $binding = null) {
        $html = "<input ";
        $html .= $this->_parse_attributes($attributes, $binding);
        $html .= "/>";

        return $html;
    }

   /**
    * Creates a number textbox.
    *
    * @param array $attributes          The HTML attributes.
    * @param obj $binding               The object to which the value of the textbox
    *                                   should be mapped.
    * @access public
    * @return string
    */
    public function number($attributes, $binding = null) {
        return $this->_input(array('type' => __FUNCTION__) + $attributes, $binding);
    }

   /**
    * Parses HTML attributes and binds an object's value to an element.
    *
    * @param array $attributes          The HTML attributes.
    * @param obj $binding               The object to which the value of the
    *                                   hidden input should be mapped.
    * @param array $options             The parsing options.  Takes the
    *                                   following keys:
    *                                   'ignore_binding_value' - whether or not
    *                                   to ignore the value attribute when a
    *                                   binding is present
    * @access protected
    * @return string
    */
    protected function _parse_attributes($attributes, $binding = null, $options = array()) {
        $options += array('ignore_binding_value' => false);
        $html = '';
        $value_present = false;
        foreach ( $attributes as $key => $setting ) {
            // An attribute passed alone without a key (e.g., array('autofocus'))
            // will be assigned a numeric key by PHP
            if ( is_numeric($key) ) {
                $html .= $this->escape($setting) . " ";
                continue;
            }

            switch ( $key ) {
                case 'name':
                    if ( is_null($binding) ) {
                        break;
                    }

                    $setting = $binding->classname() . '[' . $setting . "]";
                    break;
                case 'value':
                    $value_present = true;
                    break;
            }
            $html .= $this->escape($key) . "='" . $this->escape($setting) . "' ";
        }

        if (
            !$value_present
            && !$options['ignore_binding_value']
            && !is_null($binding)
            && isset($attributes['name'])
            && property_exists($binding, $attributes['name'])
            && !is_null($binding->$attributes['name'])
        ) {
            $html .= "value='" . $this->escape($binding->$attributes['name']) . "' ";
        }

        return $html;
    }

   /**
    * Creates a set of radio buttons.
    *
    * @param array $attributes          The HTML attributes.
    *                                   Key 'id' takes an array of ids, and
    *                                   key 'value' can be in the format of:
    *                                   'value' => 'display'
    *                                   (radio value will be set to 'value',
    *                                   and the text to the right of the
    *                                   radio will be set to 'display')
    *                                   or as an array without keys
    *                                   (both radio value and the text to the
    *                                   right of the radio will be set to the
    *                                   passed value)
    * @param obj $binding               The object to which the value of the
    *                                   radio buttons should be mapped.
    * @access public
    * @return string
    */
    public function radios($attributes, $binding = null) {
        $options = array('ignore_binding_value' => true);
        $html = '';
        $values = $attributes['value'];
        unset($attributes['value']);
        // Array is not associative
        if ( $values === array_values($values) ) {
            $values = array_combine($values, $values);
        }
        if ( isset($attributes['id']) ) {
            $ids = $attributes['id'];
            unset($attributes['id']);
        }
        $index = 0;
        foreach ( $values as $value => $display ) {
            $html .= "<input type='radio' ";
            $html .= $this->_parse_attributes($attributes, $binding, $options);
            $label_for = '';
            if ( isset($ids[$index]) ) {
                $html .= "id='" . strtolower($ids[$index]) . "' ";
                $label_for = " for='" . strtolower($ids[$index]) . "'";
            }
            $html .= "value='" . $this->escape($value) . "' ";
            if (
                !is_null($binding)
                && isset($attributes['name'])
                && property_exists($binding, $attributes['name'])
                && $binding->$attributes['name'] == $value
            ) {
                $html .= "checked='checked' ";
            }
            $html .= "/> ";
            $html .= "<label" . $label_for;
            $html .= ">" . $display . "</label>";
            $index++;
        }

        return $html;
    }

   /**
    * Creates a search textbox.
    *
    * @param array $attributes          The HTML attributes.
    * @param obj $binding               The object to which the value of the
    *                                   textbox should be mapped.
    * @access public
    * @return string
    */
    public function search($attributes, $binding = null) {
        return $this->_input(array('type' => __FUNCTION__) + $attributes, $binding);
    }

   /**
    * Creates a dropdown list.
    *
    * @param array $attributes          The HTML attributes.
    * @param array $options             The options with which to populate the
    *                                   dropdown list.
    *                                   Must be in the format of:
    *                                   'value' => 'display'
    *                                   (option value will be set to 'value',
    *                                   and the text within the option will be
    *                                   set to 'display')
    *                                   Note that this can accept a 'selected'
    *                                   attribute to set which element is
    *                                   selected.
    * @param obj $binding               The object to which the value of the
    *                                   dropdown list should be mapped.
    * @access public
    * @return string
    */
    public function select($attributes, $options = array(), $binding = null) {
        $selected = null;
        if ( isset($attributes['selected']) ) {
            $selected = $attributes['selected'];
            unset($attributes['selected']);
        }

        $element_options = array('ignore_binding_value' => true);
        $html = "<select ";
        $html .= $this->_parse_attributes($attributes, $binding, $element_options);
        $html = rtrim($html) . ">";
        // Array is not associative
        if ( $options === array_values($options) ) {
            $options = array_combine($options, $options);
        }
        foreach( $options as $value => $display ) {
            $html .= "<option value='" . $this->escape($value) . "' ";

            if (
                (!is_null($selected) && $selected == $value)
                ||
                (is_null($selected)
                && !is_null($binding)
                && isset($attributes['name'])
                && property_exists($binding, $attributes['name'])
                && $binding->$attributes['name'] == $value)
            ) {
                $html .= "selected='selected' ";
            }
            $html = rtrim($html) . ">" . $this->escape($display) . "</option>";
        }
        $html .= "</select>";

        return $html;
    }

   /**
    * Creates a textbox.
    *
    * @param array $attributes          The HTML attributes.
    * @param obj $binding               The object to which the value of the
    *                                   textbox should be mapped.
    * @access public
    * @return string
    */
    public function text($attributes, $binding = null) {
        return $this->_input(array('type' => __FUNCTION__) + $attributes, $binding);
    }

   /**
    * Creates a textarea.
    *
    * @param array $attributes          The HTML attributes.
    * @param obj $binding               The object to which the value of the
    *                                   textarea should be mapped.
    * @access public
    * @return string
    */
    public function textarea($attributes, $binding = null) {
        if ( isset($attributes['value']) ) {
            $value = $attributes['value'];
            unset($attributes['value']);
        }

        $options = array('ignore_binding_value' => true);
        $html = "<textarea ";
        $html .= $this->_parse_attributes($attributes, $binding, $options);
        $html = rtrim($html) . '>';
        if ( isset($value) ) {
            $html .= $this->escape($value);
        } elseif (
            !is_null($binding)
            && isset($attributes['name'])
            && property_exists($binding, $attributes['name'])
        ) {
            $html .= $this->escape($binding->$attributes['name']);
        }
        $html .= "</textarea>";

        return $html;
    }

}

?>
