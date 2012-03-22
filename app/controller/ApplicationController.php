<?php

namespace app\controller;

class ApplicationController extends \nx\core\Controller {

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
                'user' => new \app\model\User(),
            )
        );
        parent::__construct($config + $defaults);
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
