<?php

$nx_root = PATH_SEPARATOR . dirname(dirname(__DIR__));
set_include_path(get_include_path() . $nx_root);

spl_autoload_register(function($class) {
    $file = str_replace('\\', '/', $class) . '.php';
    require_once $file;
});


/*
require __DIR__ . '/bootstrap/cache.php';
require __DIR__ . '/bootstrap/db.php';


$config = array(
    'guest_redirect' => '/login',

    'namespace'      => array(
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
 */

?>
