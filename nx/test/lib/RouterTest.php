<?php

namespace nx\test\lib;

use nx\lib\Router;

class RouterTest extends \PHPUnit_Framework_TestCase {

    protected function setUp() {
        $this->markTestSkipped();
    }

    public function test_ParseUrl_ReturnsArray() {
        $query_string = '';
        $args = Router::parse_url($query_string);
        $check = array(
            'controller' => Router::$defaults['controller'],
            'action'     => Router::$defaults['action'],
            'id'         => Router::$defaults['id'],
            'get'        => array()
        );
        $this->assertEquals($args, $check);

        $query_string = '/';
        $args = Router::parse_url($query_string);
        $check = array(
            'controller' => Router::$defaults['controller'],
            'action'     => Router::$defaults['action'],
            'id'         => Router::$defaults['id'],
            'get'        => array()
        );
        $this->assertEquals($args, $check);

        $query_string = '/register';
        $args = Router::parse_url($query_string);
        $check = array(
            'controller' => 'Register',
            'action'     => Router::$defaults['action'],
            'id'         => Router::$defaults['id'],
            'get'        => array()
        );
        $this->assertEquals($args, $check);

        $query_string = '/register/42';
        $args = Router::parse_url($query_string);
        $check = array(
            'controller' => 'Register',
            'action'     => Router::$defaults['action'],
            'id'         => '42',
            'get'        => array()
        );
        $this->assertEquals($args, $check);

        $query_string = '/register?username=test';
        $args = Router::parse_url($query_string);
        $check = array(
            'controller' => 'Register',
            'action'     => Router::$defaults['action'],
            'id'         => Router::$defaults['id'],
            'get'        => array('username' => 'test')
        );
        $this->assertEquals($args, $check);

        $query_string = '/register?username=test&token=42';
        $args = Router::parse_url($query_string);
        $check = array(
            'controller' => 'Register',
            'action'     => Router::$defaults['action'],
            'id'         => Router::$defaults['id'],
            'get'        => array(
                'username' => 'test',
                'token'    => '42'
            )
        );
        $this->assertEquals($args, $check);

        $query_string = '/register/index';
        $args = Router::parse_url($query_string);
        $check = array(
            'controller' => 'Register',
            'action'     => 'index',
            'id'         => Router::$defaults['id'],
            'get'        => array()
        );
        $this->assertEquals($args, $check);

        $query_string = '/register/42?username=test&token=42';
        $args = Router::parse_url($query_string);
        $check = array(
            'controller' => 'Register',
            'action'     => Router::$defaults['action'],
            'id'         => '42',
            'get'        => array(
                'username' => 'test',
                'token'    => '42'
            )
        );
        $this->assertEquals($args, $check);

        $query_string = '/register/index/42';
        $args = Router::parse_url($query_string);
        $check = array(
            'controller' => 'Register',
            'action'     => 'index',
            'id'         => '42',
            'get'        => array()
        );
        $this->assertEquals($args, $check);

        $query_string = '/register/index?username=test&token=42';
        $args = Router::parse_url($query_string);
        $check = array(
            'controller' => 'Register',
            'action'     => 'index',
            'id'         => Router::$defaults['id'],
            'get'        => array(
                'username' => 'test',
                'token'    => '42'
            )
        );
        $this->assertEquals($args, $check);

        $query_string = '/register/index/42?username=test&token=42';
        $args = Router::parse_url($query_string);
        $check = array(
            'controller' => 'Register',
            'action'     => 'index',
            'id'         => '42',
            'get'        => array(
                'username' => 'test',
                'token'    => '42'
            )
        );
        $this->assertEquals($args, $check);

        $query_string = '/register/get_value?name=high%20tide*&start=0&count=20';
        $args = Router::parse_url($query_string);
        $check = array(
            'controller' => 'Register',
            'action'     => 'get_value',
            'id'         => Router::$defaults['id'],
            'get'        => array(
                'name'  => 'high tide*',
                'start' => '0',
                'count' => '20'
            )
        );
        $this->assertEquals($args, $check);

    }

}
?>
