<?php
declare(strict_types = 1);

namespace Plugin\s360_heidelpay_shop4\Payments;

use HeidelpayAlipay;
use HeidelpayCreditCard;
use HeidelpayEPS;
use HeidelpayFlexiPayDirect;
use HeidelpayGiropay;
use HeidelpayHirePurchaseDirectDebit;
use HeidelpayiDEAL;
use HeidelpayInvoice;
use HeidelpayInvoiceFactoring;
use HeidelpayInvoiceGuaranteed;
use HeidelpayPayPal;
use HeidelpayPrepayment;
use HeidelpayPrzelewy24;
use HeidelpaySEPADirectDebit;
use HeidelpaySEPADirectDebitGuaranteed;
use HeidelpaySofort;
use HeidelpayWeChatPay;
use InvalidArgumentException;
use PaymentMethod;
use UnzerSDK\Constants\IdStrings;
use UnzerSDK\Resources\PaymentTypes\Alipay;
use UnzerSDK\Resources\PaymentTypes\BasePaymentType;
use UnzerSDK\Resources\PaymentTypes\Card;
use UnzerSDK\Resources\PaymentTypes\EPS;
use UnzerSDK\Resources\PaymentTypes\Giropay;
use UnzerSDK\Resources\PaymentTypes\Ideal;
use UnzerSDK\Resources\PaymentTypes\InstallmentSecured;
use UnzerSDK\Resources\PaymentTypes\Invoice;
use UnzerSDK\Resources\PaymentTypes\InvoiceSecured;
use UnzerSDK\Resources\PaymentTypes\Paypal;
use UnzerSDK\Resources\PaymentTypes\PIS;
use UnzerSDK\Resources\PaymentTypes\Prepayment;
use UnzerSDK\Resources\PaymentTypes\Przelewy24;
use UnzerSDK\Resources\PaymentTypes\SepaDirectDebit;
use UnzerSDK\Resources\PaymentTypes\SepaDirectDebitSecured;
use UnzerSDK\Resources\PaymentTypes\Sofort;
use UnzerSDK\Resources\PaymentTypes\Wechatpay;
use UnzerSDK\Services\IdService;

/**
 * Factory to create payment method modules.
 *
 * @package Plugin\s360_heidelpay_shop4\Payments
 */
class PaymentMethodModuleFactory
{
    /**
     * @var int
     */
    private $kPlugin;

    /**
     * @var array
     */
    private $options = [];

    public const MODULE = [
        HeidelpayAlipay::class                    => 'unzeralipay',
        HeidelpayCreditCard::class                => 'unzerkreditkarte',
        HeidelpayEPS::class                       => 'unzereps',
        HeidelpayFlexiPayDirect::class            => 'unzerflexipaydirect',
        HeidelpayGiropay::class                   => 'unzergiropay',
        HeidelpayHirePurchaseDirectDebit::class   => 'unzerflexipayinstallment(hirepurchase)',
        HeidelpayInvoice::class                   => 'unzerrechnung',
        HeidelpayiDEAL::class                     => 'unzerideal',
        HeidelpayInvoiceFactoring::class          => 'unzerfakturierungvonrechnungen',
        HeidelpayInvoiceGuaranteed::class         => 'unzerrechnung(guaranteed)',
        HeidelpayPayPal::class                    => 'unzerpaypal',
        HeidelpayPrepayment::class                => 'unzerprepayment',
        HeidelpayPrzelewy24::class                => 'unzerprzelewy24',
        HeidelpaySofort::class                    => 'unzersofort',
        HeidelpaySEPADirectDebit::class           => 'unzersepalastschrift',
        HeidelpaySEPADirectDebitGuaranteed::class => 'unzersepalastschrift(guaranteed)',
        HeidelpayWeChatPay::class                 => 'unzerwechatpay'
    ];

    private const FACTORIES = [
        Alipay::class                    => 'createAlipayModule',
        Card::class                      => 'createCardModule',
        EPS::class                       => 'createEPSModule',
        Giropay::class                   => 'createGiropayModule',
        InstallmentSecured::class        => 'createHirePurchaseDirectDebitModule',
        Ideal::class                     => 'createIdealModule',
        Invoice::class                   => 'createInvoiceModule',
        // InvoiceFactoring::class          => 'createInvoiceFactoringModule',
        // InvoiceGuaranteed::class         => 'createInvoiceGuaranteedModule',
        InvoiceSecured::class            => 'createInvoiceSecuredModule',
        Paypal::class                    => 'createPaypalModule',
        PIS::class                       => 'createFlexiPayDirectModule',
        Prepayment::class                => 'createPrepaymentModule',
        Przelewy24::class                => 'createPrzelewy24Module',
        SepaDirectDebit::class           => 'createSepaDirectDebitModule',
        SepaDirectDebitSecured::class    => 'createSepaDirectDebitGuaranteedModule',
        Sofort::class                    => 'createSofortModule',
        Wechatpay::class                 => 'createWechatpayModule'
    ];

