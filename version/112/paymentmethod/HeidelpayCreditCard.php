<?php declare(strict_types=1);
// @phpcs:ignoreFile PSR1.Files.SideEffects.FoundWithSymbols

use UnzerSDK\Resources\PaymentTypes\BasePaymentType;
use UnzerSDK\Resources\PaymentTypes\Card;
use UnzerSDK\Resources\TransactionTypes\AbstractTransactionType;
use UnzerSDK\Resources\TransactionTypes\Charge;
use Plugin\s360_heidelpay_shop4\Payments\HeidelpayPaymentMethod;
use Plugin\s360_heidelpay_shop4\Payments\Interfaces\HandleStepAdditionalInterface;
use Plugin\s360_heidelpay_shop4\Payments\Interfaces\RedirectPaymentInterface;
use Plugin\s360_heidelpay_shop4\Payments\Traits\HasCustomer;
use Plugin\s360_heidelpay_shop4\Payments\Traits\HasMetadata;
use Plugin\s360_heidelpay_shop4\Utils\Config;
use ZahlungsInfo;

require_once __DIR__ . '/../init.php';

/**
 * HeidelpayCreditCard Payment Method.
 *
 * Card payment is one of the most common and popular payment methods for e-commerce.
 * Heidelpay supports nearly every available card brand worldwide:
 * * Visa
 * * Mastercard
 * * Amex
 * * Diners
 * * Carte Blue
 * * JCB
 * * CUP
 * * ...
 *
 * @see https://docs.heidelpay.com/docs/card-payment
 */
class HeidelpayCreditCard extends HeidelpayPaymentMethod implements RedirectPaymentInterface, HandleStepAdditionalInterface
{
    use HasMetadata;
    use HasCustomer;

    // Order Attributes
    public const ATTR_CARD_HOLDER = 'unzer_card_holder';
    public const ATTR_CARD_NUMBER = 'unzer_card_number';
    public const ATTR_CARD_EXPIRY_DATE = 'unzer_card_expiry_date';
    public const ATTR_CARD_CVC = 'unzer_card_cvc';
    public const ATTR_CARD_TYPE = 'unzer_card_type';

    /**
     * Save the credit card information.
     *
     * @param Bestellung $order
     * @param Charge $transaction
     * @return array
     */
    public function getOrderAttributes(Bestellung $order, AbstractTransactionType $transaction): array
    {
        try {
            /** @var Card $type */
            $type = $transaction->getPayment()->getPaymentType();

            // Save Payment Info
            $oPaymentInfo = new ZahlungsInfo(0, $order->kBestellung);
            $oPaymentInfo->kKunde       = $order->kKunde;
            $oPaymentInfo->kBestellung  = $order->kBestellung;
            $oPaymentInfo->cInhaber     = utf8_decode($type->getCardHolder() ?? '');
            $oPaymentInfo->cKartenNr    = utf8_decode($type->getNumber() ?? '');
            $oPaymentInfo->cGueltigkeit = utf8_decode($type->getExpiryDate() ?? '');
            $oPaymentInfo->cCVV         = utf8_decode($type->getCvc() ?? '');
            $oPaymentInfo->cKartenTyp   = utf8_decode($type->getBrand() ?? '');

            isset($oPaymentInfo->kZahlungsInfo) ? $oPaymentInfo->updateInDB() : $oPaymentInfo->insertInDB();

            // Order Attributes
            return [
                self::ATTR_CARD_HOLDER      => $oPaymentInfo->cInhaber,
                self::ATTR_CARD_NUMBER      => $oPaymentInfo->cKartenNr,
                self::ATTR_CARD_EXPIRY_DATE => $oPaymentInfo->cGueltigkeit,
                self::ATTR_CARD_CVC         => $oPaymentInfo->cCVV,
                self::ATTR_CARD_TYPE        => $oPaymentInfo->cKartenTyp
            ];
        } catch (Exception $exc) {
            $this->errorLog(
                'An exception was thrown while trying to get the order attributes '
                . utf8_decode($exc->getMessage()),
                static::class
            );
        }

        return [];
    }
    /**
     * Pass Styling Options to template
     *
     * @param JTLSmarty $view
     * @return void
     */
    public function handleStepAdditional(JTLSmarty $view): void
    {
        $data = $view->getTemplateVars('hpPayment') ?: [];
        $data['styling']             = [
            Config::FONT_COLOR  => $this->plugin->getConfig()->get(Config::FONT_COLOR),
            Config::FONT_FAMILY => $this->plugin->getConfig()->get(Config::FONT_FAMILY),
            Config::FONT_SIZE   => $this->plugin->getConfig()->get(Config::FONT_SIZE),
        ];
        $view->assign('hpPayment', $data);
    }

    /**
     * Although Cards support both auth as well as charge calls, we only support Direct Charge.
     *
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
