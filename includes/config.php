<?php
declare(strict_types=1);

$config = [
    'db' => [
        'dsn' => getenv('SARTU_DB_DSN') ?: 'mysql:host=localhost;dbname=sartu;charset=utf8mb4',
        'user' => getenv('SARTU_DB_USER') ?: 'sartu_user',
        'password' => getenv('SARTU_DB_PASSWORD') ?: '',
    ],
    'mail' => [
        'from' => getenv('SARTU_MAIL_FROM') ?: 'noreply@sartu.de',
        'reply_to' => getenv('SARTU_MAIL_REPLY_TO') ?: 'hallo@sartu.de',
        'base_url' => rtrim((string) (getenv('SARTU_BASE_URL') ?: ''), '/'),
    ],
    'app' => [
        'debug' => getenv('SARTU_DEBUG') === '1',
        'session_name' => 'sartu_session',
    ],
];

$localConfig = __DIR__ . '/config.local.php';
if (is_file($localConfig)) {
    $local = require $localConfig;
    if (is_array($local)) {
        $config = array_replace_recursive($config, $local);
    }
}

return $config;
