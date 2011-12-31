<?php

namespace nx\test\lib;

use nx\lib\Form;

class ModelMock {
    protected $id;
    protected $test_name = 'test value';

    public function __get($property) {
        return $this->$property;
    }

    public function classname() {
        return 'ModelMock';
    }

    public function get_pk() {
        return $this->id;
    }

    public function set_id($val) {
        $this->id = $val;
    }

}

class ModelMock2 {
    protected $id;
    protected $test_name = 'test value';

    public function __get($property) {
        return $this->$property;
    }

    public function classname() {
        return 'ModelMock2';
    }

    public function get_pk() {
        return $this->id;
    }

    public function set_id($val) {
        $this->id = $val;
    }

}

class FormTest extends \PHPUnit_Framework_TestCase {

    protected $_form;

    public function setUp() {
        $this->_form = new Form();
    }

    public function test_FormHelpersNoBindings_ReturnsHtml() {
        $attributes = array(
            'id'    => 'test_id',
            'class' => 'test_class',
            'name'  => 'test_name',
            'value' => 'test value'
        );

        $input = $this->_form->checkbox($attributes);
        $check = "<input type='checkbox' id='test_id' class='test_class' name='test_name' value='test value' />";
        $this->assertEquals($check, $input);

        $input = $this->_form->email($attributes);
        $check = "<input type='email' id='test_id' class='test_class' name='test_name' value='test value' />";
        $this->assertEquals($check, $input);

        $input = $this->_form->hidden($attributes);
        $check = "<input type='hidden' id='test_id' class='test_class' name='test_name' value='test value' />";
        $this->assertEquals($check, $input);

        $input = $this->_form->number($attributes);
        $check = "<input type='number' id='test_id' class='test_class' name='test_name' value='test value' />";
        $this->assertEquals($check, $input);

        $input = $this->_form->search($attributes);
        $check = "<input type='search' id='test_id' class='test_class' name='test_name' value='test value' />";
        $this->assertEquals($check, $input);

        $input = $this->_form->text($attributes);
        $check = "<input type='text' id='test_id' class='test_class' name='test_name' value='test value' />";
        $this->assertEquals($check, $input);

        $input = $this->_form->textarea($attributes);
        $check = "<textarea id='test_id' class='test_class' name='test_name'>test value</textarea>";
        $this->assertEquals($check, $input);

        $attributes = array(
            'id' => 'test_form',
            'action' => '/user/42',
            'method' => 'post'
        );

        $form = $this->_form->form($attributes);
        $check = "<form id='test_form' action='/user/42' method='post' />";
        $this->assertEquals($check, $form);

        $attributes = array(
            'name'  => 'test_name',
            'value' => array(
                'green'  => 'green',
                'orange' => 'orange',
                'red'    => 'red',
                'blue'   => 'blue'
            )
        );

        $input = $this->_form->radios($attributes);
        $check = "<input type='radio' name='test_name' value='green' /> <label>green</label>"
            . "<input type='radio' name='test_name' value='orange' /> <label>orange</label>"
            . "<input type='radio' name='test_name' value='red' /> <label>red</label>"
            . "<input type='radio' name='test_name' value='blue' /> <label>blue</label>";
        $this->assertEquals($check, $input);

        $attributes['value'] = array(
            '17' => 'green',
            '18' => 'orange',
            '19' => 'red',
            '20' => 'blue'
        );

        $input = $this->_form->radios($attributes);
        $check = "<input type='radio' name='test_name' value='17' /> <label>green</label>"
            . "<input type='radio' name='test_name' value='18' /> <label>orange</label>"
            . "<input type='radio' name='test_name' value='19' /> <label>red</label>"
            . "<input type='radio' name='test_name' value='20' /> <label>blue</label>";
        $this->assertEquals($check, $input);

        $attributes['id'] = array('one', 'two', 'three', 'four');
        $input = $this->_form->radios($attributes);
        $check = "<input type='radio' name='test_name' id='one' value='17' /> <label for='one'>green</label>"
            . "<input type='radio' name='test_name' id='two' value='18' /> <label for='two'>orange</label>"
            . "<input type='radio' name='test_name' id='three' value='19' /> <label for='three'>red</label>"
            . "<input type='radio' name='test_name' id='four' value='20' /> <label for='four'>blue</label>";
        $this->assertEquals($check, $input);

        $attributes = array(
            'class' => 'test_class',
            'name'  => 'test_name'
        );

        $values = array(
            'green'  => 'green',
            'orange' => 'orange',
            'red'    => 'red',
            'blue'   => 'blue'
        );

        $input = $this->_form->select($attributes, $values);
        $check = "<select class='test_class' name='test_name'>"
            . "<option value='green'>green</option><option value='orange'>orange</option>"
            . "<option value='red'>red</option><option value='blue'>blue</option>"
            . "</select>";
        $this->assertEquals($check, $input);

        $values = array(
            '17' => 'green',
            '18' => 'orange',
            '19' => 'red',
            '20' => 'blue'
        );

        $input = $this->_form->select($attributes, $values);
        $check = "<select class='test_class' name='test_name'>"
            . "<option value='17'>green</option><option value='18'>orange</option>"
            . "<option value='19'>red</option><option value='20'>blue</option>"
            . "</select>";
        $this->assertEquals($check, $input);

        $attributes = array(
            'id'    => 'test_id',
            'class' => 'test_class',
            'autofocus',
            'name'  => 'test_name',
            'value' => 'test value'
        );

        $input = $this->_form->email($attributes);
        $check = "<input type='email' id='test_id' class='test_class' autofocus name='test_name' value='test value' />";
        $this->assertEquals($check, $input);

    }

