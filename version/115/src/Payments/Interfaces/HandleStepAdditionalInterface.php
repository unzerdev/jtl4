<?php
declare(strict_types = 1);

namespace Plugin\s360_heidelpay_shop4\Payments\Interfaces;

use JTLSmarty;

/**
 * Interface to handle "Additional" page in payment process.
 *
 * @package Plugin\s360_heidelpay_shop4\Payments\Interfaces
 */
interface HandleStepAdditionalInterface
{
    /**
     * Handle step additional, ie prepare view, load lang vars etc.
     *
     * @param JTLSmarty $view
     * @return void
     */
    public function handleStepAdditional(JTLSmarty $view): void;
}
