<?php
declare(strict_types = 1);

namespace Plugin\s360_heidelpay_shop4\Utils;

use Plugin\s360_heidelpay_shop4\Utils\Config;
use Session;
use StringHandler;

/**
 * Session helper
 *
 * @package Plugins\s360_heidelpay_shop4\Utils
 */
class SessionHelper
{
    use JtlLoggerTrait;

    public const KEY_ORDER_ID = 'orderId';
    public const KEY_RESOURCE_ID = 'resourceId';
    public const KEY_CART_CHECKSUM = 'cartChecksum';
    public const KEY_CHECKOUT_SESSION = 'checkoutSession';
    public const KEY_CONFIRM_POST_ARRAY = 'confirmPostArray';
    public const KEY_SHORT_ID = 'shortId';
    public const KEY_PAYMENT_ID = 'paymentId';
    public const KEY_CUSTOMER_ID = 'customerId';

    /**
     * @var Session
     */
    private $session;

    /**
     * @var AlertService
     */
    private $alerts;

    /**
     * Init Session
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function __construct()
    {
        // We need to set cISOSprache ourself (as its not set there for some reason)
        // If not, the JTL Session class might break the admin (AdminSession is not compatible with Session class)
        if (isset($_SESSION['AdminAccount']) && !isset($_SESSION['cISOSprache']) && isset($_SESSION['kSprache'])) {
            $langs = \Shop::DB()->query("SELECT * FROM tsprache", 2);
            foreach ($langs as $lang) {
                if ($_SESSION['kSprache'] == $lang->kSprache) {
                    $_SESSION['cISOSprache'] = trim($lang->cISO);
                    $_SESSION['currentLanguage'] = clone $lang;
                    break;
                }
            }
        }

        $this->session = Session::getInstance();
    }

    /**
     * Set Alert Service
     *
     * @param AlertService $service
     * @return void
     */
    public function setAlertService(AlertService $service): void
    {
        $this->alerts = $service;
    }

    /**
     * Set a session value for a key.
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function set(string $key, $value): void
    {
        $this->session->set(
            $this->buildSessionKey([Config::PLUGIN_SESSION, $key]),
            $value
        );
    }

    /**
     * Get a session value for a key.
     *
      * @param string $key
      * @param mixed $default
      * @return mixed
     */
    public function get(string $key, $default = null)
    {
        return $this->session->get(
            $this->buildSessionKey([Config::PLUGIN_SESSION, $key]),
            $default
        );
    }

    /**
     * Get Plugin session entries.
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     * @return array
     */
    public function all(): array
    {
        return $_SESSION[Config::PLUGIN_SESSION] ?? [];
    }

    /**
     * Check if a session key exists.
     *
     * @param string $key
     * @return boolean
     */
    public function has(string $key): bool
    {
        return $this->get($key) !== null;
    }

    /**
     * Clear/delete a session entry
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     * @param string|null $key
     * @return void
     */
    public function clear(?string $key = null): void
    {
        if (is_null($key)) {
            unset($_SESSION[Config::PLUGIN_SESSION]);
            return;
        }

        $items = &$_SESSION[Config::PLUGIN_SESSION];
        $segments = explode('.', $key);
        $lastSegment = array_pop($segments);

        foreach ($segments as $segment) {
            if (!isset($items[$segment]) || !is_array($items[$segment])) {
                continue;
            }

            $items = &$items[$segment];
        }

        unset($items[$lastSegment]);
    }

    /**
     * Get the frontend session.
     *
     * @return Session
     */
    public function getFrontendSession(): Session
    {
        return $this->session;
    }
    /**
     * Build a session key in dot notation
     *
     * @param array $parts
     * @return string
     */
    public function buildSessionKey(array $parts): string
    {
        $filtered = array_filter($parts);
        return implode('.', $filtered);
    }

    /**
     * Get the payment checkout session
     *
     * @return array
     */
    public function getCheckoutSession()
    {
        return $this->get(self::KEY_CHECKOUT_SESSION, []);
    }

    /**
     * Save payment data in session
     *
     * @param string $resourceId
     * @return void
     */
    public function setCheckoutSession(string $resourceId): void
    {
        $this->set(
            $this->buildSessionKey(
                [self::KEY_CHECKOUT_SESSION, self::KEY_RESOURCE_ID]
            ),
            StringHandler::filterXSS($resourceId)
        );
    }

    /**
     * Clear the payment session
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     * @return void
     */
    public function clearCheckoutSession(): void
    {
        $this->clear(self::KEY_CHECKOUT_SESSION);
    }

    /**
     * Set an error alert and redirect if needed.
     *
     * In case of a redirect it clears the plugin session first.
     *
     * @param string $merchant The merchant message that is logged.
     * @param string $customer The customer message that is displayed
     * @param string $key a unique identifier for the alert
     * @param string|null $redirect Url to redirect to
     * @param string|null $context
     * @return void
     */
    public function addErrorAlert(string $merchant, string $customer, string $key, string $redirect = null, string $context = null): void
    {
        if (empty($redirect)) {
            $this->clear();
        }

        $this->errorLog($merchant, $context ?? static::class);
        $this->redirectError($customer, $key, $redirect);
    }

    /**
     * Add an error alert and redirect if necessary.
     *
     * @param string $message
     * @param string $errorKey
     * @param string $url
     * @return void
     */
    public function redirectError(string $message, string $errorKey, string $url = null): void
    {
        if ($url) {
            $this->alerts->flashAlert(AlertService::TYPE_ERROR, $message, $errorKey, $url);
            return;
        }

        $this->alerts->addAlert(AlertService::TYPE_ERROR, $message, $errorKey);
    }
}
