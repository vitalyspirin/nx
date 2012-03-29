<?php

$config = array(

    'development' => array(
        'plugin'   => 'app\plugin\db\PDO_MySQL',
        'database' => '',
        'host'     => 'localhost',
        'port'     => 3306,
        'username' => 'root',
        'password' => 'admin'
    ),

    'test' => array(
        'plugin'   => 'app\plugin\db\PDO_MySQL',
        'database' => '',
        'host'     => 'localhost',
        'port'     => 3306,
        'username' => 'root',
        'password' => 'admin'
    ),

    'production' => array(
        'plugin'   => 'app\plugin\db\PDO_MySQL',
        'database' => '',
        'host'     => 'localhost',
        'port'     => 3306,
        'username' => 'root',
        'password' => 'admin'
    )
);

\app\lib\Connections::add_db($config);

?>
