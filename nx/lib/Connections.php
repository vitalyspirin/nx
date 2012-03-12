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
 *  The 'Connections' class contains methods that assist
 *  in accessing database connections.
 *
 *  @package lib
 */
class Connections {

   /**
    *  The collection of database handlers.
    *
    *  @var array
    *  @access protected
    */
    protected static $_db = array();

   /**
    *  The initialization status of the handlers.
    *
    *  @var array
    *  @access protected
    */
    protected static $_initialized = array(
        'db' => array()
    );

   /**
    *  The options for handlers.
    *
    *  @var array
    *  @access protected
    */
    protected static $_options = array(
        'db' => array()
    );

   /**
    *  Stores the database connection details using the defined options.
    *
    *  @see app\config\bootstrap\db.php
    *  @param array $config    The database configuration.  Should be of
    *                          the following format: 'key' => 'value',
    *                          where 'key' is the name of the configuration
    *                          (i.e., 'development', 'test', 'production'),
    *                          and 'value' is an array which can take the
    *                          following parameters:
    *                          'plugin'   - The name of the plugin.
    *                          'database' - The database name.
    *                          'host'     - The database host.
    *                          'username' - The database username.
    *                          'password' - The database password.
    *  @access public
    *  @return void
    */
    public static function add_db($config = array()) {
        foreach ( $config as $name => $options ) {
            self::$_options['db'][$name] = $options;
            self::$_initialized['db'][$name] = false;
        }
    }

   /**
    *  Returns the database handler.
    *
    *  @param string $name    The name of the database handler.
    *  @access public
    *  @return object
    */
    public static function get_db($name) {
        if ( !self::$_initialized['db'][$name] ) {
            $plugin = self::$_options['db'][$name]['plugin'];

            $options = self::$_options['db'][$name];
            unset($options['plugin']);
            self::$_db[$name] = new $plugin($options);

            self::$_initialized['db'][$name] = true;
        }

        return self::$_db[$name];
    }
}

?>
