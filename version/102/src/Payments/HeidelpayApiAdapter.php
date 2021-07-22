<?php declare(strict_types = 1);

namespace Plugin\s360_heidelpay_shop4\Payments;

use Exception;
use UnzerSDK\Unzer;
use UnzerSDK\Resources\Payment;
use UnzerSDK\Resources\PaymentTypes\BasePaymentType;
use UnzerSDK\Resources\PaymentTypes\InstallmentSecured;
use UnzerSDK\Resources\PaymentTypes\InvoiceSecured;
use UnzerSDK\Resources\TransactionTypes\AbstractTransactionType;
use UnzerSDK\Resources\TransactionTypes\Authorization;
use Plugin\s360_heidelpay_shop4\Foundation\Plugin;
use Plugin\s360_heidelpay_shop4\Utils\Config;
use Plugin\s360_heidelpay_shop4\Utils\JtlLinkHelper;
use Plugin\s360_heidelpay_shop4\Utils\JtlLoggerTrait;
use Plugin\s360_heidelpay_shop4\Utils\SessionHelper;
use Warenkorb;

/**
 * Heidelpay API Adapter for JTL Shop.
 *
 * Handles API Class initiation and provides some helpers regarding the API usage.
 *
 * @package Plugin\s360_heidelpay_shop4\Payments
 */
class HeidelpayApiAdapter
{
    use JtlLoggerTrait;

    // Payment types that support shipment calls
    public const SUPPORTS_SHIPMENT = [
        InvoiceSecured::class,
        InstallmentSecured::class // Note: shipment docu says no, hdd says yes
    ];

    /**
     * @var Unzer
     */
    private $api;

    /**
     * @var Plugin
     */
    private $plugin;

    /**
     * @var JtlLinkHelper
     */
    private $linkHelper;

    /**
     * @param Plugin $plugin
     * @param JtlLinkHelper $linkHelper
     */
    public function __construct(Plugin $plugin, JtlLinkHelper $linkHelper)
    {
        $this->plugin = $plugin;
        $this->linkHelper = $linkHelper;
        $this->createApiClient();
    }

    /**
     * Create a new API Client Instance
     *
     * @return Unzer
     */
    public function createApiClient(): Unzer
    {
        $this->api = new Unzer(
            $this->plugin->getConfig()->get(Config::PRIVATE_KEY),
            $this->mapToLocale($this->plugin->getSession()->getFrontendSession()->Language()->cISOSprache ?? 'eng')
        );

        return $this->getApi();
    }

    /**
     * Get Api instance.
     *
     * @return Unzer
     */
    public function getApi(): Unzer
    {
        return $this->api;
    }

    /**
     * Fetch customer id and save its id to the session.
     *
     * If we cannot find a mapped id we set the customer id to -1 to avoid quering the api multiple times.
     *
     * @return string|int|null
     */
    public function fetchCustomerId()
    {
        if (!$this->plugin->getSession()->has(SessionHelper::KEY_CUSTOMER_ID)) {
            // Try to fetch the heidelpay customer by its shop id (kKunde).
            try {
                $customer = $this->getApi()->fetchCustomerByExtCustomerId(
                    $this->plugin->getSession()->getFrontendSession()->Customer()->kKunde
                );

                $this->plugin->getSession()->set(SessionHelper::KEY_CUSTOMER_ID, $customer->getId());
            } catch (Exception $exc) {
                $this->plugin->getSession()->set(SessionHelper::KEY_CUSTOMER_ID, -1);
                $this->debugLog('Tried to fetch customer by kKunde: ' . utf8_decode($exc->getMessage()));
            }
        }

        return $this->plugin->getSession()->get(SessionHelper::KEY_CUSTOMER_ID);
    }

    /**
     * Fetch a payment from the api.
     *
     * @throws UnzerApiException if there is an error returned on API-request.
     * @throws RuntimeException if there is an error while using the SDK
     * @param string|null $paymentId
     * @return Payment
     */
    public function fetchPayment(?string $paymentId = null): Payment
    {
        if (is_null($paymentId)) {
            $paymentId = $this->plugin->getSession()->get(SessionHelper::KEY_PAYMENT_ID);
        }

        return $this->api->fetchPayment($paymentId);
    }

    /**
     * Get transaction of a payment.
     *
     * @param Payment $payment
     * @return AbstractTransactionType
     */
    public function getPaymentTransaction(Payment $payment): AbstractTransactionType
    {
        $transaction = $payment->getAuthorization();
        if (!$transaction instanceof Authorization) {
            $transaction = $payment->getChargeByIndex(0);
        }

        return $transaction;
    }

    /**
     * Fetch a payment type from the api.
     *
     * @throws UnzerApiException if there is an error returned on API-request.
     * @throws RuntimeException if there is an error while using the SDK
     * @param string|null $paymentTypeId
     * @return BasePaymentType
     */
    public function fetchPaymentType(?string $paymentTypeId = null): BasePaymentType
    {
        if (is_null($paymentTypeId)) {
            $paymentTypeId = $this->plugin->getSession()->get(
                $this->plugin->getSession()->buildSessionKey(
                    [SessionHelper::KEY_CHECKOUT_SESSION, SessionHelper::KEY_RESOURCE_ID]
                )
            );
        }

        return $this->api->fetchPaymentType($paymentTypeId);
    }

    /**
     * Checks if a payment method supports the shipment call.
     *
     * @param BasePaymentType $paymentType
     * @return bool
     */
    public function supportsShipment(BasePaymentType $paymentType): bool
    {
        return in_array(get_class($paymentType), self::SUPPORTS_SHIPMENT);
    }

    /**
     * Redirect transaction to external payment provider.
     *
     * @SuppressWarnings(PHPMD.ExitExpression)
     * @param AbstractTransactionType $transaction
     * @param array $postData
     * @return void
     */
    public function redirectTransaction(AbstractTransactionType $transaction, array $postData = []): void
    {
        $this->plugin->getSession()->set(SessionHelper::KEY_PAYMENT_ID, $transaction->getPaymentId());
        $this->plugin->getSession()->set(SessionHelper::KEY_SHORT_ID, $transaction->getShortId());
        $this->plugin->getSession()->set(SessionHelper::KEY_CONFIRM_POST_ARRAY, $postData);
        $this->plugin->getSession()->set(
            SessionHelper::KEY_CART_CHECKSUM,
            Warenkorb::getChecksum($this->plugin->getSession()->getFrontendSession()->Basket())
        );

        header('Location: ' . $transaction->getRedirectUrl());
        exit();
    }

    /**
     * Map JTL ISO to iso used bei heidelpay
     *
     * @see https://docs.heidelpay.com/docs/web-integration#section-localization-and-languages
     * @param string $iso
     * @return string
     */
    public function mapToLocale(string $iso): string
    {
        switch ($iso) {
            case 'ger':
                return 'de-DE';
            default:
                return 'en-GB';
        }
    }
}
