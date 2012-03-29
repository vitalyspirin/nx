<?php

namespace app\lib;

/*
 *  The 'Connections' class contains methods that assist
 *  in accessing database connections.
 *
 *  @package lib
 */
class Connections {

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
        }
    }

   /**
    *  Returns the database options.
    *
    *  @param string $name    The name of the database handler.
    *  @access public
    *  @return object
    */
    public static function get_db($name) {
        return self::$_options['db'][$name];
    }
}

?>
