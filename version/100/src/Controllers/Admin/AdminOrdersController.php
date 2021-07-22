<?php declare(strict_types = 1);

namespace Plugin\s360_heidelpay_shop4\Controllers\Admin;

use Bestellung;
use UnzerSDK\Resources\TransactionTypes\Cancellation;
use UnzerSDK\Resources\TransactionTypes\Charge;
use UnzerSDK\Resources\TransactionTypes\Shipment;
use InvalidArgumentException;
use Plugin\s360_heidelpay_shop4\Controllers\AjaxResponse;
use Plugin\s360_heidelpay_shop4\Controllers\HasAjaxResponse;
use Plugin\s360_heidelpay_shop4\Orders\OrderMappingEntity;
use Plugin\s360_heidelpay_shop4\Orders\OrderMappingModel;
use Plugin\s360_heidelpay_shop4\Orders\OrderViewStruct;
use Plugin\s360_heidelpay_shop4\Payments\HeidelpayApiAdapter;
use Plugin\s360_heidelpay_shop4\Utils\Config;
use RuntimeException;
use s360_santander\HeidelPayException;
use StringHandler;

/**
 * Admin Orders Controller
 *
 * @package Plugin\s360_heidelpay_shop4\Controllers\Admin
 */
class AdminOrdersController extends AdminController implements AjaxResponse
{
    use HasAjaxResponse;

    public const TEMPLATE_ID_ORDERS = 'template/orders';
    public const TEMPLATE_ID_ORDER_DETAIL = 'template/partials/_order_detail';
    public const TEMPLATE_ID_ORDER_ITEM = 'template/partials/_order_item';

    /**
     * @var OrderMappingModel
     */
    private $model;

    /**
     * @var HeidelpayApiAdapter
     */
    private $adapter;

    /**
     * Init dependencies
     *
     * @return void
     */
    protected function prepare(): void
    {
        parent::prepare();

        // Abort if there are no API keys set yet (probably new install).
        if (empty($this->plugin->getConfig()->get(Config::PRIVATE_KEY))
            || empty($this->plugin->getConfig()->get(Config::PUBLIC_KEY))
        ) {
            $this->debugLog('Abort Controller. No API Keys set yet.', static::class);
            return;
        }

        $this->model = $this->plugin->getContainer()->make(OrderMappingModel::class);
        $this->adapter = $this->plugin->getContainer()->make(HeidelpayApiAdapter::class);
    }

    /**
     * Handle Orders Action.
     *
     * @return string
     */
    public function handle(): string
    {
        return $this->view(self::TEMPLATE_ID_ORDERS);
    }

    /**
     * Handle ajax request and send json response.
     *
     * Basic Structure of the JSON:
     * {
     *    status: 'success'|'fail'|'error'|'unknown',
     *    messages: [...],
     *    data: [...]
     * }
     *
     * @throws InvalidArgumentException if the provided action is not recognized.
     * @throws RuntimeException if the json encoding encounters an error.
     * @return void
     */
    public function handleAjax(): void
    {
        switch ($this->request['action']) {
            case 'loadOrders':
                $this->handleLoadOrders();
                break;

            case 'getOrderDetails':
                $this->handleGetOrderDetails();
                break;

            default:
                throw new InvalidArgumentException(
                    'Invalid action "' . StringHandler::filterXSS($this->request['action']) . '"'
                );
        }
    }

    /**
     * Handle the order detail view action
     *
     * @throws HeidelPayException if there is an error returned on API-request.
     * @throws RuntimeException if there is an error while using the SDK.
     * @throws RuntimeException if the json is invalid.
     * @return void
     */
    private function handleGetOrderDetails(): void
    {
        // Return error if order id does not exist
        $orderId = $this->request['orderId'] ?? null;
        if (empty($orderId)) {
            $this->jsonResponse(['status' => self::RESULT_ERROR, 'messages' => ['Missing parameter orderId']]);
        }

        // Get Heidelpay Payment and update local details
        $orderMapping = $this->model->find((int) $orderId);

        if (empty($orderMapping)) {
            $this->jsonResponse([
                'status' => self::RESULT_ERROR,
                'messages' => ['Cannot find an order for id: ' . $orderId]
            ]);
        }

        $payment = $this->adapter->fetchPayment($orderMapping->getPaymentId());
        $order = new Bestellung($orderMapping->getId(), true);

        // Load Charges, Cancellations and Shipments
        foreach ($payment->getCharges() as $chg) {
            /** @var Charge $chg */
            $payment->getCharge($chg->getId());
        }

        foreach ($payment->getShipments() as $shipment) {
            /** @var Shipment $shipment */
            $payment->getShipment($shipment->getId());
        }

        foreach ($payment->getCancellations() as $cancel) {
            /** @var Cancellation $cancel */
            $payment->getCancellation($cancel->getId());
        }

        // Update order mapping
        $orderMapping->setOrder($order);
        $orderMapping->setPaymentState($payment->getStateName());
        $orderMapping->setInvoiceId($payment->getInvoiceId());
        $this->model->save($orderMapping);

        // Load View
        $url = $this->plugin->getInsightPortalUrl($orderMapping->getTransactionUniqueId());
        $this->jsonResponse([
            'status' => self::RESULT_SUCCESS,
            'data'   => new OrderViewStruct(
                $orderMapping,
                $this->view(self::TEMPLATE_ID_ORDER_DETAIL, [
                    'hpOrder'     => $order,
                    'hpPayment'   => $payment,
                    'hpPortalUrl' => $url
                ])
            )
        ]);
    }

    /**
     * Handle the loading of orders.
     *
     * @throws RuntimeException if the json is invalid.
     * @return void
     */
    private function handleLoadOrders(): void
    {
        $offset = $this->request['offset'] ?? 0;
        $limit = $this->request['limit'] ?? 100;
        $search = $this->request['search'] ?? null;
        $orders = $this->model->loadOrders((int)$limit, (int)$offset, $search);
        $data = [];

        foreach ($orders as $order) {
            /** @var OrderMappingEntity $order */
            $url = $this->plugin->getInsightPortalUrl($order->getTransactionUniqueId());
            $data[] = new OrderViewStruct(
                $order,
                $this->view(self::TEMPLATE_ID_ORDER_ITEM, [
                    'hpOrder'     => $order,
                    'hpPortalUrl' => $url
                ])
            );
        }

        $this->jsonResponse([
            'status' => self::RESULT_SUCCESS,
            'data'   => $data
        ]);
    }
}
