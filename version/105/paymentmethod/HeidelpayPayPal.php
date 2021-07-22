<?php declare(strict_types=1);
// @phpcs:ignoreFile PSR1.Files.SideEffects.FoundWithSymbols

use UnzerSDK\Resources\PaymentTypes\BasePaymentType;
use UnzerSDK\Resources\TransactionTypes\AbstractTransactionType;
use UnzerSDK\Resources\TransactionTypes\Charge;
use Plugin\s360_heidelpay_shop4\Payments\HeidelpayPaymentMethod;
use Plugin\s360_heidelpay_shop4\Payments\Interfaces\RedirectPaymentInterface;
use Plugin\s360_heidelpay_shop4\Payments\Traits\HasCustomer;

require_once __DIR__ . '/../init.php';

/**
 * Heidelpay Paypal Payment Method.
 *
 * PayPal Holdings, Inc. is an American company operating a worldwide online payments system
 * that supports online money transfers and serves as an electronic alternative to
 * traditional paper methods like cheques and money orders.
 *
 * The customer has to sign up for a PayPal account.
 * Afterwards there is no need to enter the payment details again during the payment process.
 *
 * The Plugin does not support Paypal Express!
 *
 * @see https://docs.heidelpay.com/docs/paypal-payment
 */
class HeidelpayPayPal extends HeidelpayPaymentMethod implements RedirectPaymentInterface
{
    use HasCustomer;

    /**
     * Although Paypal support both auth as well as charge calls, we only support Direct Charge.
     *
     * We assign a customer with a shipping address to the charge to allow for "PayPal buyer protection"
     *
     * @inheritDoc
     * @return AbstractTransactionType|Charge
     */
    protected function performTransaction(BasePaymentType $payment, $order): AbstractTransactionType
    {
        // Create a customer with shipping address for Paypal's Buyer Protection
        $customer = $this->createOrFetchHeidelpayCustomer($this->adapter, $this->plugin->getSession(), false);
        $customer->setShippingAddress($this->createHeidelpayAddress($order->Lieferadresse));
        $customer->setBillingAddress($this->createHeidelpayAddress($order->oRechnungsadresse));

        // Update existing customer resource if needed
        if ($customer->getId()) {
            $customer = $this->adapter->getApi()->updateCustomer($customer);
        }

        return $this->adapter->getApi()->charge(
            round($order->fGesamtsummeKundenwaehrung, 2),
            $order->Waehrung->cISO,
            $payment->getId(),
            $this->getReturnURL($order),
            $customer,
            $order->cBestellNr ?? null
        );
    }
}
