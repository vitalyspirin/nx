<?php

/**
 * NX
 *
 * @author    Nick Sinopoli <NSinopoli@gmail.com>
 * @copyright Copyright (c) 2011, Nick Sinopoli
 * @license   http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace nx\core;

use nx\lib\Validator;

/*
 *  The `Model` class is the parent class of all application models.
 *  Responsible for handling object relationships, it provides automatic
 *  interfacing with both databases and caches to allow for convenient object
 *  storage and retrieval.  It also provides validation mechanisms.
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
    *  The meta information pertinent to objects and their relationships with
    *  other objects.
    *
    *  @var array
    *  @access protected
    */
    // TODO: Change this to just be $key
    protected $_meta = array('key' => 'id');

   /**
    *  The validators to be used when validating data.
    *
    *  @see /nx/lib/Validator
    *  @see /nx/core/Model->is_valid()
    *  @var array
    *  @access protected
    */
    protected $_validators = array();

   /**
    *  The validation error messages.
    *
    *  @see /nx/core/Model->is_valid()
    *  @var array
    *  @access protected
    */
    protected $_validation_errors = array();

   /**
    *  Initializes an object.  Takes the following configuration options:
    *  'db'       - The name of the db connection to use as defined
    *               in app/config/bootstrap/db.php.
    *  'cache'    - The name of the cache connection to use as defined
    *               in app/config/bootstrap/cache.php.
    *
    *  @param array $config        The configuration options.
    *  @access public
    *  @return void
    */
    public function __construct(array $config = array()) {
        $defaults = array(
            'libs' => array(
                'connections' => 'nx\lib\Connections'
            )
        );
        parent::__construct($config + $defaults);
    }

   /**
    *  Initializes an object.  Object properties are populated
    *  automatically - first by checking the cache, and then, if that
    *  fails, by retrieving the values from the database.
    *
    *  @access protected
    *  @return void
    */
    protected function _init() {
        $connections = $this->_config['libs']['connections'];
        $this->_db = $connections::get_db();
    }

   /**
    *  Returns an object's property.
    *
    *  @param string $field    The property name.
    *  @access public
    *  @return mixed
    */
    public function __get($field) {
        return $this->$field;
    }

   /**
    *  Deletes an object from both the cache and the database.
    *
    *  @access public
    *  @return bool
    */
    public function delete() {
        $key = $this->classname() . '_' . $this->get_primary_key();

        // TODO: FIX THIS ($where crap)
        $id = $this->get_primary_key();
        if ( is_null($id) ) {
            return false;
        }
        $where = array($this->_meta['key'] => $id);

        if ( !$this->_db->delete($this->classname(), $where) ) {
            return false;
        }
        return true;
    }

   /**
    *  Finds and returns an array of all the objects in the database that
    *  match the conditions provided in '$where'.
    *
    *  @param array $clauses    Any SQL clauses to be added to the query.
    *                           Takes the following keys:
    *                           'where'    - string
    *                           'distinct' - true|false
    *                           'limit'    - int
    *                           'order_by' - string
    *                           'group_by' - string
    *                           'having'   - string
    *  @param string $object    The name of the objects to retrieve.
    *  @access public
    *  @return array
    */
    public function find_all($clauses = array(), $object = null) {
        $object_name = ( is_null($object) ) ? $this->classname() : $object;
        $this->_db->find($this->_meta['key'], $object_name, $clauses);
        $rows = $this->_db->fetch_all('assoc');

        $object_name = Library::get('namespace', 'model') . $object_name;
        $collection = array();
        foreach ( $rows as $row ) {
            $new_id = $row[$this->_meta['key']];
            $collection[$new_id] = new $object_name(array('id' => $new_id));
        }
        return $collection;
    }

   /**
    *  Retrieves the "columns" (i.e., public properties) belonging to '$this'.
    *
    *  @access public
    *  @return array
    */
    // TODO: Cache these so that it's not being retrieved every time
    public function get_columns() {
        $reflection = new \ReflectionClass($this);
        $properties = $reflection->getProperties(\ReflectionProperty::IS_PUBLIC);
        $collection = array();
        foreach ( $properties as $property ) {
            $name = $property->getName();
            $collection[$name] = $property->getValue($object);
        }
        return $collection;
    }

   /**
    *  Returns the primary key associated with '$this'.
    *
    *  @access public
    *  @return int
    */
    public function get_primary_key() {
        $id = $this->_meta['key'];
        return $this->$id;
    }

   /**
    *  Returns the validation errors.
    *
    *  @param bool $flatten    Whether or not to return the errors as a
    *                          flattened array.
    *  @access public
    *  @return array
    */
    public function get_validation_errors($flatten = false) {
        if ( !$flatten ) {
            return $this->_validation_errors;
        }
        $errors = array();
        array_walk_recursive(
            $this->_validation_errors, function($a) use (&$errors) {
                $errors[] = $a;
            }
        );
        return $errors;
    }

   /**
    *  Retrieves the validators associated with a given property.
    *
    *  @param string $field    The object property.
    *  @access protected
    *  @return array
    */
    protected function _get_validators($field) {
        return ( isset($this->_validators[$field]) )
            ? $this->_validators[$field]
            : array();
    }

   /**
    *  Checks that a specific object property is valid.  If no property
    *  is supplied, then all of the object's "columns" (i.e., public
    *  properties) will be validated.  Corresponding errors are stored
    *  in $this->_validation_errors.
    *
    *  @see /nx/lib/Validator
    *  @param string|null $field    The object property to be validated.
    *  @access public
    *  @return bool
    */
    public function is_valid($field = null) {
        if ( empty($this->_validators) ) {
            return true;
        }

        if ( !is_null($field) ) {
            $this->_validation_errors = $this->_validate($field);
        } else {
            $this->_validation_errors = array();
            foreach ( array_keys($this->get_columns()) as $field ) {
                $this->_validation_errors += $this->_validate($field);
            }
        }
        return ( empty($this->_validation_errors) );
    }

   /**
    *  Loads an object, first by checking the cache, and then, if that
    *  fails, by retrieving the values from the database.
    *
    *  @param int $primary_key    The primary key.
    *  @access public
    *  @return object
    */
    public function load_by_primary_key($primary_key) {
        $where = array($this->_meta['key'] => $primary_key);
        $this->_db->find('*', $this->classname(), compact('where'));
        $this->_db->fetch('into', $this);

        return $this;
    }

    public function load() {

        if ( !is_numeric($identifier) ) {
            $clauses = array(
                'where' => $identifier,
                'limit' => 1
            );

            $this->_db->find($this->_meta['key'], $this->classname(), $clauses);
            $result = $this->_db->fetch('assoc');
            if ( !$result ) {
                return false;
            }

            $identifier = $result[$this->_meta['key']];
        }

    }

   /**
    *  Maps an array of data to '$this'.
    *
    *  @param array $data    The array of data, in the format of
    *                        array('property' => 'value'), where 'property' is
    *                        the object's property and 'value' is the object's
    *                        value.
    *  @access public
    *  @return bool
    */
    public function map($data) {
        foreach ( $data as $property => $value ) {
            $this->$property = $value;
        }
        return true;
    }

   /**
    *  Stores an object in both the database and the cache.
    *
    *  @param array $map    An array of data to be mapped to the object.  Note
    *                       that store() will store the entire object, and not
    *                       just the fields passed in to $map.
    *  @access public
    *  @return bool
    */
    public function store($map = array()) {
        if ( !empty($map) ) {
            $this->map($map);
        }

        if ( !$this->is_valid() ) {
            return false;
        }

        $this->_db->upsert($this->classname(), $this->get_columns());
        $id = $this->_meta['key'];
        if ( !$this->$id ) {
            $this->$id = $this->_db->insert_id();
        }
        return true;
    }

   /**
    *  Validates a property of an object in accordance with the validators
    *  defined in $this->_validators.  Returns an array of error messages.
    *
    *  @param string $field    The object property to be validated.
    *  @access protected
    *  @return array
    */
    protected function _validate($field) {
        $errors = array();
        $validators = $this->_get_validators($field);
        if ( empty($validators) ) {
            return $errors;
        }

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
                $valid = Validator::$method($this->$field, $validator['options']);
            } else {
                $valid = Validator::$method($this->$field);
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
