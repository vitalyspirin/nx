<?php

/**
 * NX
 *
 * @author    Nick Sinopoli <NSinopoli@gmail.com>
 * @copyright Copyright (c) 2011, Nick Sinopoli
 * @license   http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace nx\core;

use nx\lib\Connections;
use nx\lib\Data;
use nx\lib\Library;
use nx\lib\Meta;
use nx\lib\Validator;

/*
 *  The `Model` class is the parent class of all
 *  application models.  Responsible for handling
 *  object relationships, it provides automatic
 *  interfacing with both databases and caches to
 *  allow for convenient object storage and retrieval.
 *  It also provides validation mechanisms.
 *
 *  @package core
 */
class Model extends Object {

   /**
    *  The cache handler.
    *
    *  @var object
    *  @access protected
    */
    protected $_cache;

   /**
    *  The database handler.
    *
    *  @var object
    *  @access protected
    */
    protected $_db;

   /**
    *  The `belongs to` relationships pertaining to `$this`.
    *
    *  @var array
    *  @access protected
    */
    protected $_belongs_to = array();

   /**
    *  The `has one` relationships pertaining to `$this`.
    *
    *  @var array
    *  @access protected
    */
    protected $_has_one = array();

   /**
    *  The `has many` relationships pertaining to `$this`.
    *
    *  @var array
    *  @access protected
    */
    protected $_has_many = array();

   /**
    *  The `has and belongs to many` relationships pertaining to `$this`.
    *
    *  @var array
    *  @access protected
    */
    protected $_has_and_belongs_to_many = array();

   /**
    *  The meta information pertinent to objects
    *  and their relationships with other objects.
    *
    *  @var array
    *  @access protected
    */
    protected $_meta = array(
        'key'                               => 'id',
        'primary_key_separator'             => '_',
        'has_and_belongs_to_many_separator' => '__'
    );

   /**
    *  The validators to be used when validating
    *  data.
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
    *  `no_cache` - Whether or not to use the cache to store/retrieve the object.
    *  `db`       - The name of the db connection to use as defined
    *               in app/config/bootstrap/db.php.
    *  `cache`    - The name of the cache connection to use as defined
    *               in app/config/bootstrap/cache.php.
    *
    *  @see /nx/core/Model->_init()
    *  @param array $config        The configuration options.
    *  @access public
    *  @return void
    */
    public function __construct(array $config = array()) {
        $environment = Library::environment();
        $defaults = array(
            'no_cache' => false,
            'db'       => $environment,
            'cache'    => $environment,
        );
        parent::__construct($config + $defaults);
    }

   /**
    *  Initializes an object.  Object properties are populated
    *  automatically - first by checking the cache, and then, if that
    *  fails, by retrieving the values from the database.
    *
    *  @see /nx/core/Model->__construct()
    *  @access protected
    *  @return void
    */
    protected function _init() {
        parent::_init();

        $this->_db = Connections::get_db($this->_config['db']);
        if ( !$this->_cache = Connections::get_cache($this->_config['cache']) ) {
            $this->_config['no_cache'] = true;
        }
    }

   /**
    *  Returns an object's property.  If the property is bound to `$this`
    *  via an object relationship, the appropriate object (or array of objects)
    *  is returned.  If the property is an actual property belonging to `$this`,
    *  it will be returned.
    *
    *  @see /nx/core/Model->_get_belongs_to()
    *  @see /nx/core/Model->_get_has_many()
    *  @see /nx/core/Model->_get_has_one()
    *  @see /nx/core/Model->_get_has_and_belongs_to_many()
    *  @param string $field        The property name.
    *  @access public
    *  @return mixed
    */
    public function __get($field) {
        if ( $this->belongs_to($field) ) {
            return $this->_get_belongs_to($field);
        } elseif ( $this->has_many($field) ) {
            return $this->_get_has_many($field);
        } elseif ( $this->has_one($field) ) {
            return $this->_get_has_one($field);
        } elseif ( $this->has_and_belongs_to_many($field) ) {
            return $this->_get_has_and_belongs_to_many($field);
        }

        return $this->$field;
    }

