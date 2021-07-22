<?php declare(strict_types = 1);

use Plugin\s360_heidelpay_shop4\Controllers\Admin\AdminSettingsController;
use Plugin\s360_heidelpay_shop4\Foundation\Plugin;
use Plugin\s360_heidelpay_shop4\Utils\Container;

require_once __DIR__ . '/../init.php';

try {
    $controller = new AdminSettingsController(Container::getInstance()->make(Plugin::class));
    echo $controller->handle();
} catch (Exception $exception) {
    $cFehler = 'Exception: ' . $exception->getMessage();
    Jtllog::writeLog('[Unzer] Exception in settings.php: ' . print_r($exception, true));
}