    public function test_FormHelpersWithBindings_ReturnsHtml() {
        $binding = new ModelMock();

        // Test with a custom value
        $attributes = array(
            'id'    => 'test_id',
            'class' => 'test_class',
            'name'  => 'test_name',
            'value' => 'test'
        );
        $input = $this->_form->checkbox($attributes, $binding);
        $check = "<input type='checkbox' id='test_id' class='test_class' name='ModelMock[test_name]' value='test' />";
        $this->assertEquals($check, $input);

        $attributes = array(
            'id'    => 'test_id',
            'class' => 'test_class',
            'name'  => 'test_name_2',
            'value' => 'test'
        );
        $input = $this->_form->email($attributes, $binding);
        $check = "<input type='email' id='test_id' class='test_class' name='ModelMock[test_name_2]' value='test' />";
        $this->assertEquals($check, $input);

        $attributes = array(
            'id'    => 'test_id',
            'class' => 'test_class',
            'name'  => 'test_name_3',
            'value' => 'test'
        );
        $input = $this->_form->hidden($attributes, $binding);
        $check = "<input type='hidden' id='test_id' class='test_class' name='ModelMock[test_name_3]' value='test' />";
        $this->assertEquals($check, $input);

        $attributes = array(
            'id'    => 'test_id',
            'class' => 'test_class',
            'name'  => 'test_name_4',
            'value' => 'test'
        );
        $input = $this->_form->number($attributes, $binding);
        $check = "<input type='number' id='test_id' class='test_class' name='ModelMock[test_name_4]' value='test' />";
        $this->assertEquals($check, $input);

        $attributes = array(
            'id'    => 'test_id',
            'class' => 'test_class',
            'name'  => 'test_name_5',
            'value' => 'test'
        );
        $input = $this->_form->search($attributes, $binding);
        $check = "<input type='search' id='test_id' class='test_class' name='ModelMock[test_name_5]' value='test' />";
        $this->assertEquals($check, $input);

        $attributes = array(
            'id'    => 'test_id',
            'class' => 'test_class',
            'name'  => 'test_name_6',
            'value' => 'test'
        );
        $input = $this->_form->text($attributes, $binding);
        $check = "<input type='text' id='test_id' class='test_class' name='ModelMock[test_name_6]' value='test' />";
        $this->assertEquals($check, $input);

        $attributes = array('id' => 'test_form');
        $form = $this->_form->form($attributes, $binding);
        $check = "<form id='test_form' action='/modelmock' method='post' />";
        $this->assertEquals($check, $form);

        // Test with value the same as $binding->name
        $attributes = array(
            'id'    => 'test_id',
            'class' => 'test_class',
            'name'  => 'test_name',
            'value' => 'test value'
        );
        $input = $this->_form->checkbox($attributes, $binding);
        $check = "<input type='checkbox' id='test_id' class='test_class' name='ModelMock[test_name]' value='test value' checked='checked' />";
        $this->assertEquals($check, $input);

        $attributes = array(
            'id'    => 'test_id',
            'class' => 'test_class',
            'name'  => 'test_name_2',
            'value' => 'test value'
        );
        $input = $this->_form->email($attributes, $binding);
        $check = "<input type='email' id='test_id' class='test_class' name='ModelMock[test_name_2]' value='test value' />";
        $this->assertEquals($check, $input);

        $attributes = array(
            'id'    => 'test_id',
            'class' => 'test_class',
            'name'  => 'test_name_3',
            'value' => 'test value'
        );
        $input = $this->_form->hidden($attributes, $binding);
        $check = "<input type='hidden' id='test_id' class='test_class' name='ModelMock[test_name_3]' value='test value' />";
        $this->assertEquals($check, $input);

        $attributes = array(
            'id'    => 'test_id',
            'class' => 'test_class',
            'name'  => 'test_name_4',
            'value' => 'test value'
        );
        $input = $this->_form->number($attributes, $binding);
        $check = "<input type='number' id='test_id' class='test_class' name='ModelMock[test_name_4]' value='test value' />";
        $this->assertEquals($check, $input);

        $attributes = array(
            'id'    => 'test_id',
            'class' => 'test_class',
            'name'  => 'test_name_5',
            'value' => 'test value'
        );
        $input = $this->_form->search($attributes, $binding);
        $check = "<input type='search' id='test_id' class='test_class' name='ModelMock[test_name_5]' value='test value' />";
        $this->assertEquals($check, $input);

        $attributes = array(
            'id'    => 'test_id',
            'class' => 'test_class',
            'name'  => 'test_name_6',
            'value' => 'test value'
        );
        $input = $this->_form->text($attributes, $binding);
        $check = "<input type='text' id='test_id' class='test_class' name='ModelMock[test_name_6]' value='test value' />";
        $this->assertEquals($check, $input);


        // Test with no value, but with name
        // set as the property of our object
        $attributes = array(
            'id'    => 'test_id',
            'class' => 'test_class',
            'name'  => 'test_name'
        );
        $input = $this->_form->checkbox($attributes, $binding);
        $check = "<input type='checkbox' id='test_id' class='test_class' name='ModelMock[test_name]' value='test value' />";
        $this->assertEquals($check, $input);

        $input = $this->_form->email($attributes, $binding);
        $check = "<input type='email' id='test_id' class='test_class' name='ModelMock[test_name]' value='test value' />";
        $this->assertEquals($check, $input);

        $input = $this->_form->hidden($attributes, $binding);
        $check = "<input type='hidden' id='test_id' class='test_class' name='ModelMock[test_name]' value='test value' />";
        $this->assertEquals($check, $input);

        $input = $this->_form->number($attributes, $binding);
        $check = "<input type='number' id='test_id' class='test_class' name='ModelMock[test_name]' value='test value' />";
        $this->assertEquals($check, $input);

        $input = $this->_form->search($attributes, $binding);
        $check = "<input type='search' id='test_id' class='test_class' name='ModelMock[test_name]' value='test value' />";
        $this->assertEquals($check, $input);

        $input = $this->_form->text($attributes, $binding);
        $check = "<input type='text' id='test_id' class='test_class' name='ModelMock[test_name]' value='test value' />";
        $this->assertEquals($check, $input);

        $input = $this->_form->textarea($attributes, $binding);
        $check = "<textarea id='test_id' class='test_class' name='ModelMock[test_name]'>test value</textarea>";
        $this->assertEquals($check, $input);

    }

