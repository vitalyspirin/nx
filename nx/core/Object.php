<?php

/**
 * NX
 *
 * @author    Nick Sinopoli <NSinopoli@gmail.com>
 * @copyright Copyright (c) 2011-2012, Nick Sinopoli
 * @license   http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace nx\core;

/*
 *  The `Object` class is the class from which the other
 *  core classes inherit.  Rather than rely on complex
 *  method signatures for class instantiation, it provides
 *  a simple mechanism by which configuration settings
 *  can be passed to class constructors by means of an
 *  array, and optionally 'initializes' an object following
 *  construction.
 *
 *  @package core
 */
class Object {

   /**
    *  The object classname.
    *
    *  @var string
    *  @access protected
    */
    protected $_classname;

   /**
    *  The configuration settings.
    *
    *  @var array
    *  @access protected
    */
    protected $_config = array();

   /**
    *  Loads the configuration settings for the class and
    *  sets the classname.
    *
    *  @param array $config    The configuration options.
    *  @access public
    *  @return void
    */
    public function __construct(array $config = array()) {
        $defaults = array('init' => true);
        $this->_config = $config + $defaults;

        $class = explode('\\', get_called_class());
        $this->_classname = array_pop($class);

        if ( $this->_config['init'] && method_exists($this, '_init') ) {
            $this->_init();
        }
    }

   /**
    *  Returns the object's classname without the namespace.
    *
    *  @access public
    *  @return string
    */
    public function classname() {
        return $this->_classname;
    }

}

?>
