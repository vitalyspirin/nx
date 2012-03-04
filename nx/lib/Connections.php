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
 *  The `Connections` class contains methods that assist
 *  in accessing database and cache connections.
 *
 *  @package lib
 */
class Connections {

   /**
    *  The cache handler.
    *
    *  @var array
    *  @access protected
    */
    protected static $_cache;

   /**
    *  The database handler.
    *
    *  @var array
    *  @access protected
    */
    protected static $_db;

   /**
    *  The initialization status of the handlers.
    *
    *  @var array
    *  @access protected
    */
    protected static $_initialized = array(
        'cache' => false,
        'db'    => false
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
    *  Returns the cache handler.
    *
    *  @access public
    *  @return object
    */
    public static function get_cache() {
        if ( !self::$_options['cache']['enabled'] ) {
            return false;
        }

        if ( !self::$_initialized['cache'] ) {
            $plugin = self::$_options['cache']['plugin'];
            $cache = 'nx\plugin\cache\\' . $plugin;

            $options = self::$_options['cache'];
            unset($options['enabled']);
            unset($options['plugin']);
            self::$_cache = new $cache($options);

            self::$_initialized['cache'] = true;
        }

        return self::$_cache;
    }

   /**
    *  Returns the database handler.
    *
    *  @access public
    *  @return object
    */
    public static function get_db() {
        if ( !self::$_initialized['db'] ) {
            $plugin = self::$_options['db']['plugin'];
            $db = 'nx\plugin\db\\' . $plugin;

            $options = self::$_options['db'];
            unset($options['plugin']);
            self::$_db = new $db($options);

            self::$_initialized['db'] = true;
        }

        return self::$_db;
    }

   /**
    *  Stores the cache connection details using the defined options.
    *
    *  @see app\config\bootstrap\cache.php
    *  @param array $options    The cache configuration.  Should be of the
    *                           following format: `key` => `value`,
    *                           where `key` is the name of the configuration
    *                           (i.e., 'development', 'test', 'production'),
    *                           and `value` is an array which can take
    *                           the following parameters:
    *                           `enabled`       - Whether or not the
    *                                             plugin should be used.
    *                           `plugin`        - The name of the cache
    *                                             plugin.
    *                           `host`          - The hostname of the server
    *                                             where the cache resides.
    *                           `persistent_id` - A unique ID used to allow
    *                                             persistence between
    *                                             requests.
    *  @access public
    *  @return void
    */
    public static function set_cache($config = array()) {
        self::$_options['cache'] = $config;
        self::$_initialized['cache'] = false;
    }

   /**
    *  Stores the database connection details using the defined options.
    *
    *  @see app\config\bootstrap\db.php
    *  @param array $config    The database configuration.  Should be of
    *                          the following format: `key` => `value`,
    *                          where `key` is the name of the configuration
    *                          (i.e., 'development', 'test', 'production'),
    *                          and `value` is an array which can take the
    *                          following parameters:
    *                          `plugin`   - The name of the plugin.
    *                          `database` - The database name.
    *                          `host`     - The database host.
    *                          `username` - The database username.
    *                          `password` - The database password.
    *  @access public
    *  @return void
    */
    public static function set_db($config = array()) {
        self::$_options['db'] = $config;
        self::$_initialized['db'] = false;
    }
}

?>
