<?php

$config = array(

    'default' => array(
        'plugin'   => 'PDO_MySQL',
        'database' => '',
        'host'     => 'localhost',
        'username' => 'root',
        'password' => 'admin'
    )

);

\nx\lib\Connections::add_db($config);

?>
