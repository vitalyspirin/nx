<?php

namespace app\test\unit\lib;

use app\lib\Connections;

class ConnectionsTest extends \PHPUnit_Framework_TestCase {

    protected $_db_configs = array();

    public function setUp() {
        $this->_db_configs = array(
            'default' => array(
                'plugin'   => 'app\test\mock\plugin\db\DbMock',
                'database' => '',
                'port'     => 3306,
                'host'     => 'localhost',
                'username' => 'root',
                'password' => 'admin'
            )
        );

        Connections::add_db($this->_db_configs);
    }

    /* TODO: Fix this
    public function test_GetDb_ReturnsDb() {
        $db = Connections::get_db('default');
        $this->assertEquals('DbMock', $db->classname());
        $db_dupe = Connections::get_db('default');
        $this->assertEquals($db, $db_dupe);
    }
     */

}
