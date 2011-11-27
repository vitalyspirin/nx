<?php

$config = array(

    'development' => array(
        'enabled'       => false,
        'plugin'        => 'Memcached',
        'host'          => 'localhost',
        'persistent_id' => ''
    ),

    'test' => array(
        'enabled'       => true,
        'plugin'        => 'Memcached',
        'host'          => 'localhost',
        'persistent_id' => ''
    ),

    'production' => array(
        'enabled'       => true,
        'plugin'        => 'Memcached',
        'host'          => 'localhost',
        'persistent_id' => ''
    )

);

\nx\lib\Connections::add_cache($config);

?>
