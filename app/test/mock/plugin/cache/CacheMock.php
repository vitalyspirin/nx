<?php

namespace nx\test\mock\plugin\cache;

class CacheMock extends \nx\core\Object {

    public function __construct(array $config = array()) {
        $defaults = array(
            'host' => 'localhost'
        );
        parent::__construct($config + $defaults);
    }

}

?>
