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
 *  The `Router` class is used to handle url routing.
 *
 *  @package core
 */
class Router {

    protected function _compile_regex($route) {
        $pattern = '`(/|\.|)\[([^:\]]*+)(?::([^:\]]*+))?\](\?|)`';

        if ( preg_match_all($pattern, $route, $matches, PREG_SET_ORDER) ) {
            $match_types = array(
                'i'  => '[0-9]++',
                'a'  => '[0-9A-Za-z]++',
                'h'  => '[0-9A-Fa-f]++',
                '*'  => '.+?',
                ''   => '[^/]++'
            );
            foreach ( $matches as $match ) {
                list($block, $pre, $type, $param, $optional) = $match;

                if ( isset($match_types[$type]) ) {
                    $type = $match_types[$type];
                }
                if ( $param ) {
                    $param = "?<{$param}>";
                }
                if ( $optional ) {
                    $optional = '?';
                }

                $replaced = "(?:{$pre}({$param}{$type})){$optional}";
                $route = str_replace($block, $replaced, $route);
            }
        }
        return "`^{$route}$`";
    }

    public function parse($request_uri, $request_method, $routes) {
        foreach ( $routes as $route ) {
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

            if ( is_null($uri) || $uri == '*' ) {
                $params = array();
                return compact('params', 'callback');
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

            $regex = $this->_compile_regex($route_to_match);
            if ( preg_match($regex, $request_uri, $params) ) {
                foreach ( $params as $key => $arg ) {
                    if ( is_numeric($key) ) {
                        unset($params[$key]);
                    }
                }
                return compact('params', 'callback');
            }
        }
        return array(
            'params'   => null,
            'callback' => null
        );
    }

}

?>
