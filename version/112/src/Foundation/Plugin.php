<?php
declare(strict_types = 1);

namespace Plugin\s360_heidelpay_shop4\Foundation;

use InvalidArgumentException;
use Plugin as ShopPlugin;
use Plugin\s360_heidelpay_shop4\Charges\ChargeHandler;
use Plugin\s360_heidelpay_shop4\Charges\ChargeMappingModel;
use Plugin\s360_heidelpay_shop4\Orders\OrderMappingModel;
use Plugin\s360_heidelpay_shop4\Payments\HeidelpayApiAdapter;
use Plugin\s360_heidelpay_shop4\Payments\PaymentHandler;
use Plugin\s360_heidelpay_shop4\Payments\PaymentMethodModuleFactory;
use Plugin\s360_heidelpay_shop4\Utils\AlertService;
use Plugin\s360_heidelpay_shop4\Utils\Config;
use Plugin\s360_heidelpay_shop4\Utils\Container;
use Plugin\s360_heidelpay_shop4\Utils\JtlLinkHelper;
use Plugin\s360_heidelpay_shop4\Utils\SessionHelper;
use Shop;

/**
 * Plugin foundation.
 *
 * Gives easy access to plugin helpers like config or session.
 * Provides access to plugin settings and translations.
 *
 * @package Plugin\s360_heidelpay_shop4\Foundation
 */
class Plugin
{
    public const PLUGIN_ID = 's360_unzer_shop4';
    public const HIP_URL = 'https://insights.unzer.com/merchant/{merchantId}/order/{id}';
    public const HIP_URL_SANDBOX = 'https://sbx-insights.unzer.com/merchant/{merchantId}/order/{id}';

    // Paths
    public const PATH_TEXT_LICENSE = 'cTextLicensePath';
    public const PATH_TEXT_README = 'cTextReadmePath';
    public const PATH_PLUGIN = 'cPluginPfad';
    public const PATH_PLUGIN_UNINSTALL = 'cPluginUninstallPfad';
    public const PATH_ADMIN_MENU = 'cAdminmenuPfad';
    public const PATH_ADMIN_MENU_URL = 'cAdminmenuPfadURL';
    public const PATH_ADMIN_MENU_URL_SSL = 'cAdminmenuPfadURLSSL';
    public const PATH_FRONTEND = 'cFrontendPfad';
    public const PATH_FRONTEND_URL = 'cFrontendPfadURL';
    public const PATH_FRONTEND_URL_SSL = 'cFrontendPfadURLSSL';
    public const PATH_LICENCE = 'cLicencePfad';
    public const PATH_LICENCE_URL = 'cLicencePfadURL';
    public const PATH_LICENCE_URL_SSL = 'cLicencePfadURLSSL';
    private const PATHS = [
        self::PATH_TEXT_LICENSE,
        self::PATH_TEXT_README,
        self::PATH_PLUGIN,
        self::PATH_PLUGIN_UNINSTALL,
        self::PATH_ADMIN_MENU,
        self::PATH_ADMIN_MENU_URL,
        self::PATH_ADMIN_MENU_URL_SSL,
        self::PATH_FRONTEND,
        self::PATH_FRONTEND_URL,
        self::PATH_FRONTEND_URL_SSL,
        self::PATH_LICENCE,
        self::PATH_LICENCE_URL,
        self::PATH_LICENCE_URL_SSL
    ];

    /**
     * @var Config
     */
    private $config;

    /**
     * @var SessionHelper
     */
    private $session;

    /**
     * @param Config $config
     * @param SessionHelper $sessionHelper
     */
    public function __construct(Config $config, SessionHelper $session)
    {
        $this->config = $config;
        $this->session = $session;
    }

