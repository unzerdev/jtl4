<?php declare(strict_types = 1);

namespace Plugin\s360_heidelpay_shop4\Controllers\Admin;

use Plugin\s360_heidelpay_shop4\Controllers\Controller;
use Plugin\s360_heidelpay_shop4\Foundation\Plugin;
use Plugin\s360_heidelpay_shop4\Utils\JtlLinkHelper;

/**
 * Abstract Admin Controller
 *
 * @package Plugin\s360_heidelpay_shop4\Controllers\Admin
 */
abstract class AdminController extends Controller
{
    /**
     * @var array Errors to show to the user.
     */
    protected $errors;

    /**
     * @var array warnings to show to the user.
     */
    protected $warnings;

    /**
     * @var array Messages to show to the user.
     */
    protected $messages;

    /**
     * @var array Success Messages to show to the user.
     */
    protected $successes;

    /**
     * @SuppressWarnings(PHPMD.Superglobals)
     * @param Plugin $plugin
     */
    public function __construct(Plugin $plugin)
    {
        parent::__construct($plugin);

        $this->errors = [];
        $this->warnings = [];
        $this->messages = [];
        $this->successes = [];
    }

    /**
     * Prepare variable which are passed to the view.
     *
     * @return void
     */
    protected function prepare(): void
    {
        /** @var JtlLinkHelper $linkHelper */
        $linkHelper = $this->plugin->getContainer()->make(JtlLinkHelper::class);

        $data = [
            'adminTemplatePath' => $this->plugin->path(Plugin::PATH_ADMIN_MENU) . 'template/',
            'adminTemplateUrl'  => $this->plugin->path(Plugin::PATH_ADMIN_MENU_URL_SSL) . 'template/',
            'adminMenuUrl'      => $this->plugin->path(Plugin::PATH_ADMIN_MENU_URL_SSL),
            'adminUrl'          => $linkHelper->getFullAdminUrl(),
            'pluginVersion'     => (string) $this->plugin->getShopPlugin()->getCurrentVersion()
        ];

        $this->smarty->assign('hpAdmin', $data);
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

        $template = $this->plugin->path(Plugin::PATH_ADMIN_MENU) . $template . '.tpl';
        $custom = $this->plugin->path(Plugin::PATH_ADMIN_MENU) . $template . '_custom.tpl';

        if (file_exists($custom)) {
            $template = $custom;
        }

        $this->smarty->assign('hpErrors', $this->errors);
        $this->smarty->assign('hpWarnings', $this->warnings);
        $this->smarty->assign('hpSuccesses', $this->successes);
        $this->smarty->assign('hpMessages', $this->messages);

        return $this->smarty->fetch($template);
    }

    /**
     * Add an error to display to the user.
     *
     * @param string $error
     * @param string $postfix
     * @return void
     */
    protected function addError(string $error, string $postfix = ''): void
    {
        $this->errors[] = $error . $postfix;
    }

    /**
     * Add a warning to display to the user.
     *
     * @param string $warning
     * @return void
     */
    protected function addWarning(string $warning): void
    {
        if (!\in_array($warning, $this->warnings, true)) {
            $this->warnings[] = $warning;
        }
    }

    /**
     * Add a success to display to the user.
     *
     * @param string $success
     * @return void
     */
    protected function addSuccess(string $success): void
    {
        if (!\in_array($success, $this->successes, true)) {
            $this->successes[] = $success;
        }
    }

    /**
     * Add a message to display to the user.
     *
     * @param string $message
     * @return void
     */
    protected function addMessage(string $message): void
    {
        if (!\in_array($message, $this->messages, true)) {
            $this->messages[] = $message;
        }
    }
}
