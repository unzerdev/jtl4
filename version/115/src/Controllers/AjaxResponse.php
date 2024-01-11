<?php declare(strict_types = 1);

namespace Plugin\s360_heidelpay_shop4\Controllers;

/**
 * Ajax Response Interface
 *
 * @package Plugin\s360_heidelpay_shop4\Controllers
 */
interface AjaxResponse
{
    // Response result states
    public const RESULT_ERROR = 'error';
    public const RESULT_FAIL = 'fail';
    public const RESULT_SUCCESS = 'success';
    public const RESULT_UNKNOWN = 'unknown';

    /**
     * Handle ajax request and send json response.
     *
     * @return void
     */
    public function handleAjax(): void;
}
