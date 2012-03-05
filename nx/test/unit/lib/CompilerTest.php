<?php

namespace nx\test\lib;

use nx\lib\Compiler;

class CompilerTest extends \PHPUnit_Framework_TestCase {

    protected $_compiled_dir;
    protected $_file;

    public function setUp() {
        $dir = dirname(dirname(dirname(__FILE__)));
        $this->_compiled_dir = "{$dir}/resource/cache/";
        $this->_file = "{$dir}/resource/test.html";
        $contents = "<html>
    <body>
        <?=\$hello;?>
        <?=str_replace('hi', 'howdy', \$hello);?>
        <?=\$this->_form->email(array('class' => 'test'));?>
        <?php foreach ( \$items as \$item ) {
            \$e(\$item);
        } ?>
        <?php echo \$hello; ?>
    </body>
</html>";

        file_put_contents($this->_file, $contents);
    }

    public function tearDown() {
        $pattern = $this->_compiled_dir . '*.html';
        foreach ( glob($pattern) as $file ) {
            unlink($file);
        }
        rmdir($this->_compiled_dir);
        unlink($this->_file);
    }

    public function test_CompileFile_ReturnsFileLocation() {
        $path = $this->_compiled_dir;
        $result = Compiler::compile($this->_file, $path);
        $this->assertTrue(file_exists($result));
    }

    public function test_CompileFile_ReturnsCompiledContents() {
        $path = $this->_compiled_dir;
        $result = Compiler::compile($this->_file, $path);
        $result = file_get_contents($result);
        $check = "<html>
    <body>
        <?php echo \$this->_form->escape(\$hello); ?>
        <?php echo \$this->_form->escape(str_replace('hi', 'howdy', \$hello)); ?>
        <?php echo \$this->_form->email(array('class' => 'test')); ?>
        <?php foreach ( \$items as \$item ) {
            echo \$this->_form->escape(\$item);
        } ?>
        <?php echo \$hello; ?>
    </body>
</html>";
        $this->assertEquals($result, $check);
    }

    public function test_CompileFile_HitsCache() {
        $path = $this->_compiled_dir;

        $first = Compiler::compile($this->_file, $path);
        $first_glob = glob($this->_compiled_dir . '/*');

        clearstatcache();
        $cached = Compiler::compile($this->_file, $path);
        $second_glob = glob($this->_compiled_dir . '/*');
        $this->assertEquals($cached, $first);
        $this->assertEquals($first_glob, $second_glob);

        file_put_contents($this->_file, 'Some new stuff');
        clearstatcache();
        $new = Compiler::compile($this->_file, $path);
        $new_glob = glob($this->_compiled_dir . '/*');

        $this->assertNotEquals($cached, $new);
        $this->assertEquals(count($first_glob), count($new_glob));
        $this->assertNotEquals($first_glob, $new_glob);
    }

}

?>
