<?php declare(strict_types=1);
// @phpcs:ignoreFile PSR1.Files.SideEffects.FoundWithSymbols

use UnzerSDK\Resources\PaymentTypes\BasePaymentType;
use UnzerSDK\Resources\TransactionTypes\AbstractTransactionType;
use UnzerSDK\Resources\TransactionTypes\Charge;
use Plugin\s360_heidelpay_shop4\Payments\HeidelpayPaymentMethod;
use Plugin\s360_heidelpay_shop4\Payments\Interfaces\RedirectPaymentInterface;
use Plugin\s360_heidelpay_shop4\Payments\Traits\HasCustomer;
use Plugin\s360_heidelpay_shop4\Payments\Traits\HasMetadata;

require_once __DIR__ . '/../init.php';

/**
 * Heidelpay iDEAL Payment Method.
 *
 * iDEAL is a standardised payment method for making secure online payments directly between bank accounts in the Netherlands.
 *
 * To offer iDEAL as a payment method in an online store, a direct link is established with the systems of participating banks.
 * In other words, this connection to iDEAL enables each Merchant access to online banking of ABN AMRO, ASN Bank, Friesland Bank,
 * ING, Rabobank, RegioBank, SNS Bank, Triodos Bank or Van Lanschot Bankiers to make payments in this way. No other payment product offers this facility.
 *
 * Dutch customers pay online by using their login data of their bank account.
 *
 * @see https://docs.heidelpay.com/docs/ideal-payment
 */
class HeidelpayiDEAL extends HeidelpayPaymentMethod implements RedirectPaymentInterface
{
    use HasMetadata;
    use HasCustomer;

    /**
     * @inheritDoc
     * @return AbstractTransactionType|Charge
     */
    protected function performTransaction(BasePaymentType $payment, $order): AbstractTransactionType
    {
        // Create / Update existing customer resource if needed
        $customer = $this->createOrFetchHeidelpayCustomer($this->adapter, $this->plugin->getSession(), false);

        if ($customer->getId()) {
            $customer = $this->adapter->getApi()->updateCustomer($customer);
        }

        return $this->adapter->getApi()->charge(
            round($order->fGesamtsummeKundenwaehrung, 2),
            $order->Waehrung->cISO,
            $payment->getId(),
            $this->getReturnURL($order),
            $customer,
            $order->cBestellNr ?? null,
            $this->createMetadata()
        );
    }
}
