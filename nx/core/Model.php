<?php

/**
 * NX
 *
 * @author    Nick Sinopoli <NSinopoli@gmail.com>
 * @copyright Copyright (c) 2011, Nick Sinopoli
 * @license   http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace nx\core;

// TODO: Update this
/*
 *  The `Model` class is the parent class of all application models.
 *  It provides validation mechanisms.
 *
 *  @package core
 */
class Model extends Object {

   /**
    *  The validators to be used when validating data.
    *
    *  @see /nx/lib/Validator
    *  @var array
    *  @access protected
    */
    protected $_validators = array();

   /**
    *  TODO: FIX THIS
    *
    *  @param array $config        The configuration options.
    *  @access public
    *  @return void
    */
    public function __construct(array $config = array()) {
        $defaults = array(
            'libs' => array(
                'validator' => 'nx\lib\Validator'
            )
        );
        parent::__construct($config + $defaults);
    }

   /**
    *  Validates a property of an object in accordance with the validators
    *  defined in $this->_validators.  Returns an array of error messages.
    *
    *  @param string $field    The object property to be validated.
    *  @access protected
    *  @return array
    */
    public function is_valid($field) {
        if ( !isset($this->_validators[$field]) ) {
            return array();
        }
        $lib = $this->_config['libs']['validator'];
        $validators = $this->_validators[$field];
        $errors = array();

        foreach ( $validators as $validator ) {
            if (
                isset($validator['optional'])
                && $validator['optional'] === true
                && is_null($this->$field)
            ) {
                continue;
            }

            $method = $validator[0];
            if ( isset($validator['options']) ) {
                $valid = $lib::$method($this->$field, $validator['options']);
            } else {
                $valid = $lib::$method($this->$field);
            }

            if ( !$valid ) {
                if ( !isset($errors[$field]) ) {
                    $errors[$field] = array();
                }
                $errors[$field][] = $validator['message'];
            }
        }
        return $errors;
    }

}

?>
