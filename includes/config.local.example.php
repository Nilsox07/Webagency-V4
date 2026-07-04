<?php
declare(strict_types=1);

return [
    'db' => [
        'dsn' => 'mysql:host=localhost;dbname=sartu;charset=utf8mb4',
        'user' => 'sartu_user',
        'password' => 'HIER_PASSWORT_EINTRAGEN',
    ],
    'mail' => [
        'from' => 'noreply@deine-domain.de',
        'reply_to' => 'hallo@deine-domain.de',
        'base_url' => 'https://deine-domain.de',
    ],
    'app' => [
        'debug' => false,
    ],
];