   /**
    *  Checks if `$this` has a "belongs to" relationship with the
    *  object defined in `$field`.
    *
    *  @param string $field        The class name of the foreign object.
    *  @access public
    *  @return bool
    */
    public function belongs_to($field) {
        return ( in_array($field, $this->_belongs_to) );
    }

   /**
    *  Stores an object in the cache.  Only the object's "columns"
    *  (i.e., public properties) are serialized and stored.
    *
    *  @see /nx/lib/Meta::get_public_properties()
    *  @access public
    *  @return bool
    */
    public function cache() {
        if ( $this->_config['no_cache'] ) {
            return false;
        }

        $properties = $this->get_columns();
        $data = json_encode($properties);

        $key = $this->classname() . '_' . $this->get_primary_key();
        return $this->_cache->store($key, $data);
    }

   /**
    *  Deletes an object from both the cache and the database.
    *
    *  @param string|array $where  The WHERE clause to be included in the
    *                              DELETE query.
    *  @access public
    *  @return bool
    */
    public function delete($where = null) {
        $key = $this->classname() . '_' . $this->get_primary_key();

        if ( !$this->_config['no_cache'] ) {
            $this->_cache->delete($key);
        }

        if ( is_null($where) ) {
            $id = $this->get_primary_key();
            if ( is_null($id) ) {
                return false;
            }
            $where = array($this->_meta['key'] => $id);
        }

        if ( !$this->_db->delete($this->classname(), $where) ) {
            return false;
        }
        return true;
    }

   /**
    *  Finds and returns an array of all the objects in the
    *  database that match the conditions provided in `$where`.
    *
    *  @param array $clauses       Any SQL clauses to be added to
    *                              the query.  Takes the following keys:
    *                              'where'    - string
    *                              'distinct' - true|false
    *                              'limit'    - int
    *                              'order_by' - string
    *                              'group_by' - string
    *                              'having'   - string
    *  @param string $object       The name of the objects to retrieve.
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
    *  Returns the object associated with `$this` via a
    *  "belongs to" relationship.
    *
    *  @param string $field        The object name.
    *  @access protected
    *  @return object
    */
    protected function _get_belongs_to($field) {
        $lookup_id = $field . $this->_meta['primary_key_separator']
            . $this->_meta['key'];
        $object_id = $this->$lookup_id;

        $object_name = Library::get('namespace', 'model') . $field;
        return new $object_name(array('id' => $object_id));
    }

   /**
    *  Retrieves the "columns" (i.e., public properties) belonging
    *  to `$this`.
    *
    *  @access public
    *  @return array
    */
    public function get_columns() {
        return Meta::get_public_properties($this);
    }

   /**
    *  Returns an array of objects associated with `$this` via a
    *  "has and belongs to many" relationship.
    *
    *  @param string $field        The object name.
    *  @access protected
    *  @return array
    */
    protected function _get_has_and_belongs_to_many($field) {
        $class_name = $this->classname();
        if ( $class_name < $field ) {
            $table_name = $class_name
                . $this->_meta['has_and_belongs_to_many_separator'] . $field;
        } else {
            $table_name = $field
                . $this->_meta['has_and_belongs_to_many_separator']
                . $class_name;
        }

        $id = $this->_meta['key'];
        $lookup_id = $class_name . $this->_meta['primary_key_separator'] . $id;
        $where = array($lookup_id => $this->get_primary_key());

        $target_id = $field . $this->_meta['primary_key_separator'] . $id;
        $this->_db->find($target_id, $table_name, compact('where'));

        $object_name = Library::get('namespace', 'model') . $field;
        $rows = $this->_db->fetch_all('assoc');
        $collection = array();
        foreach ( $rows as $row ) {
            $new_id = $row[$target_id];
            $collection[$new_id] = new $object_name(array('id' => $new_id));
        }
        return $collection;
    }

   /**
    *  Returns an array of objects associated with `$this` via a
    *  "has many" relationship.
    *
    *  @param string $field        The object name.
    *  @access protected
    *  @return array
    */
    protected function _get_has_many($field) {
        $id = $this->_meta['key'];
        $lookup_id = $this->classname() . $this->_meta['primary_key_separator']
            . $id;
        $clauses = array(
            'where'    => array($lookup_id => $this->get_primary_key()),
            'order_by' => $id . ' DESC'
        );

        return $this->find_all($clauses, $field);
    }

