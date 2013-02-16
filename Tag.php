<?php

/**
 * Tag string generator (Engineered for making soup)
 */
class Tag
{
    public static $selfClosingMarker = '';
    public static $voidElements = array('area', 'base', 'br', 'col', 'command', 'embed', 'hr', 'img', 'input', 'keygen', 'link', 'meta', 'param', 'source', 'track', 'wbr');
    public static $booleanAttributes = array('async', 'checked', 'compact', 'declare', 'defer', 'disabled', 'ismap', 'multiple', 'noresize', 'noshade', 'nowrap', 'open', 'readonly', 'required', 'reversed', 'scoped', 'selected');

    protected $value;

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
        $class = get_called_class();
        $self = new $class();
        return call_user_func_array(array($self, $name), $args);
    }

    public function __call($name, $args)
    {
        # lots of complicated code ...
        $class = get_class($this);

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
            if (in_array($k, $class::$booleanAttributes)) {
                if ($v) {
                   $attr .= $class::$selfClosingMarker ? " $k=\"$k\"" : " $k";
                }
            } else {
                $attr .= sprintf(' %s="%s"', $k, htmlspecialchars($v));
            }
        }

        # escape tag content
        foreach ($args as &$c) {
            if (! $c instanceof Tag) {
                $c = htmlspecialchars($c);
            }
        }

        # construct tag string from $name, $attr and $args
        if (0 === strpos($name, 'end_')) {
            $tag = '</' . substr($name, 4) .'>';
        } else if (0 === strpos($name, 'begin_')) {
            $tag = '<' . substr($name, 6) . $attr . '>';
        } else if (in_array($name, $class::$voidElements)) {
            $tag = "<$name$attr{$class::$selfClosingMarker}>";
        } else {
            $tag = "<$name$attr>" . implode(' ', $args) . "</$name>";
        }

        return new $class($this->value . $tag);
    }
}

/**
 * Tag function front end.
 */
function tag()
{
    static $tag;
    if (is_null($tag)) {
        $tag = new Tag();
    }
    $args = func_get_args();
    if (empty($args)) {
        return $tag;
    } else {
        $callback = array($tag, array_shift($args));
        return call_user_func_array($callback, $args);
    }
}

