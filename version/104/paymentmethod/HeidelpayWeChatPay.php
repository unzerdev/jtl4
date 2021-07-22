<?php declare(strict_types=1);
// @phpcs:ignoreFile PSR1.Files.SideEffects.FoundWithSymbols

use UnzerSDK\Resources\PaymentTypes\BasePaymentType;
use UnzerSDK\Resources\TransactionTypes\AbstractTransactionType;
use UnzerSDK\Resources\TransactionTypes\Charge;
use Plugin\s360_heidelpay_shop4\Payments\HeidelpayPaymentMethod;
use Plugin\s360_heidelpay_shop4\Payments\Interfaces\RedirectPaymentInterface;

require_once __DIR__ . '/../init.php';

/**
 * Heidelpay WeChatPay Payment Method.
 *
 * With over 600 million active monthly users,
 * WeChat Pay is one of Chinas biggest and fastest growing mobile payment solutions to date.
 *
 * It provides an easy, safe and secure way for individuals and businesses to make and receive payments on the internet.
 *
 * @see https://docs.heidelpay.com/docs/wechatpay
 */
class HeidelpayWeChatPay extends HeidelpayPaymentMethod implements RedirectPaymentInterface
{
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
            $order->cBestellNr ?? null
        );
    }
}
