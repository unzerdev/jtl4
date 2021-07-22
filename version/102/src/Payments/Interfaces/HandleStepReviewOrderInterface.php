<?php
declare(strict_types = 1);

namespace Plugin\s360_heidelpay_shop4\Payments\Interfaces;

use JTLSmarty;

/**
 * Interface to handle "Review Order" page in payment process.
 *
 * @package Plugin\s360_heidelpay_shop4\Payments\Interfaces
 */
interface HandleStepReviewOrderInterface
{
    /**
     * Handle step review order, ie prepare view, load lang vars etc.
     *
     * @param JTLSmarty $view
     * @return string|null Template Key
     */
    public function handleStepReviewOrder(JTLSmarty $view): ?string;
}
