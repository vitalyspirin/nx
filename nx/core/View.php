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
    *  The form helper object.
    *
    *  @var object
    *  @access protected
    */
    protected $_form;

   /**
    *  Loads the configuration settings for the view.
    *
    *  @param array $config         The configuration options.
    *  @access public
    *  @return void
    */
    public function __construct(array $config = array()) {
        $defaults = array(
            'cache_dir' => dirname(dirname(__DIR__)) . '/app/resource/cache/',
            'classes'   => array(
                'compiler' => 'nx\lib\Compiler',
                'form'     => 'nx\lib\Form'
            ),
            'template' => null,
            'view_dir' => dirname(dirname(__DIR__)) . '/app/view/'
        );

        parent::__construct($config + $defaults);
    }

   /**
    *  Initializes a form for use within the view.
    *
    *  @access protected
    *  @return void
    */
    protected function _init() {
        parent::_init();

        $form = $this->_config['classes']['form'];
        $this->_form = new $form();
    }

   /**
    *  Renders a given file with the supplied variables.
    *
    *  @param string $file          The file to be rendered.
    *  @param mixed $vars           The variables to be substituted in the view.
    *  @access public
    *  @return string
    */
    public function render($file, $vars = null) {
        $path = $this->_config['view_dir'];
        if ( !is_null($this->_config['template']) ) {
            $path .= $this->_config['template'] . '/';
        }
        $file = $path . $file . '.html';

        if ( is_array($vars) ) {
            extract($vars);
        }

        $compiler = $this->_config['classes']['compiler'];
        $options = array('path' => $this->_config['cache_dir']);
        $template = $compiler::compile($file, $options);

        ob_start();
        require $template;
        return ob_get_clean();
    }

   /**
    *  Renders a 404 page.
    *
    *  @access public
    *  @return void
    */
    public function throw_404() {
        ob_start();
        require $this->_config['view_dir']
            . $this->_config['template']  . '/404.html';
        return ob_get_clean();
    }

}

?>
