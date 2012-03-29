<?php

namespace nx\test\unit\lib;

use nx\lib\Form;
use nx\test\mock\lib\ModelMock;

class FormTest extends \PHPUnit_Framework_TestCase {

    public function test_FormHelpersNoBindings_ReturnsHtml() {
        $attributes = array(
            'id'    => 'test_id',
            'class' => 'test_class',
            'name'  => 'test_name',
            'value' => 'test value'
        );

        $input = Form::checkbox($attributes);
        $check = "<input type='checkbox' id='test_id' class='test_class' name='test_name' value='test value' />";
        $this->assertEquals($check, $input);

        $input = Form::email($attributes);
        $check = "<input type='email' id='test_id' class='test_class' name='test_name' value='test value' />";
        $this->assertEquals($check, $input);

        $input = Form::hidden($attributes);
        $check = "<input type='hidden' id='test_id' class='test_class' name='test_name' value='test value' />";
        $this->assertEquals($check, $input);

        $input = Form::number($attributes);
        $check = "<input type='number' id='test_id' class='test_class' name='test_name' value='test value' />";
        $this->assertEquals($check, $input);

        $input = Form::search($attributes);
        $check = "<input type='search' id='test_id' class='test_class' name='test_name' value='test value' />";
        $this->assertEquals($check, $input);

        $input = Form::text($attributes);
        $check = "<input type='text' id='test_id' class='test_class' name='test_name' value='test value' />";
        $this->assertEquals($check, $input);


        $attributes = array(
            'id'    => 'test_id',
            'class' => 'test_class',
            'name'  => 'test_name',
        );
        $default = 'test value';

        $input = Form::textarea($attributes, $default);
        $check = "<textarea id='test_id' class='test_class' name='test_name'>test value</textarea>";
        $this->assertEquals($check, $input);

        $attributes = array(
            'id' => 'test_form',
            'action' => '/user/42',
            'method' => 'post'
        );

        $form = Form::form($attributes);
        $check = "<form id='test_form' action='/user/42' method='post' />";
        $this->assertEquals($check, $form);

        $attributes = array('name'  => 'test_name');
        $values = array('green', 'orange', 'red', 'blue');

        $input = Form::radios($attributes, $values);
        $check =
              "<input type='radio' name='test_name' id='test_name__green' value='green' />"
            . " <label for='test_name__green'>green</label>"
            . "<input type='radio' name='test_name' id='test_name__orange' value='orange' />"
            . " <label for='test_name__orange'>orange</label>"
            . "<input type='radio' name='test_name' id='test_name__red' value='red' />"
            . " <label for='test_name__red'>red</label>"
            . "<input type='radio' name='test_name' id='test_name__blue' value='blue' />"
            . " <label for='test_name__blue'>blue</label>";
        $this->assertEquals($check, $input);

        $attributes = array('name'  => 'test_name');
        $values = array(
            '17' => 'green',
            '18' => 'orange',
            '19' => 'red',
            '20' => 'blue'
        );

        $input = Form::radios($attributes, $values);
        $check =
              "<input type='radio' name='test_name' id='test_name__17' value='17' />"
            . " <label for='test_name__17'>green</label>"
            . "<input type='radio' name='test_name' id='test_name__18' value='18' />"
            . " <label for='test_name__18'>orange</label>"
            . "<input type='radio' name='test_name' id='test_name__19' value='19' />"
            . " <label for='test_name__19'>red</label>"
            . "<input type='radio' name='test_name' id='test_name__20' value='20' />"
            . " <label for='test_name__20'>blue</label>";
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

        $input = Form::select($attributes, $values);
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

        $input = Form::select($attributes, $values);
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

        $input = Form::email($attributes);
        $check = "<input type='email' id='test_id' class='test_class' autofocus='autofocus' name='test_name' value='test value' />";
        $this->assertEquals($check, $input);

    }

