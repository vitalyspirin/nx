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
 *  The `Dispatcher` class is used to handle page rendering.
 *
 *  @package core
 */
class Dispatcher extends Object {

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
            'dependencies' => array(
                'response' => new \nx\core\Response()
            ),
            'libs' => array(
                'router'  => 'nx\lib\Router'
            )
        );
        $this->_config = $config + $defaults;
	}

    // TODO: Fix this description.
   /**
    *  Handles an incoming request, and returns a response object.
    *
    *  @param obj $request    The incoming request object.
    *  @access public
    *  @return bool
    */
    public function handle($request) {
        $response = $this->_config['dependencies']['response'];

        $router = $this->_config['libs']['router'];
        $url = $request->url;
        $method = $request->get_env('REQUEST_METHOD');

        $parsed = $router::parse($url, $method);
        if ( is_null($parsed['callback']) ) {
            $response->set_status(404);
            // TODO: Fix this
            $response->set_body();
            return $response;
        }

        list($controller, $action) = explode('::', $parsed['callback']);
        $request->params = $parsed['params'];

        $controller = new $controller();
        return $controller->call($action, $request, $response);
    }

}

?>
