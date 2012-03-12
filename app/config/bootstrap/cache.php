<?php

$config = array(

    'default' => array(
        'enabled'       => false,
        'plugin'        => 'nx\plugin\cache\Memcached',
        'host'          => 'localhost'
    )

);

\nx\lib\Connections::add_cache($config);

?>
