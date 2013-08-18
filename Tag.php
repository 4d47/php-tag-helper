<?php

/**
 * Tag string generator (Engineered for making soup)
 */
final class tag
{
    public static $selfClosingMarker = '';
    public static $voidElements = array('area', 'base', 'br', 'col', 'command', 'embed', 'hr', 'img', 'input', 'keygen', 'link', 'meta', 'param', 'source', 'track', 'wbr');
    public static $booleanAttributes = array('async', 'checked', 'compact', 'declare', 'defer', 'disabled', 'ismap', 'multiple', 'noresize', 'noshade', 'nowrap', 'open', 'readonly', 'required', 'reversed', 'scoped', 'selected');

    private $value;

    public function __construct($value = '')
    {
        $this->value = (string) $value;
    }

    public function __toString()
    {
        return $this->value;
    }

    public static function __callStatic($name, $args)
    {
        return call_user_func_array(array(new tag(), $name), $args);
    }

    public function __call($name, $args)
    {
        # read attributes possibly embeded in the $name
        $pos = strpos($name, ' ');
        if (is_int($pos)) {
            $attr = substr($name, $pos);
            $name = substr($name, 0, $pos);
        } else {
            $attr = '';
        }

        # convert attributes to properly escaped string
        $attrs = (!empty($args) && is_array($args[0])) ? array_shift($args) : array();
        foreach ($attrs as $k => $v) {
            if (in_array($k, tag::$booleanAttributes)) {
                if ($v) {
                   $attr .= tag::$selfClosingMarker ? " $k=\"$k\"" : " $k";
                }
            } else {
                $attr .= sprintf(' %s="%s"', $k, htmlspecialchars($v));
            }
        }

        # flatten content
        $args = $this->flatten($args);

        # escape tag content
        foreach ($args as &$c) {
            if (! $c instanceof tag) {
                $c = htmlspecialchars($c);
            }
        }

        # construct tag string from $name, $attr and $args
        if (0 === strpos($name, 'end_')) {
            $tag = '</' . substr($name, 4) .'>';
        } else if (0 === strpos($name, 'begin_')) {
            $tag = '<' . substr($name, 6) . $attr . '>';
        } else if (in_array($name, tag::$voidElements)) {
            $tag = "<$name$attr" . tag::$selfClosingMarker . ">";
        } else {
            $tag = "<$name$attr>" . implode(' ', $args) . "</$name>";
        }

        return new tag($this->value . $tag);
    }

    private static function flatten($array)
    {
        return array_reduce($array, array('tag', 'flat'), array());
    }

    private static function flat(&$result, $item)
    {
        return array_merge($result, is_array($item) ? array_values($item) : array($item));
    }
}

/**
 * Tag function front end.
 */
function tag()
{
    static $tag;
    if (is_null($tag)) {
        $tag = new tag();
    }
    $args = func_get_args();
    if (empty($args)) {
        return $tag;
    } else {
        $callback = array($tag, array_shift($args));
        return call_user_func_array($callback, $args);
    }
}

