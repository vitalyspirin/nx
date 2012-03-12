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
    *  The database handler.
    *
    *  @var object
    *  @access protected
    */
    protected $_db;

   /**
    *  The validators to be used when validating data.
    *
    *  @see /nx/lib/Validator
    *  @var array
    *  @access protected
    */
    protected $_validators = array();

   /**
    *  Initializes an object.  Takes the following configuration options:
    *  'db'       - The name of the db connection to use as defined
    *               in app/config/bootstrap/db.php.
    *
    *  @param array $config        The configuration options.
    *  @access public
    *  @return void
    */
    public function __construct(array $config = array()) {
        $defaults = array(
            'libs' => array(
                'connections' => 'nx\lib\Connections',
                'validator'   => 'nx\lib\Validator'
            )
        );
        parent::__construct($config + $defaults);
    }

   /**
    *  Initializes an object.  Object properties are populated
    *  automatically by retrieving the values from the database.
    *
    *  @access protected
    *  @return void
    */
    protected function _init() {
        $connections = $this->_config['libs']['connections'];
        $this->_db = $connections::get_db();
    }

   /**
    *  Validates a property of an object in accordance with the validators
    *  defined in $this->_validators.  Returns an array of error messages.
    *
    *  @param string $field    The object property to be validated.
    *  @access protected
    *  @return array
    */
    public function validate($field) {
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
