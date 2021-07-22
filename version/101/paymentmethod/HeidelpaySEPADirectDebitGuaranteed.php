<?php declare(strict_types=1);
// @phpcs:ignoreFile PSR1.Files.SideEffects.FoundWithSymbols

use UnzerSDK\Resources\PaymentTypes\BasePaymentType;
use UnzerSDK\Resources\TransactionTypes\AbstractTransactionType;
use UnzerSDK\Resources\TransactionTypes\Charge;
use Plugin\s360_heidelpay_shop4\Payments\HeidelpayPaymentMethod;
use Plugin\s360_heidelpay_shop4\Payments\Interfaces\HandleStepAdditionalInterface;
use Plugin\s360_heidelpay_shop4\Payments\Interfaces\RedirectPaymentInterface;
use Plugin\s360_heidelpay_shop4\Payments\Traits\HasBasket;
use Plugin\s360_heidelpay_shop4\Payments\Traits\HasCustomer;
use Plugin\s360_heidelpay_shop4\Payments\Traits\SupportsB2B;
use Plugin\s360_heidelpay_shop4\Utils\Config;
use Plugin\s360_heidelpay_shop4\Utils\SessionHelper;

require_once __DIR__ . '/../init.php';

/**
 * HeidelpaySEPADirectDebitGuaranteed Payment Method.
 *
 * SEPA stands for "Single Euro Payments Area", and is a European Union initiative.
 * It is driven by the EU institutions, in particular the European Commission
 * and the European Central Bank.
 *
 * SEPA direct debit guaranteed works very similar to SEPA direct debit.
 * The difference is that there is also an insurance company involved in the process.
 * The insurance company guarantees the payment, but only if the risk checks are successful.
 *
 * @see https://docs.heidelpay.com/docs/sepa-direct-debit-payment
 */
class HeidelpaySEPADirectDebitGuaranteed extends HeidelpayPaymentMethod implements RedirectPaymentInterface, HandleStepAdditionalInterface
{
    use HasBasket;
    use HasCustomer;
    use SupportsB2B;

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
        $customer->setFirstname($shopCustomer->cVorname);
        $customer->setLastname($shopCustomer->cNachname);
        $customer->setShippingAddress(
            $this->createHeidelpayAddress(
                $this->plugin->getSession()->getFrontendSession()->get('Lieferadresse')
            )
        );

        $data = $view->getTemplateVars('hpPayment') ?: [];
        $data['customer'] = $customer;
        $data['mandate'] = str_replace(
            '%MERCHANT_NAME%',
            Shop::getSettingValue(CONF_GLOBAL, 'global_shopname'),
            $this->plugin->trans(Config::LANG_SEPA_MANDATE)
        );

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
     * Make sure, we only display it if shipping and invoice address are the same.
     *
     * @param array $args
     * @return boolean
     */
    public function isValidIntern($args = []): bool
    {
        $customer = $this->plugin->getSession()->getFrontendSession()->Customer();
        $shippingAddress = $this->plugin->getSession()->getFrontendSession()->get('Lieferadresse');

        return $this->shippingEqualsInvoiceAddress($shippingAddress, $customer) && parent::isValidIntern($args);
    }

    /**
     * Make sure, we only display it if shipping and invoice address are the same.
     *
     * @param object|Kunde $customer
     * @param Warenkorb $cart
     * @return boolean
     */
    public function isValid($customer, $cart): bool
    {
        $shippingAddress = $this->plugin->getSession()->getFrontendSession()->get('Lieferadresse');

        return $this->shippingEqualsInvoiceAddress($shippingAddress, $customer) && parent::isValid($customer, $cart);
    }

    /**
     * To execute risk checks we also have to provide a customer reference to the /payments/charges call.
     *
     * @inheritDoc
     * @return AbstractTransactionType|Charge
     */
    protected function performTransaction(BasePaymentType $payment, $order): AbstractTransactionType
    {
        // Create or fetch customer resource
        $customer = $this->createOrFetchHeidelpayCustomer($this->adapter, $this->plugin->getSession(), false);
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
            null,
            $basket
        );
    }
}