<?php declare(strict_types=1);
// @phpcs:ignoreFile PSR1.Files.SideEffects.FoundWithSymbols

use UnzerSDK\Resources\PaymentTypes\BasePaymentType;
use UnzerSDK\Resources\TransactionTypes\AbstractTransactionType;
use UnzerSDK\Resources\TransactionTypes\Charge;
use Plugin\s360_heidelpay_shop4\Payments\HeidelpayPaymentMethod;
use Plugin\s360_heidelpay_shop4\Payments\Interfaces\HandleStepAdditionalInterface;
use Plugin\s360_heidelpay_shop4\Payments\Interfaces\RedirectPaymentInterface;
use Plugin\s360_heidelpay_shop4\Utils\Config;

require_once __DIR__ . '/../init.php';

/**
 * HeidelpaySEPADirectDebit Payment Method.
 *
 * SEPA stands for "Single Euro Payments Area", and is a European Union initiative.
 * It is driven by the EU institutions, in particular the European Commission
 * and the European Central Bank.
 *
 * SEPA Direct Debit is an Europe-wide Direct Debit system that allows merchants
 * to collect Euro-denominated payments from accounts in the 34 SEPA countries
 * and associated territories in a safe and efficient way.
 *
 * @see https://docs.heidelpay.com/docs/sepa-direct-debit-payment
 */
class HeidelpaySEPADirectDebit extends HeidelpayPaymentMethod implements RedirectPaymentInterface, HandleStepAdditionalInterface
{
    /**
     * Add SEPA Mandate text to view.
     *
     * @param JTLSmarty $view
     * @return void
     */
    public function handleStepAdditional(JTLSmarty $view): void
    {
        $data = $view->getTemplateVars('hpPayment') ?: [];
        $data['mandate'] = str_replace(
            '%MERCHANT_NAME%',
            Shop::getSettingValue(CONF_GLOBAL, 'global_shopname'),
            $this->plugin->trans(Config::LANG_SEPA_MANDATE)
        );

        $view->assign('hpPayment', $data);
    }

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
