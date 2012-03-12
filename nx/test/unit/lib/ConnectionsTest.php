<?php

namespace nx\test\lib;

use nx\lib\Connections;

class ConnectionsTest extends \PHPUnit_Framework_TestCase {

    protected $_cache_configs = array();
    protected $_db_configs = array();

    public function setUp() {
        $this->_cache_configs = array(
            'disabled' => array(
                'enabled'       => false,
                'plugin'        => 'nx\test\mock\plugin\cache\CacheMock',
                'host'          => 'localhost'
            ),
            'enabled' => array(
                'enabled'       => true,
                'plugin'        => 'nx\test\mock\plugin\cache\CacheMock',
                'host'          => 'localhost'
            )
        );

        $this->_db_configs = array(
            'default' => array(
                'plugin'   => 'nx\test\mock\plugin\db\DbMock',
                'database' => '',
                'host'     => 'localhost',
                'username' => 'root',
                'password' => 'admin'
            )
        );

        Connections::add_cache($this->_cache_configs);
        Connections::add_db($this->_db_configs);
    }

    public function test_GetDb_ReturnsDb() {
        $db = Connections::get_db('default');
        $this->assertEquals('DbMock', $db->classname());
        $db_dupe = Connections::get_db('default');
        $this->assertEquals($db, $db_dupe);
    }

    public function test_GetEnabledCache_ReturnsCache() {
        $cache = Connections::get_cache('enabled');
        $this->assertEquals('CacheMock', $cache->classname());
        $cache_dupe = Connections::get_cache('enabled');
        $this->assertEquals($cache, $cache_dupe);
    }

    public function test_GetDisabledCache_ReturnsFalse() {
        $cache = Connections::get_cache('disabled');
        $this->assertFalse($cache);
    }

}