    public function test_FormHelpersWithBindings_ReturnsHtml() {
        $binding = new ModelMock();

        // Set the binding
        $attributes = array('id' => 'test_form');
        $form = Form::form($attributes, $binding);
        $check = "<form id='test_form' action='/modelmock' method='post' />";
        $this->assertEquals($check, $form);

        // Test with no value, but with name
        // set as the property of our object
        $attributes = array(
            'id'    => 'test_id',
            'class' => 'test_class',
            'name'  => 'test_name',
        );
        $input = Form::checkbox($attributes);
        $check = "<input type='checkbox' id='test_id' class='test_class' name='test_name' value='test value' />";
        $this->assertEquals($check, $input);

        $input = Form::email($attributes);
        $check = "<input type='email' id='test_id' class='test_class' name='test_name' value='test value' />";
        $this->assertEquals($check, $input);

        $input = Form::hidden($attributes);
        $check = "<input type='hidden' id='test_id' class='test_class' name='test_name' value='test value' />";
        $this->assertEquals($check, $input);

        $input = Form::number($attributes);
        $check = "<input type='number' id='test_id' class='test_class' name='test_name' value='test value' />";
        $this->assertEquals($check, $input);

        $input = Form::search($attributes);
        $check = "<input type='search' id='test_id' class='test_class' name='test_name' value='test value' />";
        $this->assertEquals($check, $input);

        $input = Form::text($attributes);
        $check = "<input type='text' id='test_id' class='test_class' name='test_name' value='test value' />";
        $this->assertEquals($check, $input);

        $checked = 'test value';
        $input = Form::checkbox($attributes, $checked);
        $check = "<input type='checkbox' id='test_id' class='test_class' name='test_name' value='test value' checked='checked' />";
        $this->assertEquals($check, $input);

        $input = Form::textarea($attributes);
        $check = "<textarea id='test_id' class='test_class' name='test_name'>test value</textarea>";
        $this->assertEquals($check, $input);

        // Test with values
        $attributes = array(
            'id'    => 'test_id',
            'class' => 'test_class',
            'name'  => 'test_name_2',
            'value' => 'test value 2'
        );
        $input = Form::email($attributes);
        $check = "<input type='email' id='test_id' class='test_class' name='test_name_2' value='test value 2' />";
        $this->assertEquals($check, $input);

        $attributes = array(
            'id'    => 'test_id',
            'class' => 'test_class',
            'name'  => 'test_name_3',
            'value' => 'test value 3'
        );
        $input = Form::hidden($attributes);
        $check = "<input type='hidden' id='test_id' class='test_class' name='test_name_3' value='test value 3' />";
        $this->assertEquals($check, $input);

        $attributes = array(
            'id'    => 'test_id',
            'class' => 'test_class',
            'name'  => 'test_name_4',
            'value' => 'test value 4'
        );
        $input = Form::number($attributes);
        $check = "<input type='number' id='test_id' class='test_class' name='test_name_4' value='test value 4' />";
        $this->assertEquals($check, $input);

        $attributes = array(
            'id'    => 'test_id',
            'class' => 'test_class',
            'name'  => 'test_name_5',
            'value' => 'test value 5'
        );
        $input = Form::search($attributes);
        $check = "<input type='search' id='test_id' class='test_class' name='test_name_5' value='test value 5' />";
        $this->assertEquals($check, $input);

        $attributes = array(
            'id'    => 'test_id',
            'class' => 'test_class',
            'name'  => 'test_name_6',
            'value' => 'test value 6'
        );
        $input = Form::text($attributes);
        $check = "<input type='text' id='test_id' class='test_class' name='test_name_6' value='test value 6' />";
        $this->assertEquals($check, $input);

        $attributes = array(
            'id'    => 'test_id',
            'class' => 'test_class',
            'name'  => 'test_name_7'
        );
        $default = 'test value 7';
        $input = Form::textarea($attributes, $default);
        $check = "<textarea id='test_id' class='test_class' name='test_name_7'>test value 7</textarea>";
        $this->assertEquals($check, $input);
    }

