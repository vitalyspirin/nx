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
 *  in accessing database and cache connections.
 *
 *  @package lib
 */
class Connections {

   /**
    *  The collection of cache handlers.
    *
    *  @var array
    *  @access protected
    */
    protected static $_cache = array();

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
        'cache' => array(),
        'db'    => array()
    );

   /**
    *  The options for handlers.
    *
    *  @var array
    *  @access protected
    */
    protected static $_options = array(
        'cache' => array(),
        'db'    => array()
    );

   /**
    *  Stores the cache connection details using the defined options.
    *
    *  @see app\config\bootstrap\cache.php
    *  @param array $options    The cache configuration.  Should be of the
    *                           following format: 'key' => 'value',
    *                           where 'key' is the name of the configuration
    *                           (i.e., 'development', 'test', 'production'),
    *                           and 'value' is an array which can take
    *                           the following parameters:
    *                           'enabled'       - Whether or not the
    *                                             plugin should be used.
    *                           'plugin'        - The name of the cache
    *                                             plugin.
    *                           'host'          - The hostname of the server
    *                                             where the cache resides.
    *  @access public
    *  @return void
    */
    public static function add_cache($config = array()) {
        foreach ( $config as $name => $options ) {
            self::$_options['cache'][$name] = $options;
            self::$_initialized['cache'][$name] = false;
        }
    }

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
    *  Returns the cache handler.
    *
    *  @param string $name    The name of the cache handler.
    *  @access public
    *  @return object
    */
    public static function get_cache($name) {
        if ( !self::$_options['cache'][$name]['enabled'] ) {
            return false;
        }

        if ( !self::$_initialized['cache'][$name] ) {
            $plugin = self::$_options['cache'][$name]['plugin'];

            $options = self::$_options['cache'][$name];
            unset($options['enabled']);
            unset($options['plugin']);
            self::$_cache[$name] = new $plugin($options);

            self::$_initialized['cache'][$name] = true;
        }

        return self::$_cache[$name];
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
