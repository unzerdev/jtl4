<?php

use Plugin\s360_heidelpay_shop4\Foundation\Plugin;
use Plugin\s360_heidelpay_shop4\Utils\Config;
use Plugin\s360_heidelpay_shop4\Utils\SessionHelper;

require_once __DIR__ . '/vendor/autoload.php';

// Register Services in Container
$app = new Plugin(new Config(), new SessionHelper());
$app->register();
