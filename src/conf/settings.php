<?php 
use Monolog\Logger;

return [
    'settings' => [
        'displayErrorDetails' => true,

        'logger' => [
            'name' => 'slim-app',
            'level' => Logger::DEBUG,
            'path' => __DIR__ . '/../logs/app.log',
        ],
    ],
    'messe' => [
        'CONSUMER_KEY' => 'xxxxxxxxxxxxxxxxxxx',
        'CONSUMER_SECRET' => 'xxxxxxxxxxxxxxxxxxxx',
        'CALLBACK_URL' => 'http://localhost:8000/messe',
        'AUTH_URL' => 'https://accounts.google.com/o/oauth2/auth',
        'TOKEN_URL' => 'https://accounts.google.com/o/oauth2/token',
        'EXCHANGE_TOKEN_URL' => 'https://www.googleapis.com/oauth2/v4/token',
        'INFO_URL' => 'https://www.googleapis.com/oauth2/v1/userinfo',
        'DB_DATABASE' => 'messe_db',
        'DB_USERNAME' => 'root',
        'DB_PASSWORD' => 'root'
    ]
];
