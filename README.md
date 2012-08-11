
php-tag-helper
==============

Simple helper to generate markup without too much string concatenations or html/php mixing.

    <?= tag('a', array('href' => $data['url'], 'title' => $data['title']), $data['first_name'], tag('b', $data['nickname']), $data['last_name']); ?>

