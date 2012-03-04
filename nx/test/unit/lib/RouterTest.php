<?php

namespace nx\test\lib;

use nx\lib\Router;

class RouterTest extends \PHPUnit_Framework_TestCase {

    public function test_Parse_ReturnsArray() {
        $routes = array(
            array('GET', '/', 'app\controller\Login::index'),
            array('POST', '/', 'app\controller\Login::create'),
            array('PUT', '/', 'app\controller\Login::update'),
            array('GET', '/users', 'app\controller\User::index'),
            array('GET', '/users/[i:id]', 'app\controller\User::index'),
            array('GET', '/name', 'app\controller\User::name'),
            array(array('get', 'put'), '/name/[a:name]', 'app\controller\User::name'),
            array('delete', '/name/[h:id]', 'app\controller\User::name'),
            array('get', '/phone/[a:type]', 'app\controller\User::phone'),
            array('get', '/type/[:type]', 'app\controller\Type::handle'),
            array('get', '/friends/[*:friends][i:id]', 'app\controller\Friends::load'),
            array('post', '/entry/[new|create:action]', 'app\controller\Entry::new'),
            array('put', '/entry/[:id]/[*:params]', 'app\controller\Entry::update'),
            array('get', '/file[\.jpg|\.gif|\.png:image]?', 'app\controller\File::download'),
            array('get', '/news[:params]?', 'app\controller\News::index'),
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

        $request_uri = '/users/bob';
        $request_method = 'GET';
        $check = array(
            'args'     => null,
            'callback' => null
        );
        $input = Router::parse($request_uri, $request_method);
        $this->assertEquals($check, $input);

        $request_uri = '/name/jimmy';
        $request_method = 'PUT';
        $check = array(
            'args'     => array('name' => 'jimmy'),
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
            'args'     => null,
            'callback' => null
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

        $request_uri = '/type/somethingnew';
        $request_method = 'GET';
        $check = array(
            'args'     => array('type' => 'somethingnew'),
            'callback' => 'app\controller\Type::handle'
        );
        $input = Router::parse($request_uri, $request_method);
        $this->assertEquals($check, $input);

        $request_uri = '/friends/sally-382';
        $request_method = 'GET';
        $check = array(
            'args'     => array('friends' => 'sally-', 'id' => '382'),
            'callback' => 'app\controller\Friends::load'
        );
        $input = Router::parse($request_uri, $request_method);
        $this->assertEquals($check, $input);

        $request_uri = '/entry/create';
        $request_method = 'POST';
        $check = array(
            'args'     => array('action' => 'create'),
            'callback' => 'app\controller\Entry::new'
        );
        $input = Router::parse($request_uri, $request_method);
        $this->assertEquals($check, $input);

        $request_uri = '/entry/new';
        $request_method = 'POST';
        $check = array(
            'args'     => array('action' => 'new'),
            'callback' => 'app\controller\Entry::new'
        );
        $input = Router::parse($request_uri, $request_method);
        $this->assertEquals($check, $input);

        $request_uri = '/entry/87/date/tomorrow';
        $request_method = 'PUT';
        $check = array(
            'args'     => array('id' => '87', 'params' => 'date/tomorrow'),
            'callback' => 'app\controller\Entry::update'
        );
        $input = Router::parse($request_uri, $request_method);
        $this->assertEquals($check, $input);

        $request_uri = '/file';
        $request_method = 'GET';
        $check = array(
            'args'     => array(),
            'callback' => 'app\controller\File::download'
        );
        $input = Router::parse($request_uri, $request_method);
        $this->assertEquals($check, $input);

        $request_uri = '/file.jpg';
        $request_method = 'GET';
        $check = array(
            'args'     => array('image' => '.jpg'),
            'callback' => 'app\controller\File::download'
        );
        $input = Router::parse($request_uri, $request_method);
        $this->assertEquals($check, $input);

        $request_uri = '/file.bmp';
        $request_method = 'GET';
        $check = array(
            'args'     => null,
            'callback' => null
        );
        $input = Router::parse($request_uri, $request_method);
        $this->assertEquals($check, $input);

        $request_uri = '/news';
        $request_method = 'GET';
        $check = array(
            'args'     => array(),
            'callback' => 'app\controller\News::index'
        );
        $input = Router::parse($request_uri, $request_method);
        $this->assertEquals($check, $input);

        $request_uri = '/news';
        $request_method = 'GET';
        $check = array(
            'args'     => array(),
            'callback' => 'app\controller\News::index'
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
