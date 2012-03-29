<?php

$nx_root = PATH_SEPARATOR . dirname(dirname(__DIR__));
set_include_path(get_include_path() . $nx_root);

spl_autoload_register(function($class) {
    $file = str_replace('\\', '/', $class) . '.php';
    require_once $file;
});

require __DIR__ . '/routes.php';
require __DIR__ . '/bootstrap/db.php';

?>
