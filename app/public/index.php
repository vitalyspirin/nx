<?php
    require dirname(__DIR__) . '/config/bootstrap.php';

    $dispatcher = new \nx\core\Dispatcher();
    echo $dispatcher->render($_SERVER['REQUEST_URI']);
?>