    public function test_RadiosWithBindings_ReturnsHtml() {
        $binding = new ModelMock();

        // Name set as the property of our object
        $attributes = array(
            'name'  => 'test_name',
            'value' => array(
                'green'      => 'green',
                'orange'     => 'orange',
                'test value' => 'yellow',
                'red'        => 'red',
                'blue'       => 'blue'
            )
        );

        $input = $this->_form->radios($attributes, $binding);
        $check = "<input type='radio' name='ModelMock[test_name]' value='green' /> <label>green</label>"
            . "<input type='radio' name='ModelMock[test_name]' value='orange' /> <label>orange</label>"
            . "<input type='radio' name='ModelMock[test_name]' value='test value' checked='checked' /> <label>yellow</label>"
            . "<input type='radio' name='ModelMock[test_name]' value='red' /> <label>red</label>"
            . "<input type='radio' name='ModelMock[test_name]' value='blue' /> <label>blue</label>";
        $this->assertEquals($check, $input);

        // Name different from the property of our object
        $attributes = array(
            'name'  => 'test',
            'value' => array(
                '17'         => 'green',
                '18'         => 'orange',
                'test value' => 'yellow',
                '19'         => 'red',
                '20'         => 'blue'
            )
        );

        $input = $this->_form->radios($attributes, $binding);
        $check = "<input type='radio' name='ModelMock[test]' value='17' /> <label>green</label>"
            . "<input type='radio' name='ModelMock[test]' value='18' /> <label>orange</label>"
            . "<input type='radio' name='ModelMock[test]' value='test value' /> <label>yellow</label>"
            . "<input type='radio' name='ModelMock[test]' value='19' /> <label>red</label>"
            . "<input type='radio' name='ModelMock[test]' value='20' /> <label>blue</label>";
        $this->assertEquals($check, $input);

        // Name different from the property of our object
        // and values as a non-associative array
        $attributes = array(
            'name'  => 'test',
            'value' => array('green', 'orange', 'yellow', 'red', 'blue')
        );

        $input = $this->_form->radios($attributes, $binding);
        $check = "<input type='radio' name='ModelMock[test]' value='green' /> <label>green</label>"
            . "<input type='radio' name='ModelMock[test]' value='orange' /> <label>orange</label>"
            . "<input type='radio' name='ModelMock[test]' value='yellow' /> <label>yellow</label>"
            . "<input type='radio' name='ModelMock[test]' value='red' /> <label>red</label>"
            . "<input type='radio' name='ModelMock[test]' value='blue' /> <label>blue</label>";
        $this->assertEquals($check, $input);
    }

