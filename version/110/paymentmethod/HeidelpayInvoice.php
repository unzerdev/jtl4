<?php declare(strict_types=1);
// @phpcs:ignoreFile PSR1.Files.SideEffects.FoundWithSymbols

use UnzerSDK\Resources\PaymentTypes\BasePaymentType;
use UnzerSDK\Resources\TransactionTypes\AbstractTransactionType;
use UnzerSDK\Resources\TransactionTypes\Charge;
use Plugin\s360_heidelpay_shop4\Payments\HeidelpayPaymentMethod;
use Plugin\s360_heidelpay_shop4\Payments\Traits\HasMetadata;
use ZahlungsInfo;

require_once __DIR__ . '/../init.php';

/**
 * With invoice payments an invoice is sent to the customer - the customer pays upon receipt of the invoice and
 * after the process is finished you (the merchant) receive your money.
 *
 * The Invoice payment method supports you in sending an invoice and the subsequent processes up to payment of the invoice by the customer.
 * As soon as the customer has made the payment via online banking or manual bank transfer, you will receive a notification of the successful payment.
 *
 * @see https://docs.heidelpay.com/docs/invoice-payment
 */
class HeidelpayInvoice extends HeidelpayPaymentMethod
{
    use HasMetadata;

    /**
     * Data the merchant needs to put on the Invoice.
     *
     * The information iban, bic, descriptor and holder data must be be stated on the invoice so that the customer can make the bank transfer.
     * The customer should be informed that he should use the descriptor during online banking transfer.
     * This is the identifier that links the payment to the customer.
     *
     * We also save this data as payment info (tzahlungsinfo) to it is easily accessible.
     *
     * @param Bestellung $order
     * @param Charge $transaction
     * @return array
     */
    public function getOrderAttributes(Bestellung $order, AbstractTransactionType $transaction): array
    {
        // save payment information
        $oPaymentInfo = new ZahlungsInfo(0, $order->kBestellung);
        $oPaymentInfo->kKunde            = $order->kKunde;
        $oPaymentInfo->kBestellung       = $order->kBestellung;
        $oPaymentInfo->cInhaber          = utf8_decode($transaction->getHolder() ?? '');
        $oPaymentInfo->cIBAN             = utf8_decode($transaction->getIban() ?? '');
        $oPaymentInfo->cBIC              = utf8_decode($transaction->getBic() ?? '');
        $oPaymentInfo->cKontoNr          = $oPaymentInfo->cIBAN;
        $oPaymentInfo->cBLZ              = $oPaymentInfo->cBIC;
        $oPaymentInfo->cVerwendungszweck = utf8_decode($transaction->getDescriptor() ?? '');

        isset($oPaymentInfo->kZahlungsInfo) ? $oPaymentInfo->updateInDB() : $oPaymentInfo->insertInDB();

        return [
            self::ATTR_IBAN                   => $oPaymentInfo->cIBAN,
            self::ATTR_BIC                    => $oPaymentInfo->cBIC,
            self::ATTR_TRANSACTION_DESCRIPTOR => $oPaymentInfo->cVerwendungszweck,
            self::ATTR_ACCOUNT_HOLDER         => $oPaymentInfo->cInhaber,
        ];
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
            $order->cBestellNr ?? null,
            $this->createMetadata()
        );
    }
}
