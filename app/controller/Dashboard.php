<?php

namespace app\controller;

class Dashboard extends \nx\core\Controller {

    protected $_guest_accessible = array('index');

    public function index() {
        return array();
    }
}

?>
