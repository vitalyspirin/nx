<?php

require __DIR__ . '/bootstrap/autoloader.php';
require __DIR__ . '/bootstrap/cache.php';
require __DIR__ . '/bootstrap/db.php';


$config = array(
    'namespace' => array(
        'controller' => 'app\controller\\',
        'model'      => 'app\model\\'
    ),
    'path'      => array(
        'cache' => dirname(__DIR__) . '/resource/cache/',
        'view'  => dirname(__DIR__) . '/view/'
    ),
    'version'   => array(
        'major'    => 0,
        'minor'    => 7,
        'status'   => 0,
        'revision' => 0
    )

);

\nx\lib\Library::define($config);

?>
