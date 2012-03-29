<?php

/**
 * NX
 *
 * @author    Nick Sinopoli <NSinopoli@gmail.com>
 * @copyright Copyright (c) 2011, Nick Sinopoli
 * @license   http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace nx\core;

/*
 *  The `View` class is the parent class of all
 *  application views.  It provides access to a
 *  form helper for assistance with creating common
 *  page elements.
 *
 *  @package core
 */
class View extends Object {

   /**
    *  The configuration settings.
    *
    *  @var array
    *  @access protected
    */
    protected $_config = array();

   /**
    *  The form helper object.
    *
    *  @var object
    *  @access public
    */
    public $form;

   /**
    *  Loads the configuration settings for the view.
    *
    *  @param array $config    The configuration options.
    *  @access public
    *  @return void
    */
    public function __construct(array $config = array()) {
        $root = dirname(dirname(__DIR__));
        $defaults = array(
            'libs'   => array(
                'compiler' => 'nx\lib\Compiler',
                'form'     => 'nx\lib\Form'
            ),
            'paths' => array(
                'cache' => "{$root}/app/resource/cache/",
                'view'  => "{$root}/app/view/"
            )
        );

        $this->_config = $config + $defaults;

        $form = $this->_config['libs']['form'];
        $this->form = new $form();
    }

   /**
    *  Renders a given file with the supplied variables.
    *
    *  @param string $file    The file to be rendered.
    *  @param mixed $vars     The variables to be substituted in the view.
    *  @access public
    *  @return string
    */
    public function render($file, $vars = null) {
        $file = "{$this->_config['paths']['view']}{$file}.html";

        $compiler = $this->_config['libs']['compiler'];
        $cache_path = $this->_config['paths']['cache'];
        // Named as such to avoid name collisions with the variables pulled
        // from extract($vars)
        $____template____ = $compiler::compile($file, $cache_path);

        if ( is_array($vars) ) {
            extract($vars);
        }
        ob_start();
        require $____template____;
        return ob_get_clean();
    }

}

?>
