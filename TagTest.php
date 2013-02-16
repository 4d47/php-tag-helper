<?php
require 'Tag.php';

class TagTest extends PHPUnit_Framework_TestCase
{
    public function testConstructWithOptionalStringArgument()
    {
        $this->assertEquals('', new Tag());
        $this->assertEquals('a', new Tag('a'));
        $this->assertEquals('<unescaped>', new Tag('<unescaped>'));
        $this->assertEquals('foo', new Tag(new Tag('foo')));
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

    public function testFunctionFrontendAcceptTagNameAsTheFirstArgument()
    {
        $this->assertEquals('<br>', tag('br'));
        $this->assertEquals('<p class="a">again</p>', tag('p', array('class' => 'a'), 'again'));
        $this->assertTrue(tag('a') instanceof Tag);
        $this->assertEquals('', tag());
    }

    public function testFunctionFrontendAcceptInliningAttributesWithTagName()
    {
        $this->assertEquals('<input checked>', tag('input checked'));
        $this->assertEquals('<hr class="bob">', tag('hr class="bob"'));
        $this->assertEquals('<p class="bob">1 &amp; 2</p>', tag('p class="bob"', '1 & 2'));
    }

    public function testStaticCallFrontend()
    {
        $this->assertEquals('<b>hello</b>', tag::b('hello'));
        $this->assertEquals('<form action="." method="POST">', tag::begin_form(array('action' => '.', 'method' => 'POST')));
    }

    public function testVoidElementsConfiguration()
    {
        $this->assertEquals('<p></p>', tag('p'));
        Tag::$voidElements[] = 'p';
        $this->assertEquals('<p>', tag('p'));
    }

    public function testSelfClosingMarkerOption()
    {
        Tag::$selfClosingMarker = ' /'; # going back to the xhtml days
        $this->assertEquals('<br />', tag('br'));
        $this->assertEquals('<img src="a.jpg" />', tag('img', array('src' => 'a.jpg')));
        $this->assertEquals('<p></p>', tag('p'));
    }

    public function setUp()
    {
        $this->tag = new Tag();
        Tag::$selfClosingMarker = self::$defaultSelfClosingMarker;
        Tag::$voidElements = self::$defaultVoidElements;
    }

    public static function setUpBeforeClass()
    {
        self::$defaultSelfClosingMarker = Tag::$selfClosingMarker;
        self::$defaultVoidElements = Tag::$voidElements;
    }

    private static $defaultSelfClosingMarker;
    private static $defaultVoidElements;
}

