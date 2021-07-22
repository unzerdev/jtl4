<?php declare(strict_types=1);
// @phpcs:ignoreFile PSR1.Files.SideEffects.FoundWithSymbols

use UnzerSDK\Resources\PaymentTypes\BasePaymentType;
use UnzerSDK\Resources\TransactionTypes\AbstractTransactionType;
use UnzerSDK\Resources\TransactionTypes\Charge;
use Plugin\s360_heidelpay_shop4\Payments\HeidelpayPaymentMethod;
use Plugin\s360_heidelpay_shop4\Payments\Interfaces\RedirectPaymentInterface;
use Plugin\s360_heidelpay_shop4\Payments\Traits\HasMetadata;

require_once __DIR__ . '/../init.php';

/**
 * Heidelpay FlexiPayDirect Payment Method.
 *
 * FlexiPay Direct (Payment Initiation Service = PIS) is a service allowing Merchants
 * to initiate a payment transfer directly through the online banking account of the payer.
 * The service grants access to the online banking account of the payer and performs any task necessary to initiate the payment transfer.
 *
 * The payer himself only needs to provide credentials for logging into his online banking account
 * and authorize the payment transfer by his designated OTP-device - most likely via sms TAN.
 *
 * @see https://docs.heidelpay.com/docs/flexipay-direct
 */
class HeidelpayFlexiPayDirect extends HeidelpayPaymentMethod implements RedirectPaymentInterface
{
    use HasMetadata;

    /**
     * @inheritDoc
     * @return AbstractTransactionType|Charge
     */
    protected function performTransaction(BasePaymentType $payment, $order): AbstractTransactionType
    {
        return $this->adapter->getApi()->charge(
            round($order->fGesamtsummeKundenwaehrung, 2),
            $order->Waehrung->cISO,
            $payment->getId(),
            $this->getReturnURL($order),
            null,
            $order->cBestellNr ?? null,
            $this->createMetadata()
        );
    }
}
