<?php

namespace app\test\unit\plugin\db;

use app\plugin\db\PDO_MySQL;

class PDO_MySQLTest extends \PHPUnit_Framework_TestCase {

    protected $_db;

    public function setUp() {
        $this->_db = new PDO_MySQL();
    }
}
?>
