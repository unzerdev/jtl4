<?php declare(strict_types = 1);

use Plugin\s360_heidelpay_shop4\Controllers\SyncController;
use Plugin\s360_heidelpay_shop4\Foundation\Plugin;
use Plugin\s360_heidelpay_shop4\Utils\Container;

require_once __DIR__ . '/../init.php';

/**
 * Handle cancel calls (STORNO).
 *
 * This is done per order.
 */
try {
    $controller = new SyncController(Container::getInstance()->make(Plugin::class));
    $controller->setOrder($args_arr['oBestellung']);
    $controller->setAction(SyncController::ACTION_CANCEL);
    $controller->handle();
} catch (Exception $exc) {
    Jtllog::writeLog('[Unzer] Exception in HOOK 210: ' . print_r($exc, true));
}
