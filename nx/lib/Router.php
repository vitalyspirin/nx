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
 *  The `Router` class is used to handle url routing.
 *
 *  @package lib
 */
class Router {

   /**
    *  The routing defaults.
    *
    *  @var array
    *  @access protected
    */
    public static $defaults = array(
        'controller' => 'Signup',
        'action'     => 'index',
        'id'         => null
    );

   /**
    *  The url rewrite scheme.
    *
    *  @var array
    *  @access protected
    */
    protected static $_routes = array(
        '/^\/?$/'                                                  =>
            '', // this will return the defaults

        '/^\/\?(.+)$/'                                             =>
            'args=$1',

        '/^\/([A-Za-z0-9\-]+)\/?$/'                                =>
            'controller=$1',

        '/^\/([A-Za-z0-9\-]+)\?(.+)$/'                             =>
            'controller=$1&args=$2',

        '/^\/([A-Za-z0-9\-]+)\/([\d]+)\/?$/'                       =>
            'controller=$1&id=$2',

        '/^\/([A-Za-z0-9\-]+)\/([\d]+)\?(.+)$/'                    =>
            'controller=$1&id=$2&args=$3',

        '/^\/([A-Za-z0-9\-]+)\/([A-Za-z0-9\-_]+)\/?$/'             =>
            'controller=$1&action=$2',

        '/^\/([A-Za-z0-9\-]+)\/([A-Za-z0-9\-_]+)\?(.+)$/'          =>
            'controller=$1&action=$2&args=$3',

        '/^\/([A-Za-z0-9\-]+)\/([A-Za-z0-9\-_]+)\/([\d]+)\/?$/'    =>
            'controller=$1&action=$2&id=$3',

        '/^\/([A-Za-z0-9\-]+)\/([A-Za-z0-9\-_]+)\/([\d]+)\?(.+)$/' =>
            'controller=$1&action=$2&id=$3&args=$4'
    );

    // TODO: Convert
    /*
    protected static $_routes = array(
        array('GET', '/login', 'Login::index')
    );
     */

   /**
    *  Parses a query string and returns the controller, action,
    *  id, and any additional arguments.
    *
    *  @param string $query_string  The query string.
    *  @access public
    *  @return array
    */
    protected static function _parse_query_string($query_string) {
        $arg_pos = strpos($query_string, 'args=');
        if ( $arg_pos !== false ) {
            $args = substr($query_string, $arg_pos + strlen('args='));
            $query_string = rtrim(substr($query_string, 0, $arg_pos), '&');
        }
        $params = array();
        parse_str($query_string, $params);

        $controller = ( isset($params['controller']) )
            ? ucfirst($params['controller'])
            : self::$defaults['controller'];
        $action = ( isset($params['action']) )
            ? $params['action']
            : self::$defaults['action'];
        $id = ( isset($params['id']) )
            ? $params['id']
            : self::$defaults['id'];

        $query = array();
        if ( isset($args) && $args != '' ) {
            $args = rawurldecode(str_replace('%20', '+', $args));
            parse_str($args, $query);
        }
        return compact('controller', 'action', 'id', 'query');
    }

   /**
    *  Parses a url in accordance with the defined routes,
    *  and returns the necessary components to route the request.
    *
    *  @see nx\lib\Router::_parse_query_string()
    *  @param string $url          The url.
    *  @access public
    *  @return array
    */
    /*
    public static function parse($url) {
        $matches = array();
        foreach ( self::$_routes as $pattern => $route ) {
            if ( preg_match($pattern, $url, $matches) ) {
                unset($matches[0]);
                foreach ( $matches as $key => $match ) {
                    $route = str_replace('$' . $key, $match, $route);
                }
                return self::_parse_query_string($route);
            }
        }
        return false;
    }
     */


    public static function set_routes($routes) {
        self::$_routes = $routes;
    }

    public static function parse($request_uri, $request_method) {
        foreach ( self::$_routes as $route ) {
            list($method, $uri, $callback) = $route;

            if ( is_array($method) ) {
                $found = false;
                foreach ( $method as $value ) {
                    if ( strcasecmp($request_method, $value) == 0 ) {
                        $found = true;
                        break;
                    }
                }
                if ( !$found ) {
                    continue;
                }
            } elseif ( strcasecmp($request_method, $method) != 0 ) {
                continue;
            }

            if ( is_null($uri) || $uri == '*' || $uri == '404' ) {
                $args = array();
                return compact('args', 'callback');
            }

            $route_to_match = '';
            $len = strlen($uri);

            for ( $i = 0; $i < $len; $i++ ) {
                $char = $uri[$i];
                $is_regex = (
                    $char == '[' || $char == '(' || $char == '.'
                    || $char == '?' || $char == '+' || $char == '{'
                );
                if ( $is_regex ) {
                    $route_to_match = $uri;
                    break;
                } elseif (
                    !isset($request_uri[$i]) || $char != $request_uri[$i]
                ) {
                    continue 2;
                }
                $route_to_match .= $char;
            }

            $regex = self::_compile_regex($route_to_match);
            if ( preg_match($regex, $request_uri, $args) ) {
                foreach ( $args as $key => $arg ) {
                    if ( is_numeric($key) ) {
                        unset($args[$key]);
                    }
                }
                return compact('args', 'callback');
            }
        }
        return array(
            'args'     => null,
            'callback' => null
        );
    }

    protected static function _compile_regex($route) {
        $pattern = '`(/|\.|)\[([^:\]]*+)(?::([^:\]]*+))?\](\?|)`';

        if ( preg_match_all($pattern, $route, $matches, PREG_SET_ORDER) ) {
            $match_types = array(
                'i'  => '[0-9]++',
                'a'  => '[0-9A-Za-z]++',
                'h'  => '[0-9A-Fa-f]++',
                '*'  => '.+?',
                '**' => '.++',
                ''   => '[^/]++'
            );
            foreach ( $matches as $match ) {
                list($block, $pre, $type, $param, $optional) = $match;

                if ( isset($match_types[$type]) ) {
                    $type = $match_types[$type];
                }
                if ( $pre == '.' ) {
                    $pre = '\.';
                }
                if ( $param ) {
                    $param = '?<' . $param . '>';
                }
                if ( $optional ) {
                    $optional = '?';
                }

                $replaced = '(?:' . $pre . '(' . $param . $type . '))'
                    . $optional;

                $route = str_replace($block, $replaced, $route);
            }
        }
        return '`^' . $route . '$`';
    }

}

?>
