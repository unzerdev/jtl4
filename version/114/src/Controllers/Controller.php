<?php declare(strict_types = 1);

namespace Plugin\s360_heidelpay_shop4\Controllers;

use JTLSmarty;
use Plugin\s360_heidelpay_shop4\Foundation\Plugin;
use Plugin\s360_heidelpay_shop4\Utils\JtlLoggerTrait;
use Shop;

/**
 * Abstract Controller
 *
 * @package Plugin\s360_heidelpay_shop4\Controllers
 */
abstract class Controller
{
    use JtlLoggerTrait;

    /**
     * @var array
     */
    protected $request;

    /**
     * @var Plugin
     */
    protected $plugin;

    /**
     * @var JTLSmarty
     */
    protected $smarty;

    /**
     * @SuppressWarnings(PHPMD.Superglobals)
     * @param Plugin $plugin
     */
    public function __construct(Plugin $plugin)
    {
        $this->plugin = $plugin;
        $this->smarty = Shop::Smarty();
        $this->request = $_REQUEST;

        $this->prepare();
    }

    /**
     * Expeected to fill smarty variables and return template.
     *
     * @return string
     */
    abstract public function handle(): string;

    /**
     * Prepare variable which are passed to the view.
     *
     * @return void
     */
    protected function prepare(): void
    {
    }

    /**
     * Render a template view.
     *
     * @param string $template
     * @param array $data
     * @return string
     */
    protected function view(string $template, array $data = []): string
    {
        foreach ($data as $key => $value) {
            $this->smarty->assign($key, $value);
        }

        $tpl = $this->plugin->path(Plugin::PATH_FRONTEND) . $template . '.tpl';
        $custom = $this->plugin->path(Plugin::PATH_FRONTEND) . $template . '_custom.tpl';

        if (file_exists($custom)) {
            $tpl = $custom;
        }

        return $this->smarty->fetch($tpl);
    }

    /**
     * Set plugin value.
     *
     * @param Plugin $plugin
     * @return self
     */
    public function setPlugin(Plugin $plugin): self
    {
        $this->plugin = $plugin;
        return $this;
    }

    /**
     * Get plugin value.
     *
     * @return Plugin
     */
    public function getPlugin(): Plugin
    {
        return $this->plugin;
    }

    /**
     * Set request value.
     *
     * @param array $request
     * @return self
     */
    public function setRequest(array $request): self
    {
        $this->request = $request;
        return $this;
    }

    /**
     * Get request value.
     *
     * @return array
     */
    public function getRequest(): array
    {
        return $this->request;
    }
}
