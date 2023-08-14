<?php declare(strict_types = 1);

namespace Plugin\s360_heidelpay_shop4\Controllers;

use PaymentMethod;
use Plugin\s360_heidelpay_shop4\Payments\HeidelpayPaymentMethod;
use Plugin\s360_heidelpay_shop4\Payments\Interfaces\HandleStepReviewOrderInterface;
use Plugin\s360_heidelpay_shop4\Utils\Config;
use Plugin\s360_heidelpay_shop4\Utils\SessionHelper;
use Shop;

/**
 * Payment Frontend Controller.
 *
 * Hooks into the different payment steps, to provide additional functionallity to the payment methods.
 *
 * @package Plugin\s360_heidelpay_shop4\Controllers
 */
class PaymentController extends Controller
{
    public const STATE_ABORT = 'abort';
    public const STATE_HANDLE_ADDITIONAL = 'additional';
    public const STATE_HANDLE_ORDER_REVIEW = 'review';
    public const STATE_HANDLE_FINILIZED_ORDER = 'finilized';

    /**
     * @inheritDoc
     */
    public function handle(): string
    {
        $payment = $this->plugin->getSession()->getFrontendSession()->get('Zahlungsart');

        if (Shop::getPageType() === \PAGE_BESTELLVORGANG && $payment) {
            $checkoutSession = $this->plugin->getSession()->get(SessionHelper::KEY_CHECKOUT_SESSION);
            $paymentMethod = PaymentMethod::create($payment->cModulId);

            // Not a heidelpay method, abort!
            if (!$paymentMethod instanceof HeidelpayPaymentMethod) {
                return self::STATE_ABORT;
            }

            // Review Order => plugin session contains checkoutSession
            if ($checkoutSession && $paymentMethod instanceof HandleStepReviewOrderInterface) {
                $this->debugLog('Handle Review Order Step', get_class($paymentMethod));
                $template = $paymentMethod->handleStepReviewOrder($this->smarty);

                if ($template) {
                    $this->debugLog('Add Template: ' . $template, get_class($paymentMethod));

                    $pqMethod = $this->plugin->getConfig()->get(Config::PQ_METHOD_REVIEW_STEP, 'append');
                    pq(
                        $this->plugin->getConfig()->get(Config::PQ_SELECTOR_ERRORS, '#order-confirm')
                    )->$pqMethod($this->view($template));
                }

                return self::STATE_HANDLE_ORDER_REVIEW;
            }
        }

        // Clear Payment Data if the customer wants to change his payment or shipping method
        if (Shop::getPageType() === \PAGE_BESTELLVORGANG &&
            (verifyGPCDataInteger('editZahlungsart') > 0 || verifyGPCDataInteger('editVersandart') > 0)
        ) {
            $this->plugin->getSession()->clearCheckoutSession();
        }

        // TODO: Order Finalized => PAGE_BESTELLABSCHLUSS / HOOK_BESTELLABSCHLUSS_PAGE

        return self::STATE_ABORT;
    }
}
