<?php
    require dirname(__DIR__) . '/config/bootstrap.php';

    $dependencies = array(

    );
    $dispatcher = new \nx\core\Dispatcher();
    echo $dispatcher->handle(new \nx\core\Request());
?>
