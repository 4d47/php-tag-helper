<?php

/**
 * Tag string generator (Engineered for making soup)
 */
final class tag
{
    public static $selfClosingMarker = '';
    public static $voidElements = array('area', 'base', 'br', 'col', 'command', 'embed', 'hr', 'img', 'input', 'keygen', 'link', 'meta', 'param', 'source', 'track', 'wbr');
    public static $booleanAttributes = array('async', 'checked', 'compact', 'declare', 'defer', 'disabled', 'ismap', 'multiple', 'noresize', 'noshade', 'nowrap', 'open', 'readonly', 'required', 'reversed', 'scoped', 'selected');
    public static $encoding = 'UTF-8';

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
        # convert attributes to properly escaped string
        $attr = '';
        $attrs = (!empty($args) && is_array($args[0])) ? array_shift($args) : array();
        foreach ($attrs as $k => $v) {
            if (in_array($k, tag::$booleanAttributes)) {
                if ($v) {
                   $attr .= tag::$selfClosingMarker ? " $k=\"$k\"" : " $k";
                }
            } else {
                $attr .= sprintf(' %s="%s"', $k, tag::escape($v));
            }
        }

        # flatten content
        $args = tag::flatten($args);

        # escape tag content
        foreach ($args as &$c) {
            if (! $c instanceof tag) {
                $c = tag::escape($c);
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

    private static function escape($string)
    {
        return htmlspecialchars($string, ENT_COMPAT, tag::$encoding);
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
