<?php
declare(strict_types = 1);

namespace Plugin\s360_heidelpay_shop4\Controllers;

use Exception;
use UnzerSDK\Exceptions\UnzerApiException;
use UnzerSDK\Resources\PaymentTypes\InstallmentSecured;
use Plugin\s360_heidelpay_shop4\Foundation\Plugin;
use Plugin\s360_heidelpay_shop4\Orders\OrderMappingModel;
use Plugin\s360_heidelpay_shop4\Payments\HeidelpayApiAdapter;
use Plugin\s360_heidelpay_shop4\Payments\HeidelpayPaymentMethod;
use RuntimeException;

/**
 * Sync Workflow Controller
 * @package Plugin\s360_heidelpay_shop4\Controllers
 */
class SyncWorkflowController extends Controller
{
    /**
     * @var OrderMappingModel
     */
    private $model;

    /**
     * @var HeidelpayApiAdapter
     */
    private $adapter;

    /**
     * @inheritDoc
     */
    public function __construct(Plugin $plugin)
    {
        parent::__construct($plugin);

        $this->model = $this->plugin->getContainer()->make(OrderMappingModel::class);
        $this->adapter = $this->plugin->getContainer()->make(HeidelpayApiAdapter::class);
    }

    /**
     * Save the invoice id for this order if it is a heidelpay order
     *
     * @SuppressWarnings(PHPMD.ExitExpression)
     * @return string not used
     */
    public function handle(): string
    {
        // Validate Request.
        $this->debugLog('Called SyncWorkflowController with the following data: ' . print_r($this->request, true));
        $attrs = [];
        parse_str(str_replace('|', '&', $this->request['attrs'] ?? ''), $attrs);

        if (empty($attrs) || empty($attrs[HeidelpayPaymentMethod::ATTR_PAYMENT_ID])
            || empty($this->request['invoice_id'])
        ) {
            $this->errorLog('Missing parameter payment_id or invoice_id' . print_r($attrs, true), static::class);
            http_response_code(403);
            exit;
        }

        // Get mapped order. Skip non heidelpay orders.
        $order = $this->model->findByPayment($attrs[HeidelpayPaymentMethod::ATTR_PAYMENT_ID]);
        if (empty($order)) {
            $this->noticeLog(
                'Could not find a mapped order for order id ' . $attrs[HeidelpayPaymentMethod::ATTR_PAYMENT_ID] . '
                 (Possible Reason: Used non-Heidelpay Payment Method)',
                static::class
            );
            exit;
        }

        // Save Invoice ID in order mapping so that we can use it in the shipment call
        $order->setInvoiceId((string) $this->request['invoice_id']);
        $saved = $this->model->save($order);
        if ($saved < 0) {
            $this->errorLog(
                'Could not save invoice id ' . $this->request['invoice_id'] . ' for order ' . $order->getId(),
                static::class
            );
            exit;
        }

        // Update Payment Resource (needed for HDD, as it needs invoiceDate etc)
        $this->updatePaymentType($order->getPaymentTypeId());
        $this->debugLog('Number of affected rows: ' . $saved, static::class);
        $this->debugLog(
            'Saved invoice id ' . $this->request['invoice_id'] . ' for order ' . json_encode($order->jsonSerialize()),
            static::class
        );
        return '';
    }

    /**
     * Update payment type if necessary
     *
     * @param string $paymentTypeId
     * @return void
     */
    private function updatePaymentType(string $paymentTypeId): void
    {
        try {
            $paymentType = $this->adapter->fetchPaymentType($paymentTypeId);

            if ($paymentType instanceof InstallmentSecured) {
                $paymentType->setInvoiceDate(date('Y-m-d'));
                $paymentType->setInvoiceDueDate(date('Y-m-d'));
                $this->adapter->getApi()->updatePaymentType($paymentType);
                $this->debugLog('Updated payment type: ' . json_encode($paymentType->jsonSerialize()));
            }
        } catch (UnzerApiException $exc) {
            $msg = $exc->getMerchantMessage() . ' | Id: ' . $exc->getErrorId() . ' | Code: ' . $exc->getCode();
            $this->errorLog(utf8_decode($msg), static::class);
        } catch (RuntimeException $exc) {
            $this->errorLog(
                'An exception was thrown while using the Heidelpay SDK: ' . utf8_decode($exc->getMessage()),
                static::class
            );
        } catch (Exception $exc) {
            $this->errorLog('An error occured in the payment process: ' . $exc->getMessage(), static::class);
        }
    }
}