    public function test_SelectWithBindings_ReturnsHtml() {
        $binding = new ModelMock();

        // Name set as the property of our object
        $attributes = array(
            'name'  => 'test_name'
        );

        $values = array(
            'green'      => 'green',
            'orange'     => 'orange',
            'test value' => 'yellow',
            'red'        => 'red',
            'blue'       => 'blue'
        );

        $input = $this->_form->select($attributes, $values, $binding);
        $check = "<select name='ModelMock[test_name]'>"
            . "<option value='green'>green</option><option value='orange'>orange</option>"
            . "<option value='test value' selected='selected'>yellow</option>"
            . "<option value='red'>red</option><option value='blue'>blue</option>"
            . "</select>";
        $this->assertEquals($check, $input);

        // Name different from the property of our object
        $attributes = array('name'  => 'test');
        $values = array(
            '17'         => 'green',
            '18'         => 'orange',
            'test value' => 'yellow',
            '19'         => 'red',
            '20'         => 'blue'
        );

        $input = $this->_form->select($attributes, $values, $binding);
        $check = "<select name='ModelMock[test]'>"
            . "<option value='17'>green</option><option value='18'>orange</option>"
            . "<option value='test value'>yellow</option>"
            . "<option value='19'>red</option><option value='20'>blue</option>"
            . "</select>";
        $this->assertEquals($check, $input);

        // Name different from the property of our object
        // and values as a non-associative array
        $attributes = array('name'  => 'test');
        $values = array('green', 'orange', 'yellow', 'red', 'blue');

        $input = $this->_form->select($attributes, $values, $binding);
        $check = "<select name='ModelMock[test]'>"
            . "<option value='green'>green</option><option value='orange'>orange</option>"
            . "<option value='yellow'>yellow</option>"
            . "<option value='red'>red</option><option value='blue'>blue</option>"
            . "</select>";
        $this->assertEquals($check, $input);
    }

    public function test_BindingsWithId_ReturnsHtml() {
        $binding = new ModelMock();
        $binding->set_id(27);

        $attributes = array('id' => 'test_form');
        $form = $this->_form->form($attributes, $binding);
        $check = "<form id='test_form' action='/modelmock/27' method='put' />";
        $this->assertEquals($check, $form);
    }

