<?php
return [
    'db' => [
        'host' => 'localhost',
        'dbname' => 'contagestor_dian',
        'user' => 'root',
        'pass' => '',
        'charset' => 'utf8mb4'
    ],
    'app' => [
        'base_url' => '/contadores',
        'upload_dir' => __DIR__ . '/../public/uploads/documents',
        'upload_url' => '/contadores/uploads/documents'
    ],
    'security' => [
        'allowed_extensions' => ['pdf', 'doc', 'docx', 'xls', 'xlsx'],
        'max_upload_size' => 10485760
    ]
];
