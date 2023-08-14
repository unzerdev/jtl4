<?php
declare(strict_types = 1);

namespace Plugin\s360_heidelpay_shop4\Utils;

use Session;

/**
 * Alert Service Class
 *
 * @package Plugin\s360_heidelpay_shop4\Utils
 */
class AlertService
{
    public const TYPE_ERROR = 'error';
    public const TYPE_NOTICE = 'notice';
    public const ALERT_SESSION = Config::PLUGIN_SESSION . '.alerts';

    /**
     * Check if there are alerts of a specific type.
     *
     * @param string $type
     * @return bool
     */
    public function hasAlerts(string $type): bool
    {
        return !empty(Session::get(self::ALERT_SESSION . '.' . $type));
    }

    /**
     * Add an alert.
     *
     * @param string $type
     * @param string $message
     * @param string $key
     * @return void
     */
    public function addAlert(string $type, string $message, string $key): void
    {
        Session::set(implode('.', [self::ALERT_SESSION, $type, $key]), $message);
    }

    /**
     * Add an alert and redirect to a new page.
     *
     * @SuppressWarnings(PHPMD.ExitExpression)
     * @param string $type
     * @param string $message
     * @param string $key
     * @param string $redirect
     * @return void
     */
    public function flashAlert(string $type, string $message, string $key, string $redirect): void
    {
        $this->addAlert($type, $message, $key);
        header('Location: ' . $redirect);
        exit;
    }

    /**
     * Get all alert messages for a type.
     *
     * @param string $type
     * @return array
     */
    public function all(string $type): array
    {
        return Session::get(self::ALERT_SESSION . '.' . $type, []);
    }

    /**
     * Delete a specific alert message.
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     * @param string $type
     * @param string $key
     * @return void
     */
    public function delete(string $type, string $key): void
    {
        unset($_SESSION[Config::PLUGIN_SESSION]['alerts'][$type][$key]);
    }

    /**
     * Delete all alert messages.
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     * @param string|null $type
     * @return void
     */
    public function clear(?string $type = null): void
    {
        if (!isset($_SESSION[Config::PLUGIN_SESSION]['alerts'])) {
            return;
        }

        if ($type && array_key_exists($type, $_SESSION[Config::PLUGIN_SESSION]['alerts'])) {
            unset($_SESSION[Config::PLUGIN_SESSION]['alerts'][$type]);
            return;
        }

        unset($_SESSION[Config::PLUGIN_SESSION]['alerts']);
    }
}
