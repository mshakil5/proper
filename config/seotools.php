<?php

return [

    'meta' => [
        'defaults' => [
            'title'       => false,
            'titleBefore' => false,
            'description' => false,
            'separator'   => ' - ',
            'keywords'    => [],
            'canonical'   => true,
            'robots'      => 'index,follow',
        ],

        'webmaster_tags' => [
            'google'    => null,
            'bing'      => null,
            'alexa'     => null,
            'pinterest' => null,
            'yandex'    => null,
            'norton'    => null,
        ],

        'add_notranslate_class' => false,
    ],

    'opengraph' => [
        'defaults' => [
            'title'       => false,
            'description' => false,
            'url'         => false,
            'type'        => false,
            'site_name'   => false,
            'images'      => [],
        ],
    ],

    'twitter' => [
        'defaults' => [
            'card' => false,
            'site' => false,
        ],
    ],

    'json-ld' => [
        'defaults' => [
            'title'       => false,
            'description' => false,
            'url'         => false,
            'type'        => 'WebPage',
            'images'      => [],
        ],
    ],
];