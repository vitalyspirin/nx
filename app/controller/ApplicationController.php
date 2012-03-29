<?php

namespace app\controller;

class ApplicationController extends \nx\core\Controller {

   /**
    *  The user that is currently logged in.
    *
    *  @var obj
    *  @access public
    */
    public $current_user;

   /**
    *  The session object.
    *
    *  @var obj
    *  @access public
    */
    public $session;

   /**
    *  Loads the configuration settings for the controller.
    *
    *  @param array $config        The configuration options.
    *  @access public
    *  @return void
    */
    public function __construct(array $config = array()) {
        $defaults = array(
            'libs' => array(
                'application' => 'app\lib\Application'
            )
        );
        $config += $defaults;
        $application = $config['libs']['application'];
        $db = $application::environment();

        $defaults = array(
            'dependencies' => array(
                'session' => new \nx\core\Session(),
                'user'    => new \app\model\User(compact('db'))
            )
        );
        $config += $defaults;

        $this->session = $config['dependencies']['session'];
        // TODO: Load the appropriate user
        $this->current_user = $config['dependencies']['user'];
        parent::__construct();
    }

   /**
    *  Redirects the page.
    *
    *  @param string $page    The redirect location.
    *  @access public
    *  @return void
    */
    public function redirect($page) {
        header('Location: ' . $page);
        exit;
    }

    public function to_json($array) {
        $options = JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT
            | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE;
        return json_encode($array, $options);
    }

}

?>
