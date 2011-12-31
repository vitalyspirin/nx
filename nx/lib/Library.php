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
 *  The `Library` class is used to manage various
 *  aspects of application configuration.  It serves
 *  as the reference for environment information,
 *  namespace lookups, and path locations.
 *
 *  @package lib
 */
class Library {

   /**
    *  The configuration settings.
    *
    *  @var array
    *  @access protected
    */
    protected static $_config = array();

   /**
    *  Defines the library configuration.  Note that `$config`
    *  should be of the format `key` => `value`, where `value`
    *  is an array.
    *
    *  $config = array(
    *      'namespace' => array(
    *          'controller' => 'app\controller\\',
    *          'model'      => 'app\model\\'
    *      )
    *  );
    *
    *  @param array $config         The configuration options.
    *  @access public
    *  @return void
    */
    public static function define($config = array()) {
        self::$_config = $config;
    }

   /**
    *  Determines the environment (development, test, or
    *  production).
    *
    *  @access public
    *  @return string
    */
    public static function environment() {
        $local = array('::1', '127.0.0.1');
        $is_local = in_array($_SERVER['SERVER_ADDR'], $local);
        $uri = $_SERVER['REQUEST_URI'];
        $is_test = (preg_match('/^test\//', $uri) && $is_local)
            || preg_match('/^test/', $_SERVER['HTTP_HOST']);

        if ( $is_test ) {
            return 'test';
        } elseif ( $is_local ) {
            return 'development';
        }
        return 'production';
    }

   /**
    *  Retrieves a specific configuration setting.
    *
    *  @param string $key           The configuration key.
    *  @param string $type          The configuration type.
    *  @access public
    *  @return mixed
    */
    public static function get($key, $type) {
        return self::$_config[$key][$type];
    }

   /**
    *  Retrieves the application version.
    *
    *  @access public
    *  @return string
    */
    public static function version() {
        $version = self::$_config['version'];
        return $version['major'] . '.' . $version['minor']
            . '.' . $version['iteration'] . '.' . $version['status']
            . '.' . $version['revision'];
    }

}

?>
