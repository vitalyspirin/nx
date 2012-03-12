<?php

$config = array(

    'default' => array(
        'plugin'   => 'nx\plugin\db\PDO_MySQL',
        'database' => '',
        'host'     => 'localhost',
        'username' => 'root',
        'password' => 'admin'
    )

);

\nx\lib\Connections::add_db($config);

?>
