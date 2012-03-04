<?php

$config = array(
    'plugin'   => 'PDO_MySQL',
    'database' => '',
    'host'     => 'localhost',
    'username' => 'root',
    'password' => 'admin'
);

\nx\lib\Connections::set_db($config);

?>
