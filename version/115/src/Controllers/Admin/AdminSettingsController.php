<?php declare(strict_types = 1);

namespace Plugin\s360_heidelpay_shop4\Controllers\Admin;

use Exception;
use UnzerSDK\Validators\PrivateKeyValidator;
use UnzerSDK\Validators\PublicKeyValidator;
use Plugin\s360_heidelpay_shop4\Payments\HeidelpayApiAdapter;
use Plugin\s360_heidelpay_shop4\Utils\Config;
use Plugin\s360_heidelpay_shop4\Utils\JtlLinkHelper;
use Plugin\s360_heidelpay_shop4\Webhooks\PaymentEventSubscriber;

/**
 * Admin Settings Controller
 *
 * @package Plugin\s360_heidelpay_shop4\Controllers\Admin
 */
class AdminSettingsController extends AdminController
{
    /**
     * Add some global setting variables to view.
     *
     * @return void
     */
    protected function prepare(): void
    {
        parent::prepare();

        $linkHelper = $this->plugin->getContainer()->make(JtlLinkHelper::class);

        $this->smarty->assign('hpSettings', [
            'formAction' => $linkHelper->getFullAdminTabUrl(JtlLinkHelper::ADMIN_TAB_SETTINGS)
        ]);
    }

    /**
     * Handle Config Action.
     *
     * @return string
     */
    public function handle(): string
    {
        // Handle Save Request
        if (isset($this->request['saveSettings']) && $this->request['saveSettings']) {
            $this->handleSaveRequest();
        }

        // Delete registered webhooks and register them again (useful when domain changed)!
        if (isset($this->request['registerWebhooks']) && $this->request['registerWebhooks']) {
            /** @var HeidelpayApiAdapter $adapter */
            $adapter = $this->plugin->getContainer()->make(HeidelpayApiAdapter::class);
            $this->registerWebhooks($adapter);
        }

        $settings = $this->smarty->get_template_vars('hpSettings');
        $settings['config'] = $this->plugin->getConfig()->all();

        try {
            /** @var HeidelpayApiAdapter $adapter */
            $adapter = $this->plugin->getContainer()->make(HeidelpayApiAdapter::class);
            $webhooks = $adapter->getApi()->fetchAllWebhooks();
            $settings['webhooks'] = \count($webhooks);
        } catch (\Exception $exc) {
            $settings['webhooks'] = false;
        }

        return $this->view('template/settings', ['hpSettings' => $settings]);
    }

    /**
     * Handle Save Request, ie validate and save input.
     *
     * @return void
     */
    protected function handleSaveRequest(): void
    {
        // Set Config
        $this->plugin->getConfig()->set(Config::PRIVATE_KEY, $this->request['privateKey']);
        $this->plugin->getConfig()->set(Config::PUBLIC_KEY, $this->request['publicKey']);
        $this->plugin->getConfig()->set(Config::MERCHANT_ID, $this->request['merchantId']);
        $this->plugin->getConfig()->set(Config::FONT_SIZE, $this->request['fontSize']);
        $this->plugin->getConfig()->set(Config::FONT_COLOR, $this->request['fontColor']);
        $this->plugin->getConfig()->set(Config::FONT_FAMILY, $this->request['fontFamily']);
        $this->plugin->getConfig()->set(Config::SELECTOR_SUBMIT_BTN, $this->request['selectorSubmitButton']);
        $this->plugin->getConfig()->set(
            Config::PQ_SELECTOR_CHANGE_PAYMENT_METHOD,
            $this->request['pqSelectorChangePaymentMethod']
        );
        $this->plugin->getConfig()->set(
            Config::PQ_METHOD_CHANGE_PAYMENT_METHOD,
            $this->request['pqMethodChangePaymentMethod']
        );
        $this->plugin->getConfig()->set(
            Config::PQ_SELECTOR_PAYMENT_INFORMATION,
            $this->request['pqSelectorPaymentInformation']
        );
        $this->plugin->getConfig()->set(
            Config::PQ_METHOD_PAYMENT_INFORMATION,
            $this->request['pqMethodPaymentInformation']
        );
        $this->plugin->getConfig()->set(Config::PQ_SELECTOR_ERRORS, $this->request['pqSelectorErrors']);
        $this->plugin->getConfig()->set(Config::PQ_METHOD_ERRORS, $this->request['pqMethodErrors']);
        $this->plugin->getConfig()->set(Config::PQ_SELECTOR_REVIEW_STEP, $this->request['pqSelectorReviewStep']);
        $this->plugin->getConfig()->set(Config::PQ_METHOD_REVIEW_STEP, $this->request['pqMethodReviewStep']);

        // Validate
        $valid = true;
        if (empty($this->request['privateKey']) || !PrivateKeyValidator::validate($this->request['privateKey'])) {
            $this->addError('Ungültiger Private Key.');
            $valid = false;
        }

        if (empty($this->request['publicKey']) || !PublicKeyValidator::validate($this->request['publicKey'])) {
            $this->addError(
                'Ungültiger Public Key. Bitte stellen Sie sicher,
                 dass sie hier Ihren Public Key und nicht Ihren Private Key angeben!'
            );
            $valid = false;
        }

        // If config is valid, save it in DB
        if ($valid) {
            $this->plugin->getConfig()->save();
            $this->addSuccess('Die Einstellungen wurden erfolgreich gespeichert.');

            // Register Webhooks Event Handlers if needed
            /** @var HeidelpayApiAdapter $adapter */
            $adapter = $this->plugin->getContainer()->make(HeidelpayApiAdapter::class);
            $this->registerWebhooks($adapter);
        }
    }

    /**
     * Register new webhooks.
     *
     * @param HeidelpayApiAdapter $adapter
     * @return void
     */
    protected function registerWebhooks(HeidelpayApiAdapter $adapter): void
    {
        $newEvents = array_keys(PaymentEventSubscriber::getSubscribedEvents());

        if (!empty($newEvents)) {
            /** @var JtlLinkHelper $linkHelper */
            $linkHelper = $this->plugin->getContainer()->make(JtlLinkHelper::class);

            try {
                $adapter->getApi()->registerMultipleWebhooks(
                    $linkHelper->getFullFrontendFileUrl(JtlLinkHelper::FRONTEND_FILE_WEBHOOKS),
                    $newEvents
                );
            } catch (Exception $exc) {
                $this->errorLog('Could not register webhook: ' . $exc->getMessage(), static::class);
            }

            $this->debugLog('Registered the following webhooks: ' . implode(', ', $newEvents), static::class);
            $this->addSuccess('Die Webhooks wurden erfolgreich gespeichert.');
        }
    }
}
