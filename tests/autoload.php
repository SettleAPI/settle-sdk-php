<?php

require_once __DIR__ . '/../vendor/autoload.php';

if (empty($_ENV['SETTLE_MERCHANT_ID'])) {
    $dotenv = Dotenv\Dotenv::createImmutable(realpath(__DIR__ . '/../'));
    $dotenv->safeLoad();
}

$settle_public_key = $_ENV['SETTLE_PUBLIC_KEY'] ?? '';
if ($settle_public_key && strpos($settle_public_key, 'PUBLIC') === false) {
    $settle_public_key = base64_decode($settle_public_key);
}

$settle_private_key = $_ENV['SETTLE_PRIVATE_KEY'] ?? '';
if ($settle_private_key && strpos($settle_private_key, 'PRIVATE') === false) {
    $settle_private_key = base64_decode($settle_private_key);
}

define('SETTLE_MERCHANT_ID', $_ENV['SETTLE_MERCHANT_ID'] ?? '');
define('SETTLE_USER_ID', $_ENV['SETTLE_USER_ID'] ?? '');
define('SETTLE_PUBLIC_KEY', $settle_public_key);
define('SETTLE_PRIVATE_KEY', $settle_private_key);
define('SETTLE_IN_SANDBOX', ($_ENV['SETTLE_IN_SANDBOX'] ?? false) ? true : false);
