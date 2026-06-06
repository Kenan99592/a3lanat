<?php

return [
    'mailgun' => [
        'domain'   => env('MAILGUN_DOMAIN'),
        'secret'   => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme'   => 'https',
    ],

    'meta' => [
        'app_id'       => env('META_APP_ID'),
        'app_secret'   => env('META_APP_SECRET'),
        'redirect_uri' => env('META_REDIRECT_URI'),
        'api_version'  => env('META_API_VERSION', 'v19.0'),
    ],
];
