<?php
return [
    'title' => 'Airdoc',
    'mount' => [
        'doc' => '/',
        'mount2' => '/dir2',
    ],
    'password' => '123456',
    'cache_dir' => '/tmp/airdoc',
    'ignore_regexp' => [
        '/\.',
        '\.php$',
    ],
];