    /**
     * @param int $kPlugin
     */
    public function __construct(int $kPlugin)
    {
        require_once PFAD_ROOT . PFAD_INCLUDES_MODULES . 'PaymentMethod.class.php';

        $this->kPlugin = $kPlugin;
    }

    /**
     * Create a payment method module for a payment type.
     *
     * @param BasePaymentType $type
     * @param array $options - Additional options
     * @return HeidelpayPaymentMethod
     * @throws InvalidArgumentException if no factory method for the provided type exists.
     */
    public function createForType(BasePaymentType $type, array $options = []): HeidelpayPaymentMethod
    {
        $class = get_class($type);
        $this->options = $options;
        if (array_key_exists($class, self::FACTORIES)) {
            return call_user_func([$this, self::FACTORIES[$class]]);
        }

        throw new InvalidArgumentException('Cannot find a factory for type ' . $class);
    }

    /**
     * Create Alipay Payment Module.
     *
     * @return HeidelpayPaymentMethod|HeidelpayEPS
     */
    public function createAlipayModule(): HeidelpayPaymentMethod
    {
        return PaymentMethod::create(
            'kPlugin_' . $this->kPlugin . '_' . self::MODULE[HeidelpayAlipay::class]
        );
    }

    /**
     * Create Credit Card Module
     *
     * @return HeidelpayPaymentMethod|HeidelpayCreditCard
     */
    public function createCardModule(): HeidelpayPaymentMethod
    {
        return PaymentMethod::create(
            'kPlugin_' . $this->kPlugin . '_' . self::MODULE[HeidelpayCreditCard::class]
        );
    }

    /**
     * Creatte EPS Module.
     *
     * @return HeidelpayPaymentMethod|HeidelpayEPS
     */
    public function createEPSModule(): HeidelpayPaymentMethod
    {
        return PaymentMethod::create(
            'kPlugin_' . $this->kPlugin . '_' . self::MODULE[HeidelpayEPS::class]
        );
    }

    /**
     * Create Giropay Module
     *
     * @return HeidelpayPaymentMethod|HeidelpayGiropay
     */
    public function createGiropayModule(): HeidelpayPaymentMethod
    {
        return PaymentMethod::create(
            'kPlugin_' . $this->kPlugin . '_' . self::MODULE[HeidelpayGiropay::class]
        );
    }

    /**
     * Create Hire Purchase Payment Module.
     *
     * @return HeidelpayPaymentMethod|HeidelpayHirePurchaseDirectDebit
     */
    public function createHirePurchaseDirectDebitModule(): HeidelpayPaymentMethod
    {
        return PaymentMethod::create(
            'kPlugin_' . $this->kPlugin . '_' . self::MODULE[HeidelpayHirePurchaseDirectDebit::class]
        );
    }

    /**
     * Create iDEAL Payment Module.
     *
     * @return HeidelpayPaymentMethod|HeidelpayiDEAL
     */
    public function createIdealModule(): HeidelpayPaymentMethod
    {
        return PaymentMethod::create(
            'kPlugin_' . $this->kPlugin . '_' . self::MODULE[HeidelpayiDEAL::class]
        );
    }

    /**
     * Create Invoice Payment Module.
     *
     * @return HeidelpayPaymentMethod|HeidelpayInvoice
     */
    public function createInvoiceModule(): HeidelpayPaymentMethod
    {
        return PaymentMethod::create(
            'kPlugin_' . $this->kPlugin . '_' . self::MODULE[HeidelpayInvoice::class]
        );
    }

    /**
     * Create Invoice Factoring Payment Module.
     *
     * @return HeidelpayPaymentMethod|HeidelpayInvoiceFactoring
     */
    public function createInvoiceFactoringModule(): HeidelpayPaymentMethod
    {
        return PaymentMethod::create(
            'kPlugin_' . $this->kPlugin . '_' . self::MODULE[HeidelpayInvoiceFactoring::class]
        );
    }