   /**
    *  Returns the object associated with `$this` via a
    *  "has one" relationship.
    *
    *  @param string $field        The object name.
    *  @access protected
    *  @return object
    */
    protected function _get_has_one($field) {
        $id = $this->_meta['key'];
        $lookup_id = $this->classname() . $this->_meta['primary_key_separator']
            . $id;
        $clauses = array(
            'where' => array($lookup_id => $this->get_primary_key()),
            'limit' => 1
        );

        $this->_db->find($this->_meta['key'], $field, $clauses);
        $result = $this->_db->fetch('assoc');
        $object_id = $result[$id];

        $object_name = Library::get('namespace', 'model') . $field;

        return new $object_name(array('id' => $object_id));
    }

   /**
    *  Returns the primary key associated with `$this`.
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
    *  @param bool $flatten         Whether or not to return the errors as
    *                               a flattened array.
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
    *  @param string $field        The object property.
    *  @access protected
    *  @return array
    */
    protected function _get_validators($field) {
        return ( isset($this->_validators[$field]) )
            ? $this->_validators[$field]
            : array();
    }

   /**
    *  Checks if `$this` has a "has and belongs to many"
    *  relationship with the object defined in `$field`.
    *
    *  @param string $field        The class name of the foreign object.
    *  @access public
    *  @return bool
    */
    public function has_and_belongs_to_many($field) {
        return ( in_array($field, $this->_has_and_belongs_to_many) );
    }

   /**
    *  Checks if `$this` has a "has many" relationship with the
    *  object defined in `$field`.
    *
    *  @param string $field        The class name of the foreign object.
    *  @access public
    *  @return bool
    */
    public function has_many($field) {
        return ( in_array($field, $this->_has_many) );
    }

   /**
    *  Checks if `$this` has a "has one" relationship with the
    *  object defined in `$field`.
    *
    *  @param string $field        The class name of the foreign object.
    *  @access public
    *  @return bool
    */
    public function has_one($field) {
        return ( in_array($field, $this->_has_one) );
    }

   /**
    *  Checks that a specific object property is valid.  If no property
    *  is supplied, then all of the object's "columns" (i.e., public
    *  properties) will be validated.  Corresponding errors are stored
    *  in $this->_validation_errors.
    *
    *  @see /nx/lib/Validator
    *  @param string|null $field   The object property to be validated.
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
    *  @param int $primary_key        The primary key.
    *  @access public
    *  @return object
    */
    public function load_by_primary_key($primary_key) {
        if ( $this = $this->pull_from_cache($this, $primary_key) ) {
            return $this;
        }

        $where = array($this->_meta['key'] => $primary_key);
        $this->_db->find('*', $this->classname(), compact('where'));
        $this->_db->fetch('into', $this);

        $this->cache();

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
    *  Maps an array of data to `$this`.
    *
    *  @param array $data          The array of data, in the format of
    *                              array('property' => 'value'),
    *                              where 'property' is the object's property
    *                              and 'value' is the object's value.
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
    *  Retrieves an object from the cache.
    *
    *  @param object $object       The object to be populated with the
    *                              retrieved values.
    *  @param int $id              The unique identifier of the object
    *                              to be retrieved.
    *  @access public
    *  @return object
    */
    public function pull_from_cache($object, $id) {
        if ( $this->_config['no_cache'] ) {
            return false;
        }

        $key = $object->classname() . '_' . $id;
        $cached_data = $object->_cache->retrieve($key);
        if ( !$cached_data ) {
            return false;
        }

        $cached_object = json_decode($cached_data, true);
        foreach ( $cached_object as $key => $val ) {
            $object->$key = $val;
        }
        return $object;
    }

   /**
    *  Stores an object in both the database and the cache.
    *
    *  @see /nx/core/Model->map()
    *  @param array $map           An array of data to be mapped to the
    *                              object.  Note that store() will store
    *                              the entire object, and not just the fields
    *                              passed in to $map.
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
        $this->cache();
        return true;
    }

   /**
    *  Validates a property of an object in accordance with the
    *  validators defined in $this->_validators.  Returns an array
    *  of error messages.
    *
    *  @param string $field        The object property to be validated.
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
