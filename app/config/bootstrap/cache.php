<?php

$config = array(

    'default' => array(
        'enabled'       => false,
        'plugin'        => 'Memcached',
        'host'          => 'localhost',
        'persistent_id' => ''
    )

);

\nx\lib\Connections::add_cache($config);

?>
