<?php

require_once __DIR__ . '/../vendor/autoload.php';

if (empty($_ENV['SETTLE_MERCHANT_ID'])) {
    $dotenv = Dotenv\Dotenv::createImmutable(realpath(__DIR__ . '/../'));
    $dotenv->safeLoad();
}

define('SETTLE_MERCHANT_ID', $_ENV['SETTLE_MERCHANT_ID'] ?? '');
define('SETTLE_USER_ID', $_ENV['SETTLE_USER_ID'] ?? '');
define('SETTLE_PUBLIC_KEY', $_ENV['SETTLE_PUBLIC_KEY'] ?? '');
define('SETTLE_PRIVATE_KEY', $_ENV['SETTLE_PRIVATE_KEY'] ?? '');
define('SETTLE_IN_SANDBOX', ($_ENV['SETTLE_IN_SANDBOX'] ?? false) ? true : false);