    public function test_EscapeXSS_ReturnsCleanData() {
        // Tests taken from http://ha.ckers.org/xss.html
        $tests = array(
<<<'EOD'
';alert(String.fromCharCode(88,83,83))//\';alert(String.fromCharCode(88,83,83))//";alert(String.fromCharCode(88,83,83))//\";alert(String.fromCharCode(88,83,83))//--></SCRIPT>">'><SCRIPT>alert(String.fromCharCode(88,83,83))</SCRIPT>
EOD
,

<<<'EOD'
'';!--"<XSS>=&{()}
EOD
,

<<<'EOD'
<SCRIPT SRC=http://ha.ckers.org/xss.js></SCRIPT>
EOD
,

<<<'EOD'
<IMG SRC="javascript:alert('XSS');">
EOD
,

<<<'EOD'
<IMG SRC=javascript:alert('XSS')>
EOD
,

<<<'EOD'
<IMG SRC=JaVaScRiPt:alert('XSS')>
EOD
,

<<<'EOD'
<IMG SRC=javascript:alert(&quot;XSS&quot;)>
EOD
,

<<<'EOD'
<IMG SRC=`javascript:alert("RSnake says, 'XSS'")`>
EOD
,

<<<'EOD'
<IMG """><SCRIPT>alert("XSS")</SCRIPT>">
EOD
,

<<<'EOD'
<IMG SRC=javascript:alert(String.fromCharCode(88,83,83))>
EOD
,

<<<'EOD'
<IMG SRC=&#106;&#97;&#118;&#97;&#115;&#99;&#114;&#105;&#112;&#116;&#58;&#97;&#108;&#101;&#114;&#116;&#40;&#39;&#88;&#83;&#83;&#39;&#41;>
EOD
,

<<<'EOD'
<IMG SRC=&#0000106&#0000097&#0000118&#0000097&#0000115&#0000099&#0000114&#0000105&#0000112&#0000116&#0000058&#0000097&#0000108&#0000101&#0000114&#0000116&#0000040&#0000039&#0000088&#0000083&#0000083&#0000039&#0000041>
EOD
,

<<<'EOD'
<IMG SRC=&#x6A&#x61&#x76&#x61&#x73&#x63&#x72&#x69&#x70&#x74&#x3A&#x61&#x6C&#x65&#x72&#x74&#x28&#x27&#x58&#x53&#x53&#x27&#x29>
EOD
,

<<<'EOD'
<IMG SRC="jav&#x09;ascript:alert('XSS');">
EOD
,

<<<'EOD'
<IMG SRC="jav&#x09;ascript:alert('XSS');">
EOD
,

<<<'EOD'
<IMG SRC="jav&#x0A;ascript:alert('XSS');">
EOD
,

<<<'EOD'
<IMG SRC="jav&#x0D;ascript:alert('XSS');">
EOD
,

<<<'EOD'
<IMG
SRC
=
"
j
a
v
a
s
c
r
i
p
t
:
a
l
e
r
t
(
'
X
S
S
')
"
>

EOD
,

<<<'EOD'
perl -e 'print "<IMG SRC=java\0script:alert(\"XSS\")>";' > out
EOD
,

<<<'EOD'
perl -e 'print "<SCR\0IPT>alert(\"XSS\")</SCR\0IPT>";' > out
EOD
,

<<<'EOD'
<IMG SRC=" &#14;  javascript:alert('XSS');">
EOD
,

<<<'EOD'
<SCRIPT/XSS SRC="http://ha.ckers.org/xss.js"></SCRIPT>
EOD
,

<<<'EOD'
<BODY onload!#$%&()*~+-_.,:;?@[/|\]^`=alert("XSS")>
EOD
,

<<<'EOD'
<SCRIPT/SRC="http://ha.ckers.org/xss.js"></SCRIPT>
EOD
,

<<<'EOD'
<<SCRIPT>alert("XSS");//<</SCRIPT>
EOD
,

<<<'EOD'
<SCRIPT SRC=http://ha.ckers.org/xss.js?<B>
EOD
,

<<<'EOD'
<SCRIPT SRC=//ha.ckers.org/.j>
EOD
,

<<<'EOD'
<IMG SRC="javascript:alert('XSS')"
EOD
,

<<<'EOD'
<SCRIPT>a=/XSS/
alert(a.source)</SCRIPT>
EOD
,

<<<'EOD'
\";alert('XSS');//
EOD
,

<<<'EOD'
</TITLE><SCRIPT>alert("XSS");</SCRIPT>
EOD
,

<<<'EOD'
<INPUT TYPE="IMAGE" SRC="javascript:alert('XSS');">
EOD
,

<<<'EOD'
<BODY BACKGROUND="javascript:alert('XSS')">
EOD
,

<<<'EOD'
<BODY ONLOAD=alert('XSS')>
EOD
,

<<<'EOD'
<IMG DYNSRC="javascript:alert('XSS')">
EOD
,

<<<'EOD'
<IMG LOWSRC="javascript:alert('XSS')">
EOD
,

<<<'EOD'
<BGSOUND SRC="javascript:alert('XSS');">
EOD
,

<<<'EOD'
<BR SIZE="&{alert('XSS')}">
EOD
        );

        foreach ( $tests as $test ) {
            $check = htmlspecialchars($test, ENT_QUOTES, 'UTF-8');
            $clean = $this->_form->escape($test);
            $this->assertEquals($clean, $check);
        }

    }

}
?>
