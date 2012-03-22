<?php

namespace app\model;

class ApplicationModel extends \nx\core\Model {

   /**
    *  The 'belongs to' relationships pertaining to '$this'.
    *
    *  @var array
    *  @access protected
    */
    protected $_belongs_to = array();

   /**
    *  The database handler.
    *
    *  @var object
    *  @access protected
    */
    protected $_db;

   /**
    *  The 'has one' relationships pertaining to '$this'.
    *
    *  @var array
    *  @access protected
    */
    protected $_has_one = array();

   /**
    *  The 'has many' relationships pertaining to '$this'.
    *
    *  @var array
    *  @access protected
    */
    protected $_has_many = array();

   /**
    *  The 'has and belongs to many' relationships pertaining to '$this'.
    *
    *  @var array
    *  @access protected
    */
    protected $_has_and_belongs_to_many = array();


   /**
    *  Initializes an object.  Takes the following configuration options:
    *
    *  @param array $config        The configuration options.
    *  @access public
    *  @return void
    */
    public function __construct(array $config = array()) {
        $defaults = array(
            'has_and_belongs_to_many_separator' => '__',
            'libs'                              => array(
                'connections' => 'nx\lib\Connections'
            ),
            'primary_key'                       => 'id',
            'primary_key_separator'             => '_'
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
    *  Deletes an object from the database.
    *
    *  @access public
    *  @return bool
    */
    public function delete() {
        $primary_key = $this->get_primary_key();
        if ( is_null($primary_key) ) {
            return false;
        }

        if ( $this->_db->classname() == 'PDO_MySQL' ) {
            $sql = "DELETE FROM {$this->classname()} WHERE "
                . "{$this->_config['primary_key']} = ?";
            $bindings = array($primary_key);
            return $this->_db->query($sql, $bindings);
        }

        return false;
    }

   /**
    *  Returns an object's property.  If the property is bound to '$this'
    *  via an object relationship, the appropriate object (or array of objects)
    *  is returned.  If the property is an actual property belonging to '$this',
    *  it will be returned.
    *
    *  @param string $field    The property name.
    *  @access public
    *  @return mixed
    */
    public function __get($field) {
        if ( in_array($field, $this->_belongs_to) ) {
            return $this->_get_belongs_to($field);
        } elseif ( in_array($field, $this->_has_many) ) {
            return $this->_get_has_many($field);
        } elseif ( in_array($field, $this->_has_one) ) {
            return $this->_get_has_one($field);
        } elseif ( in_array($field, $this->_has_and_belongs_to_many) ) {
            return $this->_get_has_and_belongs_to_many($field);
        }

        return $this->$field;
    }

   /**
    *  Returns the object associated with '$this' via a "belongs to"
    *  relationship.
    *
    *  @param string $field    The object name.
    *  @access protected
    *  @return object
    */
    protected function _get_belongs_to($field) {
        if ( $this->_db->classname() == 'PDO_MySQL' ) {
            $primary_key = $this->_config['primary_key'];
            $foreign_key = "{$field}{$this->_config['primary_key_separator']}"
                . $primary_key;
            $sql = "SELECT * FROM {$this->classname()} "
                . "WHERE {$foreign_key} = ?";
            $bindings = array($this->$foreign_key);
            $this->_db->query($sql, $bindings);

            $object = new $field();
            $this->_db->fetch('into', $object);
            return $object;
        }
    }

   /**
    *  Returns an array of objects associated with '$this' via a
    *  "has and belongs to many" relationship.
    *
    *  @param string $field    The object name.
    *  @access protected
    *  @return array
    */
    protected function _get_has_and_belongs_to_many($field) {
        if ( $this->_db->classname() == 'PDO_MySQL' ) {
            $class = $this->classname();
            $table = ( $class < $field )
                ? $class . $this->_meta['has_and_belongs_to_many_separator']
                    . $field
                : $field . $this->_meta['has_and_belongs_to_many_separator']
                    . $class;

            $primary_key = $this->_config['primary_key'];
            $reference_key = "{$class}{$this->_config['primary_key_separator']}"
                . $primary_key;
            $foreign_key = "{$field}{$this->_config['primary_key_separator']}"
                . $primary_key;
            $sql = "SELECT {$field}.* FROM {$field} "
                . "JOIN {$table} ON "
                . "{$field}.{$primary_key} = {$table}.{$foreign_key} "
                . "WHERE {$table}.{$reference_key} = ?";
            $bindings = array($this->$primary_key);
            $this->_db->query($sql, $bindings);

            $rows = $this->_db->fetch_all('assoc');
            $objects = array();
            foreach ( $rows as $row ) {
                $object = new $field();
                $objects[] = $object->map($row);
            }
            return $objects;
        }
    }

   /**
    *  Returns an array of objects associated with '$this' via a "has many"
    *  relationship.
    *
    *  @param string $field    The object name.
    *  @access protected
    *  @return array
    */
    protected function _get_has_many($field) {
        if ( $this->_db->classname() == 'PDO_MySQL' ) {
            $primary_key = $this->_config['primary_key'];
            $foreign_key = $this->classname()
                . "{$this->_config['primary_key_separator']}{$primary_key}";
            $sql = "SELECT * FROM {$field} WHERE {$foreign_key} = ? ORDER BY "
                . "{$primary_key} DESC";
            $bindings = array($this->get_primary_key());
            $this->_db->query($sql, $bindings);

            $rows = $this->_db->fetch_all('assoc');
            $objects = array();
            foreach ( $rows as $row ) {
                $object = new $field();
                $objects[] = $object->map($row);
            }
            return $objects;
        }
    }

   /**
    *  Returns the object associated with '$this' via a "has one" relationship.
    *
    *  @param string $field    The object name.
    *  @access protected
    *  @return object
    */
    protected function _get_has_one($field) {
        if ( $this->_db->classname() == 'PDO_MySQL' ) {
            $primary_key = $this->_config['primary_key'];
            $foreign_key = $this->classname()
                . "{$this->_config['primary_key_separator']}{$primary_key}";
            $sql = "SELECT * FROM {$field} WHERE {$foreign_key} = ? LIMIT 1";
            $bindings = array($this->get_primary_key());
            $this->_db->query($sql, $bindings);

            $object = new $field();
            $this->_db->fetch('into', $object);
            return $object;
        }
    }

   /**
    *  Returns the primary key associated with '$this'.
    *
    *  @access public
    *  @return int
    */
    public function get_primary_key() {
        $primary_key = $this->_config['primary_key'];
        return $this->$primary_key;
    }

   /**
    *  Loads an object ($this) from the database.
    *
    *  @param int $key    The primary key.
    *  @access public
    *  @return object
    */
    public function load_by_primary_key($key) {
        if ( $this->_db->classname() == 'PDO_MySQL' ) {
            $primary_key = $this->_config['primary_key'];
            $sql = "SELECT * FROM {$this->classname()} "
                . "WHERE {$primary_key} = ?";
            $bindings = array($key);
            $this->_db->query($sql, $bindings);

            $this->_db->fetch('into', $this);
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
    *  @return obj
    */
    public function map($data, $object = null) {
        foreach ( $data as $property => $value ) {
            $this->$property = $value;
        }
        return $this;
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

        // TODO: Define is_valid() here, iterate over object properties and
        // check upstairs is_valid()
        if ( !$this->is_valid() ) {
            return false;
        }

        $this->_db->upsert($this->classname(), get_object_vars($this));
        $primary_key = $this->_config['primary_key'];
        if ( !$this->$primary_key ) {
            $this->$primary_key = $this->_db->insert_id();
        }
        return true;
    }

}

?>
