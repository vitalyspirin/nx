<?php

/**
 * NX
 *
 * @author    Nick Sinopoli <NSinopoli@gmail.com>
 * @copyright Copyright (c) 2011-2012, Nick Sinopoli
 * @license   http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace nx\core;

class Session extends \nx\core\Object {

   /**
    *  Initializes the session handler.
    *
    *  @access protected
    *  @return void
    */
    protected function _init() {
        session_start();
    }

   /**
    *  Creates a new session.
    *
    *  @param array $vars    The session variables to be set.
    *  @access private
    *  @return bool
    */
    public function create($vars) {
        session_regenerate_id(true);
        $_SESSION = array('_fingerprint' => $this->_get_fingerprint()) + $vars;

        return true;
    }

   /**
    *  Returns a session variable.
    *
    *  @param string $field    The session variable identifier.
    *  @access public
    *  @return mixed
    */
    public function __get($field) {
        return ( isset($_SESSION[$field]) ) ? $_SESSION[$field] : null;
    }

   /**
    *  Returns the session fingerprint.  The only thing that's unlikely
    *  to change between requests is the user agent.  While this can be spoofed
    *  easily, it's still an additional obstacle to overcome in session
    *  hijacking.
    *
    *  @access private
    *  @return string
    */
    private function _get_fingerprint() {
        return $_SERVER['HTTP_USER_AGENT'];
    }

   /**
    *  Checks whether the current session is valid.
    *
    *  @access public
    *  @return bool
    */
    public function is_valid() {
        $fingerprint = $this->_get_fingerprint();
        return ( $this->_fingerprint && $this->_fingerprint == $fingerprint );
    }

   /**
    *  Ends the current session and starts a new one.
    *
    *  @access public
    *  @return void
    */
    public function reset() {
        session_destroy();
        session_start();
        session_regenerate_id();
        $_SESSION = array();
    }

}

?>
