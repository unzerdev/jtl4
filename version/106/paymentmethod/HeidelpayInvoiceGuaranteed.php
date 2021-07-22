<?php declare(strict_types=1);
// @phpcs:ignoreFile PSR1.Files.SideEffects.FoundWithSymbols

use Plugin\s360_heidelpay_shop4\Payments\Interfaces\CancelableInterface;
use UnzerSDK\Resources\PaymentTypes\BasePaymentType;
use UnzerSDK\Resources\TransactionTypes\AbstractTransactionType;
use UnzerSDK\Resources\TransactionTypes\Charge;
use Plugin\s360_heidelpay_shop4\Payments\Interfaces\HandleStepAdditionalInterface;
use Plugin\s360_heidelpay_shop4\Payments\Traits\HasBasket;
use Plugin\s360_heidelpay_shop4\Payments\Traits\HasCustomer;
use Plugin\s360_heidelpay_shop4\Payments\Traits\HasMetadata;
use Plugin\s360_heidelpay_shop4\Payments\Traits\SupportsB2B;
use Plugin\s360_heidelpay_shop4\Utils\SessionHelper;
use UnzerSDK\Constants\CancelReasonCodes;
use UnzerSDK\Resources\Payment;
use UnzerSDK\Resources\TransactionTypes\Cancellation;

require_once __DIR__ . '/HeidelpayInvoice.php';

/**
 * With invoice payments an invoice is sent to the customer - the customer pays upon receipt of the invoice and
 * after the process is finished you (the merchant) receive your money.
 *
 * With a guaranteed invoice you will be guaranteed receiving money, even if the customer does not pay.
 * The guaranteed invoice works similarly to the invoice. The difference is that an insurance company will be involved into the process.
 * The insurance company guarantees payment because it carries out a risk check in advance.
 *
 * @see https://docs.heidelpay.com/docs/invoice-payment
 */
class HeidelpayInvoiceGuaranteed extends HeidelpayInvoice implements HandleStepAdditionalInterface, CancelableInterface
{
    use HasCustomer;
    use HasBasket;
    use HasMetadata;
    use SupportsB2B;

    /**
     * Cancel the Charge.
     *
     * Invoice factoring has an additional mandatory field (reason code) in case of a cancel.
     *
     * @param Payment $payment
     * @param Charge $transaction
     * @param Bestellung $order
     * @return Cancellation
     */
    public function cancelPaymentTransaction(
        Payment $payment,
        AbstractTransactionType $transaction,
        Bestellung $order
    ): Cancellation {
        return $transaction->cancel(null, CancelReasonCodes::REASON_CODE_CANCEL);
    }

    /**
     * Add Customer Resource to view.
     *
     * @param JTLSmarty $view
     * @return void
     */
    public function handleStepAdditional(JTLSmarty $view): void
    {
        $shopCustomer = $this->plugin->getSession()->getFrontendSession()->Customer();
        $customer = $this->createOrFetchHeidelpayCustomer(
            $this->adapter,
            $this->plugin->getSession(),
            $this->isB2BCustomer($shopCustomer)
        );
        $customer->setShippingAddress(
            $this->createHeidelpayAddress(
                $this->plugin->getSession()->getFrontendSession()->get('Lieferadresse')
            )
        );

        $data = $view->getTemplateVars('hpPayment') ?: [];
        $data['customer'] = $customer;
        $data['isB2B'] = $this->isB2BCustomer($shopCustomer);

        $view->assign('hpPayment', $data);
    }

    /**
     * Save customer resource id in the session.
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     * @return bool
     */
    public function validateAdditional(): bool
    {
        $postPaymentData = $_POST['paymentData'] ?? [];

        // Save Customer ID if it exists
        if (isset($postPaymentData['customerId'])) {
            $this->plugin->getSession()->set(SessionHelper::KEY_CUSTOMER_ID, $postPaymentData['customerId']);

            return true && parent::validateAdditional();
        }

        return false;
    }

    /**
     * For Invoice guaranteed and factoring you need to provide a customer resource within the charge call.
     * Only with this customer resource the insurance company can do the risk check.
     *
     * @inheritDoc
     * @return AbstractTransactionType|Charge
     */
    protected function performTransaction(BasePaymentType $payment, $order): AbstractTransactionType
    {
        // Create or fetch customer resource
        $shopCustomer = $this->plugin->getSession()->getFrontendSession()->Customer();
        $customer = $this->createOrFetchHeidelpayCustomer(
            $this->adapter,
            $this->plugin->getSession(),
            $this->isB2BCustomer($shopCustomer)
        );
        $customer->setShippingAddress($this->createHeidelpayAddress($order->Lieferadresse));
        $customer->setBillingAddress($this->createHeidelpayAddress($order->oRechnungsadresse));
        $this->debugLog('Customer Resource: ' . $customer->jsonSerialize(), static::class);

        // Update existing customer resource if needed
        if ($customer->getId()) {
            $customer = $this->adapter->getApi()->updateCustomer($customer);
            $this->debugLog('Updated Customer Resource: ' . $customer->jsonSerialize(), static::class);
        }

        // Create Basket
        $session = $this->plugin->getSession()->getFrontendSession();
        $basket = $this->createHeidelpayBasket($session->Basket(), $order->Waehrung, $session->Language(), $payment->getId());
        $this->debugLog('Basket Resource: ' . $basket->jsonSerialize(), static::class);

        return $this->adapter->getApi()->charge(
            round($order->fGesamtsummeKundenwaehrung, 2),
            $order->Waehrung->cISO,
            $payment->getId(),
            $this->getReturnURL($order),
            $customer,
            $order->cBestellNr ?? null,
            $this->createMetadata(),
            $basket
        );
    }
}
