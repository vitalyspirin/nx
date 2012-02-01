<?php

/**
 * NX
 *
 * @author    Nick Sinopoli <NSinopoli@gmail.com>
 * @copyright Copyright (c) 2011, Nick Sinopoli
 * @license   http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace nx\core;

use nx\lib\Auth;
use nx\lib\Data;
use nx\lib\Meta;

/*
 *  The `Controller` class is the parent class of all
 *  application controllers.  It provides access request
 *  data and ensures protection against CSRF attacks.
 *
 *  @package core
 */
class Controller extends Object {

   /**
    *  The controller actions that are
    *  accessible to guests (i.e., users
    *  that are not logged in).
    *
    *  @var array
    *  @access protected
    */
    protected $_guest_accessible = array();

   /**
    *  The uri to which a guest should be
    *  redirected if an action is not
    *  accessible to them.
    *
    *  @var string
    *  @access protected
    */
    protected $_guest_redirect = '/';

   /**
    *  The request object containing
    *  all of the information pertinent
    *  to the incoming request.
    *
    *  @var obj
    *  @access protected
    */
    protected $_request;

   /**
    *  The session object.
    *
    *  @var object
    *  @access protected
    */
    protected $_session;

   /**
    *  The request token.
    *
    *  @var string
    *  @access protected
    */
    protected $_token = null;

   /**
    *  The user object.
    *
    *  @var object
    *  @access protected
    */
    protected $_user;

   /**
    *  Loads the configuration settings for the controller.
    *
    *  @param array $config        The configuration options.
    *  @access public
    *  @return void
    */
    public function __construct(array $config = array()) {
        $defaults = array(
            'classes'      => array(
                'session' => 'app\model\Session',
                'user'    => 'app\model\User'
            ),
            'dependencies' => array(
                'request' => null
            ),
            'libraries' => array(
                'library' => 'nx\lib\Library',
            )
        );
        parent::__construct($config + $defaults);
    }

   /**
    *  Initializes the controller with http request data,
    *  generates a token to be used to ensure that the next request is valid,
    *  and loads a user object if a valid session is found.
    *
    *  @access protected
    *  @return void
    */
    protected function _init() {
        parent::_init();

        $session = $this->_config['classes']['session'];
        $this->_session = new $session();

        $this->_request = $this->_config['dependencies'];

        if ( !$this->_is_valid_request($this->_request) ) {
            $this->handle_CSRF();
        }

        $this->_token = Auth::create_token();

        if ( $this->_session->is_logged_in() ) {
            $user = $this->_config['classes']['user'];
            $id = $this->_session->get_user_id();
            $this->_user = new $user(compact('id'));
        }
    }

   /**
    *  Calls the controller action, whose return values can then
    *  be passed to and parsed by a view.
    *
    *  @param string $action       The action.
    *  @param int $id              The id (passed from the URL, useful with
    *                              query strings like `http://foobar.com/entry/23`
    *                              or `http://foobar.com/entry/view/23`).
    *  @access public
    *  @return mixed
    */
    public function call($action, $id = null) {
        if ( !$this->_user && !in_array($action, $this->_guest_accessible) ) {
            $this->redirect($this->_guest_redirect);
            return false;
        }

        if ( !$action = $this->_determine_action($action, $this->_request) ) {
            return false;
        }

        $results = $this->$action($id);

        if ( is_null($results) || $results === false ) {
            return false;
        }

        $additional = array(
            'token' => $this->_token,
            'user'  => $this->_user
        );

        return $results + $additional;
    }

   /**
    *  Determines the appropriate action depending on the
    *  nature of the request.
    *
    *  @param string $action       The action.
    *  @param obj $request         The request.
    *  @access public
    *  @return mixed
    */
    protected function _determine_action($action, $request) {
        if ( $this->is_protected($action) ) {
            return false;
        }

        if ( $action != 'index' ) {
            if ( !method_exists($this, $action) ) {
                return false;
            }
            return $action;
        }

        $methods = array('get', 'post', 'put', 'delete');
        foreach ( $methods as $method ) {
            if ( $request->is($method) ) {
                $action = '_' . $method;
                break;
            }
        }

        if ( !method_exists($this, $action) ) {
            return false;
        }

        return $action;
    }

   /**
    *  Handles CSRF attacks.
    *
    *  @access public
    *  @return void
    */
    public function handle_CSRF() {
        // TODO: HTTP 403
        die('CSRF attack!');
    }

   /**
    *  Checks if a method is protected.
    *
    *  @param string $method       The method.
    *  @access public
    *  @return bool
    */
    public function is_protected($method) {
        return ( in_array($method, Meta::get_protected_methods($this)) );
    }

   /**
    *  Checks that the token submitted with the
    *  request data is valid.
    *
    *  @param obj $request       The request object.
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

        return Auth::is_token_valid($token);
    }

   /**
    *  Redirects the page.
    *
    *  @param string $page         The page to be redirected to.
    *  @access public
    *  @return bool
    */
    public function redirect($page) {
        if ( headers_sent() ) {
            echo '<meta content="0; url=' . $page . '" http-equiv="refresh"/>';
        } else {
            header('Location: ' . $page);
        }
        return false;
    }

}

?>
