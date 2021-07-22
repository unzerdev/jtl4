<?php declare(strict_types = 1);

namespace Plugin\s360_heidelpay_shop4\Controllers;

use Plugin\s360_heidelpay_shop4\Utils\AlertService;
use Plugin\s360_heidelpay_shop4\Utils\Config;

/**
 * Frontend Output Controller
 *
 * @package Plugin\s360_heidelpay_shop4\Controllers
 */
class FrontendOutputController extends Controller
{
    private const TEMPLATE_ID_ERROR_ALERT = 'template/partials/payment_error';
    private const TEMPLATE_ID_CHANGE_PAYMENT_METHOD = 'template/partials/change_payment';

    /**
     * @inheritDoc
     */
    public function handle(): string
    {
        // Add "Change Payment Button"/Link
        if (Shop()->getPageType() == \PAGE_BESTELLVORGANG) {
            $snippet = $this->view(self::TEMPLATE_ID_CHANGE_PAYMENT_METHOD);
            $pqMethod = $this->plugin->getConfig()->get(Config::PQ_METHOD_CHANGE_PAYMENT_METHOD, 'append');
            pq(
                $this->plugin->getConfig()->get(
                    Config::PQ_SELECTOR_CHANGE_PAYMENT_METHOD,
                    '#order-additional-payment'
                )
            )->$pqMethod($snippet);
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
