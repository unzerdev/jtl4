<?php

use Plugin\s360_heidelpay_shop4\Controllers\SyncWorkflowController;
use Plugin\s360_heidelpay_shop4\Foundation\Plugin;
use Plugin\s360_heidelpay_shop4\Utils\Container;

require_once __DIR__ . '/../init.php';

try {
    $controller = new SyncWorkflowController(Container::getInstance()->make(Plugin::class));
    $controller->handle();
} catch (Exception $exception) {
    Jtllog::writeLog('[Unzer] Exception in FRONTEND_LINK sync-workflow.php: ' . print_r($exception, true));
    http_response_code(403);
}

exit;
