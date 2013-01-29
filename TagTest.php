<?php
require_once 'Tag.php';

class TagTest extends PHPUnit_Framework_TestCase
{
    private static $defaultSelfClosingMarker;

    static function setUpBeforeClass()
    {
        self::$defaultSelfClosingMarker = Tag::$selfClosingMarker;
    }

    function setUp()
    {
        Tag::$selfClosingMarker = self::$defaultSelfClosingMarker;
        $this->tag = new Tag();
    }

    function testConstructWithOneUnescapedStringArgument()
    {
        $this->assertEquals('a', new Tag('a'));
        $this->assertEquals('<unescaped>', new Tag('<unescaped>'));
        $this->assertEquals('foo', new Tag(new Tag('foo')));
    }

    function testAcceptsAnyMethodsWithAnyNumberOfArguments()
    {
        $this->assertEquals('<foo />', $this->tag->foo());
        $this->assertEquals('<test />', $this->tag->test());
        $this->assertEquals('<test>foo</test>', $this->tag->test('foo'));
        $this->assertEquals('<test>foo bar</test>', $this->tag->test('foo', 'bar'));
        $this->assertEquals('<test>1 &gt; 2</test>', $this->tag->test('1 > 2'));
        $this->assertEquals('<test>1 &gt; 2</test>', $this->tag->test('1', '>', '2'));
        $this->assertEquals('<test><bar /></test>', $this->tag->test($this->tag->bar()));
    }

    function testFirstArgumentAsArrayMeansTagAttributes()
    {
        $this->assertEquals('<test class="foo" />', $this->tag->test(array('class' => 'foo')));
        $this->assertEquals('<test alt="1 &gt; 2" title="a" />', $this->tag->test(array('alt' => '1 > 2', 'title' => 'a')));
        $this->assertEquals('<test>foo</test>', $this->tag->test(array(), 'foo'));
    }

    function testMethodsStartingWithBeginUnderscoreMeansOpeningTag()
    {
        $this->assertEquals('<test>', $this->tag->begin_test('foo'));
        $this->assertEquals('<test class="foo">', $this->tag->begin_test( array('class' => 'foo')));
    }

    function testMethodsStartingWithEndUnderscoreMeansClosingTag()
    {
        $this->assertEquals('</test>', $this->tag->end_test('foo'));
        $this->assertEquals('</test>', $this->tag->end_test(array('class' => 'foo'), 'foo'));
    }

    function testReturnValueIsATagObjectNotString()
    {
        $this->assertEquals('<test><foo /><bar /><baz /></test>', $this->tag->test($this->tag->foo()->bar()->baz()));
    }

    function testFunctionFrontendAcceptTagNameAsFirstArgument()
    {
        $this->assertTrue(tag('a') instanceof Tag);
        $this->assertEquals('', tag());
        $this->assertEquals('<test />', tag('test'));
        $this->assertEquals('<test>again</test>', tag('test', 'again'));
    }

    function testFunctionFrontendEaseInliningAttributesWithTagName()
    {
        $this->assertEquals('<test baz />', tag('test baz'));
        $this->assertEquals('<test class="bob" />', tag('test class="bob"'));
        $this->assertEquals('<test class="bob">1 &amp; 2</test>', tag('test class="bob"', '1 & 2'));
    }

    function testSelfClosingMarkerOption()
    {
        Tag::$selfClosingMarker = '';
        $this->assertEquals('<img src="a.jpg">', tag('img', array('src' => 'a.jpg')));
    }
}

