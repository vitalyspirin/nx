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
            'classes'   => array(
                'library' => 'nx\lib\Library',
                'router'  => 'nx\lib\Router',
                'view'    => 'nx\core\View'
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
        $template = ( $request->is('mobile') ) ? 'mobile' : 'web';

        $router = $this->_config['classes']['router'];
        $url = $request->env('REQUEST_URI');
        if ( !$parsed = $router::parse($url) ) {
            return $this->throw_404($template);
        }

        $request->query = $parsed['query'] + $request->query;

        $library = $this->_config['classes']['library'];
        $controller_namespace = $library::get('namespace', 'controller');
        $controller = $controller_namespace . $parsed['controller'];

        if ( !class_exists($controller) ) {
            return $this->throw_404($template);
        }

        $controller = new $controller(array('request' => $request));

        $results = $controller->call($parsed['action'], $parsed['id']);
        if ( !is_array($results) ) {
            return $this->throw_404($template);
        }

        // AJAX
        if ( isset($results['json']) ) {
            $json = $results['json'];
            $options = JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP;
            return json_encode($json, $options);
        }

        $view = $this->_config['classes']['view'];
        $view = new $view(compact('template'));

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
