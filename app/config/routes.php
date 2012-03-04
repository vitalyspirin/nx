<?php

$routes = array(
    array('get', '/', 'app\controller\Dashboard::index')
);

nx\lib\Router::set_routes($routes);

?>
