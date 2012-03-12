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
    *  Sets the configuration options for the dispatcher.
    *
    *  @param array $config    The configuration options.
    *  @access public
    *  @return void
    */
	public function __construct(array $config = array()) {
        $defaults = array(
            'libs' => array(
                'router'  => 'nx\lib\Router'
            ),
            'dependencies' => array(
                'view' => new \nx\core\View()
            )
        );
        parent::__construct($config + $defaults);
	}

    // TODO: Fix this description.  We should be returning a response.
   /**
    *  Handles an incoming request.
    *
    *  @param obj $request    The incoming request object.
    *  @access public
    *  @return bool
    */
    public function handle($request) {
        $router = $this->_config['libs']['router'];
        $url = $request->url;
        $method = $request->get_env('REQUEST_METHOD');

        $parsed = $router::parse($url, $method);
        if ( is_null($parsed['callback']) ) {
            // TODO: Fix this
            return $this->throw_404($template);
        }

        list($controller, $action) = explode('::', $parsed['callback']);
        if ( !class_exists($controller) ) {
            return $this->throw_404($template);
        }

        $request->params = $parsed['params'];

        $controller = new $controller();
        $response = $controller->call($action, $request);
        // TODO: Fix this
        // Allow for $response->set_404();
        if ( !$response ) {
            return $this->throw_404();
        }

        if ( $request->is('ajax') ) {
            return $response;
        }

        // TODO: Fix this
        $view = $this->_config['dependencies']['view'];
        $file = lcfirst($controller->classname()) . "/{$action}";
        return $view->render($file, $results);
    }

   /**
    *  Renders a 404 page.
    *
    *  @param string $template    The view template to use.
    *  @access public
    *  @return void
    */
    public function throw_404($template) {
        $view = $this->_config['classes']['view'];
        $view = new $view(compact('template'));
        // TODO: Fix this
        //return $view->throw_404();
    }

}

?>
