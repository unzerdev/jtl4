<?php declare(strict_types = 1);

namespace Plugin\s360_heidelpay_shop4\Payments\Traits;

use Shop;
use Plugin\s360_heidelpay_shop4\Foundation\Plugin;
use Plugin\s360_heidelpay_shop4\Utils\Container;
use UnzerSDK\Resources\Metadata;

/**
 * Add Meta Data to transactions
 *
 * @package Plugin\s360_heidelpay_shop4\Payments\Traits
 */
trait HasMetadata
{
    /**
     * Create Metadata object
     *
     * @return Metadata
     */
    public function createMetadata(): Metadata
    {
        /** @var Plugin $plugin */
        $plugin = Container::getInstance()->make(Plugin::class);
        $shopPlugin = $plugin->getShopPlugin();
        $shopVersion = sprintf('%.2f', Shop::getShopVersion() / 100);
        $strMinorVersion = JTL_MINOR_VERSION;
        if ($strMinorVersion === '#JTL_MINOR_VERSION#') {
            $strMinorVersion = 'DEV';
        }

        return (new Metadata())
            ->setShopType('JTL')
            ->setShopVersion($shopVersion . '.' . $strMinorVersion)
            ->addMetadata('pluginType', 'unzerdev/jtl4')
            ->addMetadata('pluginVersion', (string) $shopPlugin->getCurrentVersion())
            ->addMetadata('PHP', phpversion());
    }
}
