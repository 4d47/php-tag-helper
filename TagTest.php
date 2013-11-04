<?php
require 'Tag.php';

class TagTest extends PHPUnit_Framework_TestCase
{
    public function testConstructWithOptionalStringArgument()
    {
        $this->assertEquals('', new tag());
        $this->assertEquals('a', new tag('a'));
        $this->assertEquals('<unescaped>', new tag('<unescaped>'));
        $this->assertEquals('foo', new tag(new tag('foo')));
    }

    public function testAcceptsAnyMethodsWithAnyNumberOfArguments()
    {
        $this->assertEquals('<br>', $this->tag->br());
        $this->assertEquals('<p></p>', $this->tag->p());
        $this->assertEquals('<p>foo</p>', $this->tag->p('foo'));
        $this->assertEquals('<p>foo bar</p>', $this->tag->p('foo', 'bar'));
        $this->assertEquals('<p>1 &gt; 2</p>', $this->tag->p('1 > 2'));
        $this->assertEquals('<p>1 &gt; 2</p>', $this->tag->p('1', '>', '2'));
        $this->assertEquals('<p><img></p>', $this->tag->p($this->tag->img()));
        $this->assertEquals('<foo>test</foo>', $this->tag->foo('test'));
    }

    public function testFirstArgumentAsArrayMeansAttributes()
    {
        $this->assertEquals('<hr class="foo">', $this->tag->hr(array('class' => 'foo')));
        $this->assertEquals('<img src="a.jpg" alt="1 &gt; 2">', $this->tag->img(array('src' => 'a.jpg', 'alt' => '1 > 2')));
        $this->assertEquals('<p>foo</p>', $this->tag->p(array(), 'foo'));
    }

    public function testBooleanAttributes()
    {
        $this->assertEquals('<input disabled>', $this->tag->input(array('disabled' => true)));
        $this->assertEquals('<input>', $this->tag->input(array('disabled' => false)));
    }

    public function testVoidElementsIgnoreThereInnerContent()
    {
        $this->assertEquals('<img>', $this->tag->img('should', 'ignore'));
    }

    public function testMethodsStartingWithBeginUnderscoreMeansOpeningTag()
    {
        $this->assertEquals('<form>', $this->tag->begin_form());
        $this->assertEquals('<form>', $this->tag->begin_form('should', 'ignore'));
        $this->assertEquals('<form method="POST">', $this->tag->begin_form( array('method' => 'POST')));
    }

    public function testMethodsStartingWithEndUnderscoreMeansClosingTag()
    {
        $this->assertEquals('</form>', $this->tag->end_form());
        $this->assertEquals('</form>', $this->tag->end_form('should', 'ignore'));
        $this->assertEquals('</form>', $this->tag->end_form(array('class' => 'foo'), 'foo'));
    }

    public function testReturnValueIsATagObjectAndNotAString()
    {
        $this->assertEquals('<div><br><br><br></div>', $this->tag->div($this->tag->br()->br()->br()));
    }

    public function testStaticCallFrontend()
    {
        $this->assertEquals('<b>hello</b>', tag::b('hello'));
        $this->assertEquals('<form action="." method="POST">', tag::begin_form(array('action' => '.', 'method' => 'POST')));
    }

    public function testVoidElementsOption()
    {
        $this->assertEquals('<p></p>', tag::p());
        tag::$voidElements[] = 'p';
        $this->assertEquals('<p>', tag::p());
    }

    public function testSelfClosingMarkerOption()
    {
        tag::$selfClosingMarker = ' /'; # going back to the xhtml days
        $this->assertEquals('<br />', tag::br());
        $this->assertEquals('<img src="a.jpg" />', tag::img(array('src' => 'a.jpg')));
        $this->assertEquals('<p></p>', tag::p());
    }

    public function testBooleanAttributesOption()
    {
        $this->assertEquals('<input disabled>', tag::input(array('disabled' => true)));
        $this->assertEquals('<input a="1">', tag::input(array('a' => true)));
        tag::$booleanAttributes[] = 'a';
        $this->assertEquals('<input a>', tag::input(array('a' => true)));
        tag::$selfClosingMarker = ' /';
        $this->assertEquals('<input disabled="disabled" />', tag::input(array('disabled' => true)));
    }

    public function testArrayContentIsFlatten()
    {
        $data = array('a', 'b', 'c');
        $this->assertEquals('<ul id="abc"><li>a</li> <li>b</li> <li>c</li></ul>',
            tag::ul(array('id' => 'abc'), array_map(array('tag', 'li'), $data)));
    }

    public function setUp()
    {
        $this->tag = new tag();
        tag::$selfClosingMarker = self::$defaultSelfClosingMarker;
        tag::$voidElements = self::$defaultVoidElements;
        tag::$booleanAttributes = self::$defaultBooleanAttributes;
    }

    public static function setUpBeforeClass()
    {
        self::$defaultSelfClosingMarker = tag::$selfClosingMarker;
        self::$defaultVoidElements = tag::$voidElements;
        self::$defaultBooleanAttributes = tag::$booleanAttributes;
    }

    private static $defaultSelfClosingMarker;
    private static $defaultVoidElements;
    private static $defaultBooleanAttributes;
}

