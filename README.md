
php-tag-helper
==============

Simple helper to generate html5/markup with automatic escaping
and without string concatenations, printf or html/php mixmatch.

    <?php
    echo tag::a(array('href' => $url, 'title' => $title), tag::b($name))->br();
    ?>

