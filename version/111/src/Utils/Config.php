<?php
declare(strict_types = 1);

namespace Plugin\s360_heidelpay_shop4\Utils;

use NiceDB;
use Shop;

/**
 * Config Class
 *
 * @package Plugin\s360_heidelpay_shop4\Utils
 */
class Config
{
    protected const TABLE = 'xplugin_s360_unzer_shop4_config';
    public const PLUGIN_SESSION = 's360_heidelpay';

    // Lang Var Keys
    public const LANG_INVALID_TOKEN = 's360_hp_invalid_form_token';
    public const LANG_PAYMENT_PROCESS_RUNTIME_EXCEPTION = 's360_hp_payment_process_runtime_exception';
    public const LANG_PAYMENT_PROCESS_EXCEPTION = 's360_hp_payment_process_exception';
    public const LANG_SEPA_MANDATE = 's360_hp_sepa_mandate';
    public const LANG_REDIRECTING = 's360_hp_redirecting';
    public const LANG_CONFIRM_INSTALLMENT_TITLE = 's360_hp_confirm_instalment_title';
    public const LANG_DOWNLOAD_AND_CONFIRM_INSTALLMENT_PLAN = 's360_hp_confirm_download_instalment_plan';
    public const LANG_TOTAL_PURCHASE_AMOUNT = 's360_hp_total_purchase_amount';
    public const LANG_TOTAL_INTEREST_AMOUNT = 's360_hp_total_interest_amount';
    public const LANG_TOTAL_AMOUNT = 's360_hp_total_amount';
    public const LANG_DOWNLOAD_YOUR_PLAN = 's360_hp_download_your_plan';
    public const LANG_CLOSE_MODAL = 's360_hp_close_modal';
    public const LANG_CONFIRMATION_CHECKSUM = 's360_hp_confirmation_checksum';

    // Config Keys
    public const PRIVATE_KEY = 'privateKey';
    public const PUBLIC_KEY = 'publicKey';
    public const MERCHANT_ID = 'merchantId';
    public const FONT_SIZE = 'fontSize';
    public const FONT_COLOR = 'fontColor';
    public const FONT_FAMILY = 'fontFamily';
    public const SELECTOR_SUBMIT_BTN = 'selectorSubmitButton';
    public const PQ_SELECTOR_CHANGE_PAYMENT_METHOD = 'pqSelectorChangePaymentMethod';
    public const PQ_METHOD_CHANGE_PAYMENT_METHOD = 'pqMethodChangePaymentMethod';
    public const PQ_SELECTOR_ERRORS = 'pqSelectorErrors';
    public const PQ_METHOD_ERRORS = 'pqMethodErrors';
    public const PQ_SELECTOR_REVIEW_STEP = 'pqSelectorReviewStep';
    public const PQ_METHOD_REVIEW_STEP = 'pqMethodReviewStep';
    public const PQ_SELECTOR_PAYMENT_INFORMATION = 'pqSelectorPaymentInformation';
    public const PQ_METHOD_PAYMENT_INFORMATION = 'pqMethodPaymentInformation';

    /**
     * @var NiceDB
     */
    protected $database;

    /**
     * @var array
     */
    protected $data = [];

    /**
     * Load Config.
     */
    public function __construct()
    {
        $this->database = Shop::DB();
        $this->load();
    }

    /**
     * Load Config Data.
     *
     * @return self
     */
    public function load(): self
    {
        $this->data = array_column($this->database->query('SELECT * FROM ' . self::TABLE, 9), 'value', 'key');
        return $this;
    }

    /**
     * Get a config entry.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        return $this->data[$key] ?? $default;
    }

    /**
     * Set a new value for a config entry.
     *
     * @param string $key
     * @param mixed $value
     * @return self
     */
    public function set(string $key, $value): self
    {
        $this->data[$key] = $value;
        return $this;
    }

    /**
     * Check if a config entry exists.
     *
     * @return bool
     */
    public function has($key): bool
    {
        return array_key_exists($key, $this->data);
    }

    /**
     * Get all config values.
     *
     * @return array
     */
    public function all(): array
    {
        return $this->data;
    }

    /**
     * Save config to the database.
     *
     * @return void
     */
    public function save(): void
    {
        foreach ($this->data as $key => $value) {
            $this->database->executeQueryPrepared(
                'INSERT INTO ' . self::TABLE . '(`key`, `value`)
                VALUES (:key, :value)
                ON DUPLICATE KEY UPDATE `value` = :value',
                ['key' => $key, 'value' => $value],
                3
            );
        }
    }
}
