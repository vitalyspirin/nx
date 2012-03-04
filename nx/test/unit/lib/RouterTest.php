<?php

namespace nx\test\lib;

use nx\lib\Router;

class RouterTest extends \PHPUnit_Framework_TestCase {

    /*
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
     */


    public function test_Parse_ReturnsArray() {
        $routes = array(
            array('GET', '/', 'app\controller\Login::index'),
            array('POST', '/', 'app\controller\Login::create'),
            array('PUT', '/', 'app\controller\Login::update'),
            array('GET', '/users', 'app\controller\User::index'),
            array('GET', '/users/[i:id]', 'app\controller\User::index'),
            array(array('GET', 'PUT'), '/name', 'app\controller\User::name'),
            array('get', '/name/[a:name]', 'app\controller\User::name'),
            array('delete', '/name/[h:id]', 'app\controller\User::name'),
            array('get', '/phone/[a:type]', 'app\controller\User::phone'),
        );
        Router::set_routes($routes);

        $request_uri = '/';
        $request_method = 'GET';
        $check = array(
            'args'     => array(),
            'callback' => 'app\controller\Login::index'
        );
        $input = Router::parse($request_uri, $request_method);
        $this->assertEquals($check, $input);

        $request_uri = '/';
        $request_method = 'POST';
        $check = array(
            'args'     => array(),
            'callback' => 'app\controller\Login::create'
        );
        $input = Router::parse($request_uri, $request_method);
        $this->assertEquals($check, $input);

        $request_uri = '/';
        $request_method = 'PUT';
        $check = array(
            'args'     => array(),
            'callback' => 'app\controller\Login::update'
        );
        $input = Router::parse($request_uri, $request_method);
        $this->assertEquals($check, $input);

        $request_uri = '/users';
        $request_method = 'GET';
        $check = array(
            'args'     => array(),
            'callback' => 'app\controller\User::index'
        );
        $input = Router::parse($request_uri, $request_method);
        $this->assertEquals($check, $input);

        $request_uri = '/users/37';
        $request_method = 'GET';
        $check = array(
            'args'     => array('id' => '37'),
            'callback' => 'app\controller\User::index'
        );
        $input = Router::parse($request_uri, $request_method);
        $this->assertEquals($check, $input);

        $request_uri = '/users/adfsfsk';
        $request_method = 'GET';
        $check = array(
            'args'     => null,
            'callback' => null
        );
        $input = Router::parse($request_uri, $request_method);
        $this->assertEquals($check, $input);

        $request_uri = '/name';
        $request_method = 'PUT';
        $check = array(
            'args'     => array(),
            'callback' => 'app\controller\User::name'
        );
        $input = Router::parse($request_uri, $request_method);
        $this->assertEquals($check, $input);

        $request_uri = '/name/john';
        $request_method = 'POST';
        $check = array(
            'args'     => null,
            'callback' => null
        );
        $input = Router::parse($request_uri, $request_method);
        $this->assertEquals($check, $input);

        $request_uri = '/name/john';
        $request_method = 'GET';
        $check = array(
            'args'     => array('name' => 'john'),
            'callback' => 'app\controller\User::name'
        );
        $input = Router::parse($request_uri, $request_method);
        $this->assertEquals($check, $input);

        $request_uri = '/name/42?options=none';
        $request_method = 'GET';
        $check = array(
            'args'     => null,
            'callback' => null
        );
        $input = Router::parse($request_uri, $request_method);
        $this->assertEquals($check, $input);

        $request_uri = '/name/af0928';
        $request_method = 'DELETE';
        $check = array(
            'args'     => array('id' => 'af0928'),
            'callback' => 'app\controller\User::name'
        );
        $input = Router::parse($request_uri, $request_method);
        $this->assertEquals($check, $input);

        $request_uri = '/name/AJ';
        $request_method = 'DELETE';
        $check = array(
            'args'     => null,
            'callback' => null
        );
        $input = Router::parse($request_uri, $request_method);
        $this->assertEquals($check, $input);

        $request_uri = '/phone';
        $request_method = 'GET';
        $check = array(
            'args'     => array(),
            'callback' => 'app\controller\User::phone'
        );
        $input = Router::parse($request_uri, $request_method);
        $this->assertEquals($check, $input);

        $request_uri = '/phone/home';
        $request_method = 'GET';
        $check = array(
            'args'     => array('type' => 'home'),
            'callback' => 'app\controller\User::phone'
        );
        $input = Router::parse($request_uri, $request_method);
        $this->assertEquals($check, $input);



        $catchall_route = array(
            array('get', '*', 'app\controller\Error::show')
        );
        Router::set_routes(array_merge($routes, $catchall_route));

        $request_uri = '/something/that/doesnt/exist';
        $request_method = 'GET';
        $check = array(
            'args'     => array(),
            'callback' => 'app\controller\Error::show'
        );
        $input = Router::parse($request_uri, $request_method);
        $this->assertEquals($check, $input);
    }


}
?>
