php-tag-helper
==============

The tag string generator (Engineered for making XML or HTML5 soup).
With automatic escaping and without string concatenations, `printf`
or HTML/PHP switching.

    <?=
    tag::a(['href' => $url, 'title' => $title], tag::b($name))->br();
    ?>

There is no documentation but install with `composer require 4d47/tag ~2.0` and refer to [the tests](https://github.com/4d47/php-tag-helper/blob/master/TagTest.php) for usage.
