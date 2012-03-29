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
 *  The `Controller` class is the parent class of all
 *  application controllers.  It provides access request
 *  data and ensures protection against CSRF attacks.
 *
 *  @package core
 */
class Controller extends Object {

   /**
    *  The configuration settings.
    *
    *  @var array
    *  @access protected
    */
    protected $_config = array();

   /**
    *  Loads the configuration settings for the controller.
    *
    *  @param array $config    The configuration options.
    *  @access public
    *  @return void
    */
    public function __construct(array $config = array()) {
        $defaults = array(
            'dependencies' => array(
                'view'     => new \nx\core\View()
            ),
            'libs' => array(
                'auth' => 'nx\lib\Auth'
            )
        );

        $this->_config = $config + $defaults;
    }

   /**
    *  Calls the controller action, whose return values can then
    *  be passed to and parsed by a view.
    *
    *  @param string $action    The action.
    *  @param obj $request      The request.
    *  @param obj $response     The response.
    *  @access public
    *  @return mixed
    */
    public function call($action, $request, $response) {
        if ( !$this->_is_valid_request($request) ) {
            return $this->handle_CSRF($response);
        }

        $result = $this->$action($request, $response);

        if ( !is_array($result) ) {
            return $result;
        }

        $auth = $this->_config['libs']['auth'];
        $result += array('authenticity_token' => $auth::create_token());

        $view = $this->_config['dependencies']['view'];
        $file = lcfirst($this->classname()) . "/{$action}";
        $response->body = $view->render($file, $result);
        return $response;
    }

   /**
    *  Handles CSRF attacks.
    *
    *  @access public
    *  @return void
    */
    public function handle_CSRF($response) {
        $response->status = 403;
        $response->body = '<h1>403 Forbidden</h1>';
        return $response;
    }

   /**
    *  Checks that the token submitted with the request data is valid.
    *
    *  @param obj $request    The request object.
    *  @access protected
    *  @return bool
    */
    protected function _is_valid_request($request) {
        if ( $request->is('get') && empty($request->data) ) {
            return true;
        }

        $token = null;
        if ( $request->is('delete') && isset($request->query['token']) ) {
            $token = $request->query['token'];
        } elseif ( isset($request->data['token']) ) {
            $token = $request->data['token'];
        }

        if ( is_null($token) ) {
            return false;
        }

        $auth = $this->_config['libs']['auth'];
        return $auth::is_token_valid($token);
    }

}

?>