    public function test_RadiosWithBindings_ReturnsHtml() {
        $binding = new ModelMock();

        // Set the binding
        $attributes = array('id' => 'test_form');
        $form = Form::form($attributes, $binding);
        $check = "<form id='test_form' action='/modelmock' method='post' />";
        $this->assertEquals($check, $form);

        // Name set as the property of our object
        $attributes = array('name'  => 'test_name');
        $values = array(
            'green'      => 'green',
            'orange'     => 'orange',
            'test value' => 'yellow',
            'red'        => 'red',
            'blue'       => 'blue'
        );

        $input = Form::radios($attributes, $values);
        $check =
            "<input type='radio' name='test_name' id='test_name__green' value='green' />"
            . " <label for='test_name__green'>green</label>"
            . "<input type='radio' name='test_name' id='test_name__orange' value='orange' />"
            . " <label for='test_name__orange'>orange</label>"
            . "<input type='radio' name='test_name' id='test_name__test_value' value='test value' checked='checked' />"
            . " <label for='test_name__test_value'>yellow</label>"
            . "<input type='radio' name='test_name' id='test_name__red' value='red' />"
            . " <label for='test_name__red'>red</label>"
            . "<input type='radio' name='test_name' id='test_name__blue' value='blue' />"
            . " <label for='test_name__blue'>blue</label>";
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

        $input = Form::radios($attributes, $values);
        $check =
            "<input type='radio' name='test' id='test__17' value='17' />"
            . " <label for='test__17'>green</label>"
            . "<input type='radio' name='test' id='test__18' value='18' />"
            . " <label for='test__18'>orange</label>"
            . "<input type='radio' name='test' id='test__test_value' value='test value' />"
            . " <label for='test__test_value'>yellow</label>"
            . "<input type='radio' name='test' id='test__19' value='19' />"
            . " <label for='test__19'>red</label>"
            . "<input type='radio' name='test' id='test__20' value='20' />"
            . " <label for='test__20'>blue</label>";
        $this->assertEquals($check, $input);

        // Name different from the property of our object
        // and values as a non-associative array
        $attributes = array('name'  => 'test');
        $values = array('green', 'orange', 'yellow', 'red', 'blue');

        $input = Form::radios($attributes, $values);
        $check =
            "<input type='radio' name='test' id='test__green' value='green' />"
            . " <label for='test__green'>green</label>"
            . "<input type='radio' name='test' id='test__orange' value='orange' />"
            . " <label for='test__orange'>orange</label>"
            . "<input type='radio' name='test' id='test__yellow' value='yellow' />"
            . " <label for='test__yellow'>yellow</label>"
            . "<input type='radio' name='test' id='test__red' value='red' />"
            . " <label for='test__red'>red</label>"
            . "<input type='radio' name='test' id='test__blue' value='blue' />"
            . " <label for='test__blue'>blue</label>";
        $this->assertEquals($check, $input);
    }

    public function test_SelectWithBindings_ReturnsHtml() {
        $binding = new ModelMock();

        // Set the binding
        $attributes = array('id' => 'test_form');
        $form = Form::form($attributes, $binding);
        $check = "<form id='test_form' action='/modelmock' method='post' />";
        $this->assertEquals($check, $form);

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

        $input = Form::select($attributes, $values);
        $check = "<select name='test_name'>"
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

        $input = Form::select($attributes, $values);
        $check = "<select name='test'>"
            . "<option value='17'>green</option><option value='18'>orange</option>"
            . "<option value='test value'>yellow</option>"
            . "<option value='19'>red</option><option value='20'>blue</option>"
            . "</select>";
        $this->assertEquals($check, $input);

        // Name different from the property of our object
        // and values as a non-associative array
        $attributes = array('name'  => 'test');
        $values = array('green', 'orange', 'yellow', 'red', 'blue');

        $input = Form::select($attributes, $values);
        $check = "<select name='test'>"
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
        $form = Form::form($attributes, $binding);
        $check = "<form id='test_form' action='/modelmock/27' method='put' />";
        $this->assertEquals($check, $form);
    }

    public function test_EscapeArray_ReturnsEscapedArray() {
        $unescaped = array('yu"ck', 'gro&ss', "te'st");
        $escaped = Form::escape($unescaped);
        $check = array('yu&quot;ck', 'gro&amp;ss', 'te&#039;st');
        $this->assertEquals($check, $escaped);
    }

}
?>
