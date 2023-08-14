<?php declare(strict_types = 1);

use Plugin\s360_heidelpay_shop4\Controllers\Admin\AdminOrdersController;
use Plugin\s360_heidelpay_shop4\Foundation\Plugin;
use Plugin\s360_heidelpay_shop4\Utils\Container;

require_once __DIR__ . '/../init.php';

try {
    // Handle Ajax Requests
    if (isAjaxRequest() && $_GET['controller'] == 'OrderManagement') {
        try {
            $controller = new AdminOrdersController(Container::getInstance()->make(Plugin::class));
            $controller->handleAjax();
        } catch (Exception $exc) {
            Jtllog::writeLog('[Unzer] Exception in orders.php: ' . print_r($exc, true));
            echo json_encode([
                'status'   => 'error',
                'messages' => [$exc->getMessage()]
            ]);
            exit();
        }
    }

    $controller = new AdminOrdersController(Container::getInstance()->make(Plugin::class));
    echo $controller->handle();
} catch (Exception $exception) {
    $cFehler = 'Exception: ' . $exception->getMessage();
    Jtllog::writeLog('[Unzer] Exception in orders.php: ' . print_r($exception, true));
}
