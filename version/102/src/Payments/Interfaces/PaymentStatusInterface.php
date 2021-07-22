<?php declare(strict_types=1);

namespace Plugin\s360_heidelpay_shop4\Payments\Interfaces;

/**
 * Interface PaymentStatusInterface
 * @package Plugin\s360_heidelpay_shop4\Payments\Interfaces
 */
interface PaymentStatusInterface
{
    public const PAYSTATUS_SUCCESS = 'success';
    public const PAYSTATUS_FAILED  = 'failed';
    public const PAYSTATUS_PENDING = 'pending';
    public const PAYSTATUS_PAID    = 'paid';
    public const PAYSTATUS_CANCEL  = 'cancel';
}
