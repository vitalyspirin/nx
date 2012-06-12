<?php

namespace nx\test\core;

use nx\core\Router;

class RouterTest extends \PHPUnit_Framework_TestCase {

    public function test_Parse_ReturnsArray() {
        $router = new Router();

        $routes = array(
            array('GET', '/', function() { return 'GET /'; }),
            array('POST', '/', function() { return 'POST /'; }),
            array('PUT', '/', function() { return 'PUT /'; }),
            array('GET', '/users', function() { return 'GET /users'; }),
            array('GET', '/users/[i:id]', function() { return 'GET /users/[i:id]'; }),
            array('GET', '/name', function() { return 'GET /name'; }),
            array(array('get', 'put'), '/name/[a:name]', function() { return 'GET /name/[a:name]'; }),
            array('delete', '/name/[h:id]', function() { return 'DELETE name/[h:id]'; }),
            array('get', '/phone/[a:type]', function() { return 'GET /phone/[a:type]'; }),
            array('get', '/type/[:type]', function() { return 'GET /type/[:type]'; }),
            array('get', '/friends/[*:friends][i:id]', function() { return 'GET /friends/[*:friends][i:id]'; }),
            array('post', '/entry/[new|create:action]', function() { return 'POST /entry/[new|create:action]'; }),
            array('put', '/entry/[:id]/[*:params]', function() { return 'PUT /entry/[:id][*:params]'; }),
            array('get', '/file[\.jpg|\.gif|\.png:image]?', function() { return 'GET /file/[\.jpg|\.gif|\.png:image]?'; }),
            array('get', '/news[:params]?', function() { return 'GET /news/[:params]?'; })
        );

        $request_uri = '/';
        $request_method = 'GET';
        $check = array(
            'params'   => array(),
            'callback' => function() { return 'GET /'; }
        );
        $input = $router->parse($request_uri, $request_method, $routes);
        $this->assertEquals($check, $input);

        $request_uri = '/';
        $request_method = 'POST';
        $check = array(
            'params'   => array(),
            'callback' => function() { return 'POST /'; }
        );
        $input = $router->parse($request_uri, $request_method, $routes);
        $this->assertEquals($check, $input);

        $request_uri = '/';
        $request_method = 'PUT';
        $check = array(
            'params'   => array(),
            'callback' => function() { return 'PUT /'; }
        );
        $input = $router->parse($request_uri, $request_method, $routes);
        $this->assertEquals($check, $input);

        $request_uri = '/users';
        $request_method = 'GET';
        $check = array(
            'params'   => array(),
            'callback' => function() { return 'GET /users'; }
        );
        $input = $router->parse($request_uri, $request_method, $routes);
        $this->assertEquals($check, $input);

        $request_uri = '/users/';
        $request_method = 'GET';
        $check = array(
            'params'   => array(),
            'callback' => function() { return 'GET /users'; }
        );
        $input = $router->parse($request_uri, $request_method, $routes);
        $this->assertEquals($check, $input);

        $request_uri = '/users/37';
        $request_method = 'GET';
        $check = array(
            'params'   => array('id' => '37'),
            'callback' => function() { return 'GET /users/[i:id]'; }
        );
        $input = $router->parse($request_uri, $request_method, $routes);
        $this->assertEquals($check, $input);

        $request_uri = '/users/bob';
        $request_method = 'GET';
        $check = array(
            'params'   => null,
            'callback' => null
        );
        $input = $router->parse($request_uri, $request_method, $routes);
        $this->assertEquals($check, $input);

        $request_uri = '/name/jimmy';
        $request_method = 'PUT';
        $check = array(
            'params'   => array('name' => 'jimmy'),
            'callback' => function() { return 'GET /name'; }
        );
        $input = $router->parse($request_uri, $request_method, $routes);
        $this->assertEquals($check, $input);

        $request_uri = '/name/john';
        $request_method = 'POST';
        $check = array(
            'params'   => null,
            'callback' => null
        );
        $input = $router->parse($request_uri, $request_method, $routes);
        $this->assertEquals($check, $input);

        $request_uri = '/name/john';
        $request_method = 'GET';
        $check = array(
            'params'   => array('name' => 'john'),
            'callback' => function() { return 'GET /name/[a:name]'; }
        );
        $input = $router->parse($request_uri, $request_method, $routes);
        $this->assertEquals($check, $input);

        $request_uri = '/name/42?options=none';
        $request_method = 'GET';
        $check = array(
            'params'   => null,
            'callback' => null
        );
        $input = $router->parse($request_uri, $request_method, $routes);
        $this->assertEquals($check, $input);

        $request_uri = '/name/af0928';
        $request_method = 'DELETE';
        $check = array(
            'params'   => array('id' => 'af0928'),
            'callback' => function() { return 'DELETE name/[h:id]'; }
        );
        $input = $router->parse($request_uri, $request_method, $routes);
        $this->assertEquals($check, $input);

        $request_uri = '/name/AJ';
        $request_method = 'DELETE';
        $check = array(
            'params'   => null,
            'callback' => null
        );
        $input = $router->parse($request_uri, $request_method, $routes);
        $this->assertEquals($check, $input);

        $request_uri = '/phone';
        $request_method = 'GET';
        $check = array(
            'params'   => null,
            'callback' => null
        );
        $input = $router->parse($request_uri, $request_method, $routes);
        $this->assertEquals($check, $input);

        $request_uri = '/phone/home';
        $request_method = 'GET';
        $check = array(
            'params'   => array('type' => 'home'),
            'callback' => function() { return 'GET /phone/[a:type]'; }
        );
        $input = $router->parse($request_uri, $request_method, $routes);
        $this->assertEquals($check, $input);

        $request_uri = '/type/somethingnew';
        $request_method = 'GET';
        $check = array(
            'params'   => array('type' => 'somethingnew'),
            'callback' => function() { return 'GET /type/[:type]'; }
        );
        $input = $router->parse($request_uri, $request_method, $routes);
        $this->assertEquals($check, $input);

        $request_uri = '/friends/sally-382';
        $request_method = 'GET';
        $check = array(
            'params'   => array('friends' => 'sally-', 'id' => '382'),
            'callback' => function() { return 'GET /friends/[*:friends][i:id]'; }
        );
        $input = $router->parse($request_uri, $request_method, $routes);
        $this->assertEquals($check, $input);

        $request_uri = '/entry/create';
        $request_method = 'POST';
        $check = array(
            'params'   => array('action' => 'create'),
            'callback' => function() { return 'POST /entry/[new|create:action]'; }
        );
        $input = $router->parse($request_uri, $request_method, $routes);
        $this->assertEquals($check, $input);

        $request_uri = '/entry/new';
        $request_method = 'POST';
        $check = array(
            'params'   => array('action' => 'new'),
            'callback' => function() { return 'POST /entry/[new|create:action]'; }
        );
        $input = $router->parse($request_uri, $request_method, $routes);
        $this->assertEquals($check, $input);

        $request_uri = '/entry/87/date/tomorrow';
        $request_method = 'PUT';
        $check = array(
            'params'   => array('id' => '87', 'params' => 'date/tomorrow'),
            'callback' => function() { return 'PUT /entry/[:id][*:params]'; }
        );
        $input = $router->parse($request_uri, $request_method, $routes);
        $this->assertEquals($check, $input);

        $request_uri = '/file';
        $request_method = 'GET';
        $check = array(
            'params'   => array(),
            'callback' => function() { return 'GET /file/[\.jpg|\.gif|\.png:image]?'; }
        );
        $input = $router->parse($request_uri, $request_method, $routes);
        $this->assertEquals($check, $input);

        $request_uri = '/file.jpg';
        $request_method = 'GET';
        $check = array(
            'params'   => array('image' => '.jpg'),
            'callback' => function() { return 'GET /file/[\.jpg|\.gif|\.png:image]?'; }
        );
        $input = $router->parse($request_uri, $request_method, $routes);
        $this->assertEquals($check, $input);

        $request_uri = '/file.bmp';
        $request_method = 'GET';
        $check = array(
            'params'   => null,
            'callback' => null
        );
        $input = $router->parse($request_uri, $request_method, $routes);
        $this->assertEquals($check, $input);

        $request_uri = '/news';
        $request_method = 'GET';
        $check = array(
            'params'   => array(),
            'callback' => function() { return 'GET /news/[:params]?'; }
        );
        $input = $router->parse($request_uri, $request_method, $routes);
        $this->assertEquals($check, $input);

        $request_uri = '/newsflash';
        $request_method = 'GET';
        $check = array(
            'params'   => array('params' => 'flash'),
            'callback' => function() { return 'GET /news/[:params]?'; }
        );
        $input = $router->parse($request_uri, $request_method, $routes);
        $this->assertEquals($check, $input);


        $null_route = array(
            array('get', null, function() { return 'GET 404'; })
        );
        $routes_null = array_merge($routes, $null_route);

        $request_uri = '/something/that/doesnt/exist/here';
        $request_method = 'GET';
        $check = array(
            'params'   => array(),
            'callback' => function() { return 'GET 404'; }
        );
        $input = $router->parse($request_uri, $request_method, $routes_null);
        $this->assertEquals($check, $input);


        $catchall_route = array(
            array('get', '*', function() { return 'GET 404'; })
        );
        $routes_all = array_merge($routes, $catchall_route);

        $request_uri = '/something/that/doesnt/exist';
        $request_method = 'GET';
        $check = array(
            'params'   => array(),
            'callback' => function() { return 'GET 404'; }
        );
        $input = $router->parse($request_uri, $request_method, $routes_all);
        $this->assertEquals($check, $input);
    }

}
?>
