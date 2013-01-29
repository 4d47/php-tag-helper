<?php

/**
 * Tag string generator (Engineered for making soup)
 */
class Tag
{
    public static $selfClosingMarker = ' /';
    protected $value;

    public function __construct($value = '')
    {
        $this->value = (string) $value;
    }

    public function __toString()
    {
        return $this->value;
    }

    public function __call($name, $args)
    {
        $selfClosingMarker = self::$selfClosingMarker;
        $class = get_class($this);

        $w = strpos($name, ' ');
        if (is_int($w)) {
            # name contains literal attributes
            $attrs = substr($name, $w);
            $name = substr($name, 0, $w);
        } else {
            $attrs = '';
        }

        if (strpos($name, 'end_') === 0) {
            return new $class($this->value . '</' . substr($name, 4) .'>');
        }

        $a = (!empty($args) and is_array($args[0])) ? array_shift($args) : array();

        foreach ($a as $k => $v) {
            $attrs .= sprintf(' %s="%s"', $k, htmlspecialchars($v));
        }
        foreach ($args as &$c) {
            if (! $c instanceof Tag) {
                $c = htmlspecialchars($c);
            }
        }

        if (strpos($name, 'begin_') === 0) {
            return new $class($this->value . '<' . substr($name, 6) . $attrs . '>');
        } else if (empty($args)) {
            return new $class($this->value . "<$name$attrs$selfClosingMarker>");
        } else {
            return new $class($this->value . "<$name$attrs>" . implode(' ', $args) . "</$name>");
        }
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
        $fn = array($tag, array_shift($args));
        return call_user_func_array($fn, $args);
    }
}

