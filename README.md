
php-tag-helper
==============

Simple helper to generate markup without too much string concatenations or html/php mixing.

    <?= tag('a', array('href' => $url, 'title' => $title), $first_name, tag('b', $nickname), $last_name); ?>

