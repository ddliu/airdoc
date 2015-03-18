<?php
return [
    'title' => 'Airdoc',
    'mount' => [
        'doc' => '/',
        'mount2' => '/dir2',
    ],
    'comstom_auth' => false,
    'users' => [
        'user1' => 'password1',
        'user2' => 'password2',
    ],
    'ignore_regexp' => [
        '/\.',
        '\.php$',
    ],
];