<?php

/**
 * NX
 *
 * @author    Nick Sinopoli <NSinopoli@gmail.com>
 * @copyright Copyright (c) 2011, Nick Sinopoli
 * @license   http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace nx\lib;

use nx\lib\File;

/*
 *  The `Dispatcher` class is used to handle url routing and
 *  page rendering.
 *
 *
 *  @package lib
 */
class Dispatcher {

   /**
    *  Checks to see if a controller is whitelisted for use.
    *
    *  @param string $controller          The controller name.
    *  @access public
    *  @return bool
    */
    public static function is_whitelisted($controller) {
        // TODO: Fix this, get rid of the NX_ROOT constant
        $whitelist = File::get_filenames_within(NX_ROOT . '/app/controller');
        $strip_ext = create_function('$val', 'return basename($val, ".php");');
        $whitelist = array_map($strip_ext, $whitelist);
        return in_array($controller, $whitelist);
    }

   /**
    *  Parses a query string and returns the controller, action, 
    *  id, and any additional arguments passed via $_GET. 
    *
    *  @param string $query_string        The controller name.
    *  @access public
    *  @return array
    */
    public static function parse_query_string($query_string) {

        parse_str($query_string, $query);

        $controller = ( isset($query['controller']) ) ? ucfirst($query['controller']) : DEFAULT_CONTROLLER;
        $action =     ( isset($query['action']) )     ? $query['action']              : DEFAULT_ACTION;
        $id =         ( isset($query['id']) )         ? $query['id']                  : null;

        $get = array();
        if ( isset($query['args']) && $query['args'] != '' ) {
            $args = substr($query_string, strpos($query_string, $query['args']));
            parse_str($args, $get);
        }
        return compact('controller', 'action', 'id', 'get');
    }

   /**
    *  Renders a page.
    *
    *  @see nx\lib\Dispatcher::parse_query_string()
    *  @param array $args                 The data parsed from the query string.
    *  @access public
    *  @return bool
    */
    public static function render($args) {
        // URL layout
        // foobar.com/
        // foobar.com/controller[?args]
        // foobar.com/controller/id[?args]
        // foobar.com/controller/action[?args]
        // foobar.com/controller/action/id[?args]
        /* 
        rewrite ^/$ index.php;
        rewrite ^/([A-Za-z0-9\-]+)/?$ index.php?controller=$1&args=$args? break;
        rewrite ^/([A-Za-z0-9\-]+)/([\d]+)/?$ index.php?controller=$1&id=$2&args=$args? break;
        rewrite ^/([A-Za-z0-9\-]+)/([A-Za-z0-9\-_]+)/?$ index.php?controller=$1&action=$2&args=$args? break;
        rewrite ^/([A-Za-z0-9\-]+)/([A-Za-z0-9\-_]+)/([\d]+)/?$ index.php?controller=$1&action=$2&id=$3&args=$args? break;
        */

        if ( !self::is_whitelisted($args['controller']) ) {
            self::throw_404(DEFAULT_TEMPLATE);
            return false;
        } 

        $controller_name = CONTROLLER_LOCATION . $args['controller']; 
        $controller = new $controller_name(array(
            'http_get'  => $args['get'],
            'http_post' => $args['post']
        ));

        $results = $controller->call($args['action'], $args['id']);
        if ( !$results ) {
            self::throw_404($controller->get_template());
            return false;
        }

        return true;
    }

   /**
    *  Renders a 404 page.
    *
    *  @param string $template            The view template to use.
    *  @access public
    *  @return void
    */
    public static function throw_404($template) {
        $view_file = '../view/' . $template . '/404.html';
        if ( file_exists($view_file) ) {
            include $view_file;
        }
    }

}

?>
