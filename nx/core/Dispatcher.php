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
 *  @package lib
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
                'request' => 'nx\lib\Request',
                'router'  => 'nx\lib\Router',
                'view'    => 'nx\core\View'
            ),
            'locations' => array(
                'controller' => 'app\controller\\',
                'model'      => 'app\model\\'
            )
        );
        parent::__construct($config + $defaults);
	}

   /**
    *  Renders a page.
    *
    *  @param string $url          The url representing the page to be rendered.
    *  @access public
    *  @return bool
    */
    public function render($url) {
        $router = $this->_config['classes']['router'];
        if ( !$args = $router::parse_url($url) ) {
            return $this->throw_404('web');
        }

        $controller = $this->_config['locations']['controller']
            . $args['controller'];

        if ( !class_exists($controller) ) {
            return $this->throw_404('web');
        }

        $request = $this->_config['classes']['request'];
        $args['post'] = $request::extract_post($_POST,
            $this->_config['locations']['model']);

        $controller = new $controller(array(
            'http_get'  => $args['get'],
            'http_post' => $args['post']
        ));

        $results = $controller->call($args['action'], $args['id']);
        $template = $controller->get_template();
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

        $file = lcfirst($args['controller']) . '/' . $args['action'];
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
