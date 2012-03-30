<?php

$root = PATH_SEPARATOR . dirname(dirname(__DIR__));
set_include_path(get_include_path() . $root);

spl_autoload_register(function($class) {
    $file = str_replace('\\', '/', $class) . '.php';
    require $file;
});

?>
