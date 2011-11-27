<?php

$config = array(

    'development' => array(
        'plugin'   => 'PDO_MySQL',
        'database' => '',
        'host'     => 'localhost',
        'username' => 'root',
        'password' => 'admin'
    ),

    'test' => array(
        'plugin'   => 'PDO_MySQL',
        'database' => '',
        'host'     => 'localhost',
        'username' => 'root',
        'password' => 'admin'
    ),

    'production' => array(
        'plugin'   => 'PDO_MySQL',
        'database' => '',
        'host'     => 'localhost',
        'username' => 'root',
        'password' => 'admin'
    )

);

\nx\lib\Connections::add_db($config);

?>
