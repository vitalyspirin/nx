<?php

$config = array(
    'enabled'       => false,
    'plugin'        => 'Memcached',
    'host'          => 'localhost',
    'persistent_id' => ''
);

\nx\lib\Connections::set_cache($config);

?>
