<?php declare(strict_types = 1);

namespace Plugin\s360_heidelpay_shop4\Controllers;

use Plugin\s360_heidelpay_shop4\Utils\AlertService;
use Plugin\s360_heidelpay_shop4\Utils\Config;
use Shop;

/**
 * Frontend Output Controller
 *
 * @package Plugin\s360_heidelpay_shop4\Controllers
 */
class FrontendOutputController extends Controller
{
    private const TEMPLATE_ID_ERROR_ALERT = 'template/partials/payment_error';
    private const TEMPLATE_ID_CHANGE_PAYMENT_METHOD = 'template/partials/change_payment';
    private const TEMPLATE_ID_PAYMENT_INFO = 'template/partials/payment_info';

    /**
     * @inheritDoc
     */
    public function handle(): string
    {
        // Add "Change Payment Button"/Link
        if (Shop::getInstance()->getPageType() == \PAGE_BESTELLVORGANG) {
            $snippet = $this->view(self::TEMPLATE_ID_CHANGE_PAYMENT_METHOD);
            $pqMethod = $this->plugin->getConfig()->get(Config::PQ_METHOD_CHANGE_PAYMENT_METHOD, 'append');
            pq(
                $this->plugin->getConfig()->get(
                    Config::PQ_SELECTOR_CHANGE_PAYMENT_METHOD,
                    '#order-additional-payment'
                )
            )->$pqMethod($snippet);
        }

        // Add Payment Information
        if (Shop::getInstance()->getPageType() == \PAGE_BESTELLABSCHLUSS) {
            /** @var Bestellung $order */
            $order = $this->smarty->get_template_vars('Bestellung');

            if (!empty($order) && (
                strpos($order->Zahlungsart->cModulId, 'unzervorkasse') !== false ||
                strpos($order->Zahlungsart->cModulId, 'unzerprepayment') !== false ||
                strpos($order->Zahlungsart->cModulId, 'unzerrechnung') !== false
            )) {
                $snippet = $this->view(self::TEMPLATE_ID_PAYMENT_INFO);
                $pqMethod = $this->plugin->getConfig()->get(Config::PQ_METHOD_PAYMENT_INFORMATION, 'append');

                pq(
                    $this->plugin->getConfig()->get(
                        Config::PQ_SELECTOR_PAYMENT_INFORMATION,
                        '#order-confirmation'
                    )
                )->$pqMethod($snippet);
            }
        }

        /** @var AlertService $alertService */
        $alertService = $this->plugin->getContainer()->make(AlertService::class);

        if ($alertService->hasAlerts(AlertService::TYPE_ERROR)) {
            $errors = $alertService->all(AlertService::TYPE_ERROR);

            foreach ($errors as $message) {
                $snippet = $this->view(self::TEMPLATE_ID_ERROR_ALERT, ['s360_hp_error_alert' => $message]);
                $pqMethod = $this->plugin->getConfig()->get(Config::PQ_METHOD_ERRORS, 'prepend');
                pq(
                    $this->plugin->getConfig()->get(
                        Config::PQ_SELECTOR_ERRORS,
                        '#result-wrapper, .basket_wrapper, .order-completed'
                    )
                )->$pqMethod($snippet);
            }

            $alertService->clear(AlertService::TYPE_ERROR);
            $this->plugin->getSession()->clear();
        }

        return '';
    }
}
