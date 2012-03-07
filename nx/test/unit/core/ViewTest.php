<?php

namespace nx\test\core;

use nx\core\View;

class ViewTest extends \PHPUnit_Framework_TestCase {

    protected $_compiled_dir;
    protected $_file;
    protected $_path;
    protected $_view;
    protected $_view_name;

    public function setUp() {
        $dir = dirname(dirname(dirname(__FILE__)));
        $paths = array(
            'cache' => "{$dir}/resource/cache/",
            'view'  => "{$dir}/resource/"
        );

        $this->_path = $paths['view'];
        $this->_compiled_dir = $paths['cache'];
        $this->_view_name = 'test';
        $this->_file = $this->_path . $this->_view_name . '.html';
        $contents = "<html>
    <body>\n
        <?=\$hello;?> <?=str_replace('test', 'yes', \$hello);?>\n
        <?=\$this->form->email(array('class' => 'test'));?>\n
        <?php echo \$hello; ?>\n
    </body>\n
</html>";

        file_put_contents($this->_file, $contents);
        $this->_view = new View(compact('paths'));
    }

    public function tearDown() {
        $pattern = $this->_compiled_dir . '*.html';
        foreach ( glob($pattern) as $file ) {
            unlink($file);
        }
        rmdir($this->_compiled_dir);
        unlink($this->_file);
    }

    public function test_RenderFile_ReturnsHTML() {
        $hello = 'test please';
        $result = $this->_view->render($this->_view_name, compact('hello'));
        $check = "<html>
    <body>\n
        test please yes please
        <input type='email' class='test' />
        test please
    </body>\n
</html>";
        $this->assertEquals($result, $check);
    }


}

?>
