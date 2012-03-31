<?php

/**
 * NX
 *
 * @author    Nick Sinopoli <NSinopoli@gmail.com>
 * @copyright Copyright (c) 2011, Nick Sinopoli
 * @license   http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace nx\core;

/*
 *  // TODO: Fix this
 *  The `Dispatcher` class is used to handle page rendering.
 *
 *  @package core
 */
class Dispatcher {

   /**
    *  The configuration settings.
    *
    *  @var array
    *  @access protected
    */
    protected $_config = array();

   /**
    *  Sets the configuration options for the dispatcher.
    *
    *  @param array $config    The configuration options.
    *  @access public
    *  @return void
    */
	public function __construct(array $config = array()) {
        $defaults = array(
            'buffer_size'  => 8192,
            'dependencies' => array(
                'response' => new \nx\core\Response(),
                'router'   => new \nx\core\Router()
            )
        );
        $this->_config = $config + $defaults;
	}

   /**
    *  Handles an incoming request, and returns a response object.
    *
    *  @param obj $request    The incoming request object.
    *  @access public
    *  @return bool
    */
    public function handle($request, $routes) {
        $method = $request->get_env('REQUEST_METHOD');

        $router = $this->_config['dependencies']['router'];
        $parsed = $router->parse($request->url, $method, $routes);

        $request->params = $parsed['params'];
        $result = call_user_func($parsed['callback'], $request);

        $response = $this->_config['dependencies']['response'];
        return $response->render($result, $this->_config['buffer_size']);
    }

}

?>
