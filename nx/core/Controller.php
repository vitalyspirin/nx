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

/*
 *  The `Controller` class is the parent class of all
 *  application controllers.  It provides access request
 *  data and ensures protection against CSRF attacks.
 *
 *  @package core
 */
class Controller extends Object {

   /**
    *  The request object containing
    *  all of the information pertinent
    *  to the incoming request.
    *
    *  @var obj
    *  @access public
    */
    public $request;

   /**
    *  The session object.
    *
    *  @var object
    *  @access public
    */
    public $session;

   /**
    *  The request token.
    *
    *  @var string
    *  @access public
    */
    public $token = null;

   /**
    *  The user object representing the user currently logged in.
    *
    *  @var object
    *  @access public
    */
    public $current_user;

   /**
    *  Loads the configuration settings for the controller.
    *
    *  @param array $config        The configuration options.
    *  @access public
    *  @return void
    */
    public function __construct(array $config = array()) {
        $defaults = array(
            'dependencies' => array(
                'session' => new app\model\Session(),
                'user'    => new app\model\User(),
                'request' => null
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

        $this->session = $this->_config['dependencies']['session'];
        $this->request = $this->_config['dependencies']['request'];

        if ( !$this->_is_valid_request($this->request) ) {
            $this->handle_CSRF();
        }

        $this->token = Auth::create_token();

        if ( $this->_session->is_logged_in() ) {
            $user = $this->_config['dependencies']['user'];
            $id = $this->session->get_user_id();
            $this->current_user = $user->load_by_primary_key($id);
        }
    }

   /**
    *  Calls the controller action, whose return values can then
    *  be passed to and parsed by a view.
    *
    *  @param string $action       The action.
    *  @param array $args          The arguments extracted from the URI.
    *  @access public
    *  @return mixed
    */
    public function call($action, $args = array()) {
        $results = $this->$action($args);

        if ( !is_array($results) ) {
            return false;
        }

        $additional = array(
            'token' => $this->token,
            'user'  => $this->current_user
        );

        return $results + $additional;
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
    *  @return void
    */
    public function redirect($page) {
        header('Location: ' . $page);
        exit;
    }

}

?>
