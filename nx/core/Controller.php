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
 *  application controllers.  It provides access to typecasted
 *  $_POST and $_GET data and ensures protection against CSRF
 *  attacks.
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
    protected $_guest_redirect = '/login';

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
    *  The typecasts to be used when parsing
    *  request data.  Acceptable types are:
    *  `key` => `b` for booleans
    *  `key` => `f` for float/decimals
    *  `key` => `i` for integers
    *  `key` => `s` for strings
    *
    *  @see /nx/lib/Data::typecast()
    *  @see /nx/core/Controller->typecast()
    *  @var array
    *  @access protected
    */
    protected $_typecasts = array();

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
            'classes'        => array(
                'session' => 'app\model\Session',
                'user'    => 'app\model\User'
            ),
            'request' => null
        );
        parent::__construct($config + $defaults);
    }

   /**
    *  Initializes the controller with typecasted http request data,
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

        $this->_typecasts += array('token' => 's');

        $this->_request = $this->_config['request'];
        $this->_request->data = $this->typecast($this->_request->data);
        die(var_dump($this->_request->data));
        $this->_request->query = $this->typecast($this->_request->query);

        if ( !$this->_is_valid_request($this->_request) ) {
            $this->handle_CSRF();
            $this->_token = null;
        }

        if ( is_null($this->_token) ) {
            $this->_token = Auth::create_token();
        }

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

        if ( !isset($request->data['token']) ) {
            return false;
        }

        return Auth::is_token_valid($request->data['token']);
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

   /**
    *  Typecasts data according to the typecasts defined in $this->_typecasts.
    *  If data is an object, the object's typecast() method will be called.
    *
    *  @param array $data          The data to be typecasted.
    *  @access public
    *  @return array
    */
    public function typecast($data) {
        $typecasted = array();
        foreach ( $data as $key => $val ) {
            if ( !is_array($val) ) {
                if ( isset($this->_typecasts[$key]) ) {
                    $typecasted[$key] = Data::typecast($val, $this->_typecasts[$key]);
                }
            } else {
                foreach ( $val as $id => $obj ) {
                    $typecasted[$key][$id] = $obj->typecast();
                }
            }
        }
        return $typecasted;
    }

}

?>
