<?php declare(strict_types = 1);

use Plugin\s360_heidelpay_shop4\Controllers\FrontendOutputController;
use Plugin\s360_heidelpay_shop4\Controllers\PaymentController;
use Plugin\s360_heidelpay_shop4\Foundation\Plugin;
use Plugin\s360_heidelpay_shop4\Utils\Container;

require_once __DIR__ . '/../init.php';

try {
    $paymentController = new PaymentController(Container::getInstance()->make(Plugin::class));
    $paymentController->handle();

    $controller = new FrontendOutputController(Container::getInstance()->make(Plugin::class));
    $controller->handle();
} catch (Exception $exception) {
    Jtllog::writeLog('[Unzer] Exception in HOOK 140: ' . print_r($exception, true));
}
