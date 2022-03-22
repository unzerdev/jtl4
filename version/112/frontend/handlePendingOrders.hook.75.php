<?php declare(strict_types = 1);

require_once __DIR__ . '/../init.php';

/**
 * Handle Pending Orders.
 *
 * Prevent the WaWi from collection an order that is currently PENDING.
 * Therefore, we mark the order as already collected (not great but JTL does not have a pending state).
 */
try {
    if (Shop::has('360HpOrderPending')) {
        $args_arr['oBestellung']->cAbgeholt = 'Y';
    }
} catch (Exception $exception) {
    Jtllog::writeLog('[Unzer] Exception in HOOK 75: ' . print_r($exception, true));
}
