<?php

/**
 * NX
 *
 * @author    Nick Sinopoli <NSinopoli@gmail.com>
 * @copyright Copyright (c) 2011-2012, Nick Sinopoli
 * @license   http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace nx\core;

/*
 *
 *  @package core
 */
class Object {

   /**
    *  Returns the object's classname without the namespace.
    *
    *  @access public
    *  @return string
    */
    public function classname() {
        $class = explode('\\', get_called_class());
        return end($class);
    }

}

?>
