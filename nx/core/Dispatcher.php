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
    *  @param array $config        The configuration options.
    *  @access public
    *  @return void
    */
	public function __construct(array $config = array()) {
        $defaults = array(
            'libs' => array(
                'router'  => 'nx\lib\Router'
            ),
            'dependencies' => array(
                'view' => new nx\core\View()
            )
        );
        parent::__construct($config + $defaults);
	}

    // TODO: Fix this description.  We should be returning a response.
   /**
    *  Handles an incoming request.
    *
    *  @param obj $request          The incoming request object.
    *  @access public
    *  @return bool
    */
    public function handle($request) {
        $router = $this->_config['libs']['router'];
        $url = $request->env('REQUEST_URI');
        if ( !$parsed = $router::parse($url) ) {
            return $this->throw_404($template);
        }

        $request->query = $parsed['query'] + $request->query;

        $controller = 'app\controller\\' . $parsed['controller'];

        if ( !class_exists($controller) ) {
            return $this->throw_404($template);
        }

        $dependencies = compact('request');
        $controller = new $controller(compact('dependencies'));

        $results = $controller->call($parsed['action'], $parsed['id']);
        if ( !is_array($results) ) {
            return $this->throw_404($template);
        }

        if ( $request->is('ajax') ) {
            $options = JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT
                | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE;
            return json_encode($results, $options);
        }

        $view = $this->_config['dependencies']['view'];
        $file = lcfirst($parsed['controller']) . '/' . $parsed['action'];
        return $view->render($file, $results);
    }

   /**
    *  Renders a 404 page.
    *
    *  @param string $template     The view template to use.
    *  @access public
    *  @return void
    */
    public function throw_404($template) {
        $view = $this->_config['classes']['view'];
        $view = new $view(compact('template'));
        return $view->throw_404();
    }

}

?>
