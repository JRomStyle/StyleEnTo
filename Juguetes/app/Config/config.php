<?php
return [
    'db' => [
        'host' => getenv('DB_HOST') ?: 'localhost',
        'name' => getenv('DB_NAME') ?: 'juguetes',
        'user' => getenv('DB_USER') ?: 'root',
        'pass' => getenv('DB_PASS') ?: '',
    ],
    'mail' => [
        'from' => getenv('MAIL_FROM') ?: 'no-reply@juguetes.local',
    ],
    'app' => [
        'name' => 'Juguetes',
    ],
];
