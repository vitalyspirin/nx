<?php

/**
 * NX
 *
 * @author    Nick Sinopoli <NSinopoli@gmail.com>
 * @copyright Copyright (c) 2011-2012, Nick Sinopoli
 * @license   http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace nx\lib;

/*
 *  The `Compiler` class is used to compile HTML pages with custom
 *  syntax into templates.  It is also used to serve cached templates.
 *
 *  @package lib
 */
class Compiler {

   /**
    *  Retrieves the compiled filename, and caches the file
    *  if it is not already cached.
    *
    *  @param string $file    The file location.
    *  @param string $path    The path where where the cached file should be
    *                         stored.
    *  @access public
    *  @return string
    */
    public static function compile($file, $path) {
        $stats = stat($file);
        $dir = dirname($file);
        $location = basename(dirname($dir)) . '_' . basename($dir)
            . '_' . basename($file, '.html');
        $template = "template_{$location}_{$stats['mtime']}_{$stats['ino']}_"
            . "{$stats['size']}.html";
        $template = $path . $template;

        if ( file_exists($template) ) {
            return $template;
        }

        $template_dir = dirname($template);
        if ( !file_exists($template_dir) ) {
            mkdir($template_dir, 0755, true);
        }

        $compiled = self::_replace(file_get_contents($file));
        file_put_contents($template, $compiled);

        $pattern = "{$template_dir}/template_{$location}_*.html";
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
    *  @param string $template    The template.
    *  @access public
    *  @return string
    */
    protected static function _replace($template) {
        $replace = array(
            '/\<\?=\s*\$this->(.+?)\s*;?\s*\?>/msx' =>
                '<?php echo $this->$1; ?>',

            '/\$e\((.+?)\)\s*;/msx'                 =>
                'echo $this->_form->escape($1);',

            '/\<\?=\s*(.+?)\s*;?\s*\?>/msx'         =>
                '<?php echo $this->_form->escape($1); ?>'
        );

        return preg_replace(
            array_keys($replace), array_values($replace), $template
        );
    }

}