    /**
     * Register Services in the DI Container.
     *
     * @return void
     */
    public function register(): void
    {
        $this->getContainer()->addSingleton(Plugin::class, function () {
            return $this;
        });

        $this->getContainer()->addSingleton(HeidelpayApiAdapter::class, function () {
            return new HeidelpayApiAdapter(
                $this,
                $this->getContainer()->make(JtlLinkHelper::class)
            );
        });

        $this->getContainer()->addSingleton(ChargeHandler::class, function () {
            return new ChargeHandler($this->getContainer()->make(ChargeMappingModel::class));
        });

        $this->getContainer()->addSingleton(PaymentHandler::class, function () {
            return new PaymentHandler(
                $this,
                $this->getContainer()->make(HeidelpayApiAdapter::class),
                $this->getContainer()->make(ChargeHandler::class),
                $this->getContainer()->make(OrderMappingModel::class)
            );
        });

        $this->getContainer()->addSingleton(PaymentMethodModuleFactory::class, function () {
            return new PaymentMethodModuleFactory((int) $this->getShopPlugin()->kPlugin);
        });

        $this->getContainer()->addSingleton(JtlLinkHelper::class, function () {
            return new JtlLinkHelper((int) $this->getShopPlugin()->kPlugin);
        });

        $this->getContainer()->addSingleton(AlertService::class, function () {
            return new AlertService();
        });

        $this->getContainer()->addSingleton(OrderMappingModel::class, function () {
            return new OrderMappingModel(Shop::getInstance()->DB());
        });

        $this->getContainer()->addSingleton(ChargeMappingModel::class, function () {
            return new ChargeMappingModel(Shop::getInstance()->DB());
        });

        $this->session->setAlertService($this->getContainer()->make(AlertService::class));
    }

    /**
     * Get the shop plugin class.
     *
     * @return ShopPlugin
     */
    public function getShopPlugin(): ShopPlugin
    {
        return ShopPlugin::getPluginById(self::PLUGIN_ID);
    }

    /**
     * Get the container instance.
     *
     * @return Container
     */
    public function getContainer(): Container
    {
        return Container::getInstance();
    }

    /**
     * Get the plugin config instance.
     *
     * @return Config
     */
    public function getConfig(): Config
    {
        return $this->config;
    }

    /**
     * Get the Plugin Session Helper.
     *
     * @return SessionHelper
     */
    public function getSession(): SessionHelper
    {
        return $this->session;
    }

    /**
     * Get a plugin path/url.
     *
     * @throws InvalidArgumentException if path property does not exist.
     * @param string $path
     * @return string
     */
    public function path(string $path): string
    {
        $plugin = $this->getShopPlugin();
        if (!in_array($path, self::PATHS) || !property_exists($plugin, $path)) {
            throw new InvalidArgumentException('Could not find path for key: ' . $path);
        }

        return $plugin->{$path};
    }

    /**
     * Get the translation of a lang var by its key.
     *
     * @param string $key
     * @return string
     */
    public function trans(string $key): string
    {
        return $this->getShopPlugin()->oPluginSprachvariableAssoc_arr[$key] ?? $key;
    }

    /**
     * Get a payment setting.
     *
     * @param string $key
     * @param string $moduleId
     * @return string|null
     */
    public function getPaymentSetting(string $key, string $moduleId): ?string
    {
        $setting = $moduleId . '_' . $key;

        return array_key_exists($setting, $this->getShopPlugin()->oPluginEinstellungAssoc_arr)
            ? $this->getShopPlugin()->oPluginEinstellungAssoc_arr[$setting]
            : null;
    }

    /**
     * Get Insight Portal URL if merchant id is configured
     *
     * @param string|null $uid
     * @return string|null
     */
    public function getInsightPortalUrl(?string $uid): ?string
    {
        $merchantId = $this->getConfig()->get(Config::MERCHANT_ID);

        if (!empty($merchantId) && !empty($uid)) {
            return str_replace(
                ['{merchantId}', '{id}'],
                [$merchantId, $uid],
                $this->isSandbox() ? self::HIP_URL_SANDBOX : self::HIP_URL
            );
        }

        return null;
    }

    /**
     * Check if api is in sandbox mode based on api keys
     *
     * @return boolean
     */
    public function isSandbox(): bool
    {
        $priv = $this->getConfig()->get(Config::PRIVATE_KEY);
        $pub = $this->getConfig()->get(Config::PUBLIC_KEY);

        return substr($priv, 0, 1) === 's' && substr($pub, 0, 1) === 's';
    }
}
