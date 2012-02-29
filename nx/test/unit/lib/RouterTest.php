<?php

namespace nx\test\lib;

use nx\lib\Router;

class RouterTest extends \PHPUnit_Framework_TestCase {

    public function test_ParseUrl_ReturnsArray() {
        $query_string = '';
        $args = Router::parse($query_string);
        $check = array(
            'controller' => Router::$defaults['controller'],
            'action'     => Router::$defaults['action'],
            'id'         => Router::$defaults['id'],
            'query'      => array()
        );
        $this->assertEquals($args, $check);

        $query_string = '/';
        $args = Router::parse($query_string);
        $check = array(
            'controller' => Router::$defaults['controller'],
            'action'     => Router::$defaults['action'],
            'id'         => Router::$defaults['id'],
            'query'      => array()
        );
        $this->assertEquals($args, $check);

        $query_string = '/?code=42';
        $args = Router::parse($query_string);
        $check = array(
            'controller' => Router::$defaults['controller'],
            'action'     => Router::$defaults['action'],
            'id'         => Router::$defaults['id'],
            'query'      => array(
                'code' => 42
            )
        );
        $this->assertEquals($args, $check);

        $query_string = '/register';
        $args = Router::parse($query_string);
        $check = array(
            'controller' => 'Register',
            'action'     => Router::$defaults['action'],
            'id'         => Router::$defaults['id'],
            'query'      => array()
        );
        $this->assertEquals($args, $check);

        $query_string = '/register/42';
        $args = Router::parse($query_string);
        $check = array(
            'controller' => 'Register',
            'action'     => Router::$defaults['action'],
            'id'         => '42',
            'query'      => array()
        );
        $this->assertEquals($args, $check);

        $query_string = '/register?username=test';
        $args = Router::parse($query_string);
        $check = array(
            'controller' => 'Register',
            'action'     => Router::$defaults['action'],
            'id'         => Router::$defaults['id'],
            'query'      => array('username' => 'test')
        );
        $this->assertEquals($args, $check);

        $query_string = '/register?username=test&token=42';
        $args = Router::parse($query_string);
        $check = array(
            'controller' => 'Register',
            'action'     => Router::$defaults['action'],
            'id'         => Router::$defaults['id'],
            'query'      => array(
                'username' => 'test',
                'token'    => '42'
            )
        );
        $this->assertEquals($args, $check);

        $query_string = '/register/index';
        $args = Router::parse($query_string);
        $check = array(
            'controller' => 'Register',
            'action'     => 'index',
            'id'         => Router::$defaults['id'],
            'query'      => array()
        );
        $this->assertEquals($args, $check);

        $query_string = '/register/42?username=test&token=42';
        $args = Router::parse($query_string);
        $check = array(
            'controller' => 'Register',
            'action'     => Router::$defaults['action'],
            'id'         => '42',
            'query'      => array(
                'username' => 'test',
                'token'    => '42'
            )
        );
        $this->assertEquals($args, $check);

        $query_string = '/register/index/42';
        $args = Router::parse($query_string);
        $check = array(
            'controller' => 'Register',
            'action'     => 'index',
            'id'         => '42',
            'query'      => array()
        );
        $this->assertEquals($args, $check);

        $query_string = '/register/index?username=test&token=42';
        $args = Router::parse($query_string);
        $check = array(
            'controller' => 'Register',
            'action'     => 'index',
            'id'         => Router::$defaults['id'],
            'query'      => array(
                'username' => 'test',
                'token'    => '42'
            )
        );
        $this->assertEquals($args, $check);

        $query_string = '/register/index/42?username=test&token=42';
        $args = Router::parse($query_string);
        $check = array(
            'controller' => 'Register',
            'action'     => 'index',
            'id'         => '42',
            'query'      => array(
                'username' => 'test',
                'token'    => '42'
            )
        );
        $this->assertEquals($args, $check);

        $query_string = '/register/query_value?name=high%20tide*&start=0&count=20';
        $args = Router::parse($query_string);
        $check = array(
            'controller' => 'Register',
            'action'     => 'query_value',
            'id'         => Router::$defaults['id'],
            'query'      => array(
                'name'  => 'high tide*',
                'start' => '0',
                'count' => '20'
            )
        );
        $this->assertEquals($args, $check);

    }

}
?>
