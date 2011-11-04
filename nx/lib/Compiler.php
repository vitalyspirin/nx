<?php

/**
 * NX
 *
 * @author    Nick Sinopoli <NSinopoli@gmail.com>
 * @copyright Copyright (c) 2011, Nick Sinopoli
 * @license   http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace nx\lib;

/*
 *  The `Compiler` class is used to compile HTML pages with custom
 *  syntax into templates.  It is also used to serve
 *  cached templates.
 *
 *  @package lib
 */
class Compiler {

   /**
    *  The configuration settings.
    *
    *  @var array
    *  @access protected
    */
    protected static $_config = array(
        'classes'   => array(
            'dispatcher' => 'nx\lib\Dispatcher',
        )
    );

   /**
    *  Retrieves the compiled filename, and caches the file
    *  if it is not already cached.
    *
    *  @param string $file          The file location.
    *  @param array $options        The compilation options, which take
    *                               the following keys:
    *                               'path' - The path relative to the file
    *                                        where the cached file should be
    *                                        stored.
    *  @access public
    *  @return string
    */
    public static function compile($file, $options = array()) {
        $options += array(
            'path' => 'compiled/'
        );

        $stats = stat($file);
        $dir = dirname($file);
        $location = basename(dirname($dir)) . '_' . basename($dir)
            . '_' . basename($file, '.html');
        $template = 'template_' . $location . '_' . $stats['mtime']
            . '_' . $stats['ino'] . '_' . $stats['size'] . '.html';
        $template = $dir . '/' . $options['path'] . $template;

        if ( file_exists($template) ) {
            return $template;
        }

        $compiled = self::_replace(file_get_contents($file));
        $template_dir = dirname($template);
        if ( !is_dir($template_dir) && !mkdir($template_dir, 0755, true) ) {
           return false;
        }

        if ( !is_writable($template_dir)
            || file_put_contents($template, $compiled) === false ) {
            return false;
        }

        $pattern = $template_dir . '/template_' . $location . '_*.html';
        foreach ( glob($pattern) as $old ) {
            if ( $old !== $template ) {
                unlink($old);
            }
        }
        return $template;
    }

   /**
    *  Replaces a template with custom syntax.
    *
    *  @param string $template      The template.
    *  @access public
    *  @return string
    */
    protected static function _replace($template) {
        $dispatcher = self::$_config['classes']['dispatcher'];
        $replace = array(
            '/\<\?=\s*Dispatcher::(.+?)\s*;?\s*\?>/msx' => '<?php echo ' . $dispatcher . '::$1; ?>',
            '/\<\?=\s*\$this->(.+?)\s*;?\s*\?>/msx'     => '<?php echo $this->$1; ?>',
            '/\<\?=\s*(.+?)\s*;?\s*\?>/msx'             => '<?php echo $this->_form->escape($1); ?>'
        );

        return preg_replace(array_keys($replace), array_values($replace), $template);
    }

}
