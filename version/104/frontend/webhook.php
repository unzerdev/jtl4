<?php declare(strict_types = 1);

use Plugin\s360_heidelpay_shop4\Controllers\WebhookController;
use Plugin\s360_heidelpay_shop4\Foundation\Plugin;
use Plugin\s360_heidelpay_shop4\Utils\Container;

require_once __DIR__ . '/../init.php';

try {
    $controller = new WebhookController(Container::getInstance()->make(Plugin::class));
    $controller->handle();
} catch (Exception $exception) {
    Jtllog::writeLog('[Unzer] Exception in FRONTEND_LINK webhook.php: ' . print_r($exception, true));
    http_response_code(403);
}

exit;
