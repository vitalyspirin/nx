<?php

require __DIR__ . '/bootstrap/autoloader.php';
require __DIR__ . '/bootstrap/cache.php';
require __DIR__ . '/bootstrap/db.php';


$config = array(
    'guest_redirect' => '/login',

    'namespace'      => array(
        'controller' => 'app\controller\\',
        'model'      => 'app\model\\'
    ),

    'path'           => array(
        'cache' => dirname(__DIR__) . '/resource/cache/',
        'view'  => dirname(__DIR__) . '/view/'
    ),

    'version'        => array(
        'major'     => 0,
        'minor'     => 0,
        'iteration' => 0,
        'status'    => 0,
        'revision'  => 1
    )
);

\nx\lib\Library::define($config);

?>