    /**
     * Create Invoice Guaranteed Payment Module.
     *
     * @return HeidelpayPaymentMethod|HeidelpayInvoiceGuaranteed
     */
    public function createInvoiceGuaranteedModule(): HeidelpayPaymentMethod
    {
        return PaymentMethod::create(
            'kPlugin_' . $this->kPlugin . '_' . self::MODULE[HeidelpayInvoiceGuaranteed::class]
        );
    }

    /**
     * Create Invoice Secured Payment Module.
     *
     * !NOTE: Unzer has changed the sdk so that both factoring and guaranteed invoices are now
     * ! mapped to Invoice Secured, but it is not clear if one is removed or they both should act the same
     * ! As a result, we try to differentiate based on their Id String, default to invoice guaranteed!
     *
     * @return HeidelpayPaymentMethod|HeidelpayInvoiceGuaranteed|HeidelpayInvoiceFactoring
     */
    public function createInvoiceSecuredModule(): HeidelpayPaymentMethod
    {
        $module = self::MODULE[HeidelpayInvoiceGuaranteed::class];

        if (array_key_exists('id-string', $this->options)) {
            if (IdService::getResourceTypeFromIdString($this->options['id-string']) == IdStrings::INVOICE_FACTORING) {
                $module = self::MODULE[HeidelpayInvoiceFactoring::class];
            }
        }

        return PaymentMethod::create(
            'kPlugin_' . $this->kPlugin . '_' . $module
        );
    }

    /**
     * Create Paypayl Payment Module.
     *
     * @return HeidelpayPaymentMethod|HeidelpayPayPal
     */
    public function createPaypalModule(): HeidelpayPaymentMethod
    {
        return PaymentMethod::create(
            'kPlugin_' . $this->kPlugin . '_' . self::MODULE[HeidelpayPayPal::class]
        );
    }

    /**
     * Create FlexiPay Direct (PIS) Payment Module.
     *
     * @return HeidelpayPaymentMethod|HeidelpayFlexiPayDirect
     */
    public function createFlexiPayDirectModule(): HeidelpayPaymentMethod
    {
        return PaymentMethod::create(
            'kPlugin_' . $this->kPlugin . '_' . self::MODULE[HeidelpayFlexiPayDirect::class]
        );
    }

    /**
     * Create Prepayment Payment Module
     *
     * @return HeidelpayPaymentMethod|HeidelpayPrepayment
     */
    public function createPrepaymentModule(): HeidelpayPaymentMethod
    {
        return PaymentMethod::create(
            'kPlugin_' . $this->kPlugin . '_' . self::MODULE[HeidelpayPrepayment::class]
        );
    }

    /**
     * Create Przelewy24 Payment Module.
     *
     * @return HeidelpayPaymentMethod|HeidelpayPrzelewy24
     */
    public function createPrzelewy24Module(): HeidelpayPaymentMethod
    {
        return PaymentMethod::create(
            'kPlugin_' . $this->kPlugin . '_' . self::MODULE[HeidelpayPrzelewy24::class]
        );
    }

    /**
     * Create SEPA Direct Debit Module
     *
     * @return HeidelpayPaymentMethod|HeidelpaySEPADirectDebit
     */
    public function createSepaDirectDebitModule(): HeidelpayPaymentMethod
    {
        return PaymentMethod::create(
            'kPlugin_' . $this->kPlugin . '_' . self::MODULE[HeidelpaySEPADirectDebit::class]
        );
    }

    /**
     * Create SEPA Direct Debit (guaranteed) Module
     *
     * @return HeidelpayPaymentMethod|HeidelpaySEPADirectDebitGuaranteed
     */
    public function createSepaDirectDebitGuaranteedModule(): HeidelpayPaymentMethod
    {
        return PaymentMethod::create(
            'kPlugin_' . $this->kPlugin . '_' . self::MODULE[HeidelpaySEPADirectDebitGuaranteed::class]
        );
    }

    /**
     * Create Sofort Payment Module.
     *
     * @return HeidelpayPaymentMethod|HeidelpaySofort
     */
    public function createSofortModule(): HeidelpayPaymentMethod
    {
        return PaymentMethod::create(
            'kPlugin_' . $this->kPlugin . '_' . self::MODULE[HeidelpaySofort::class]
        );
    }

    /**
     * Create WeChat Pay Payment Module.
     *
     * @return HeidelpayPaymentMethod|HeidelpayWeChatPay
     */
    public function createWechatpayModule(): HeidelpayPaymentMethod
    {
        return PaymentMethod::create(
            'kPlugin_' . $this->kPlugin . '_' . self::MODULE[HeidelpayWeChatPay::class]
        );
    }
}
