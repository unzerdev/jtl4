CREATE TABLE xplugin_s360_unzer_shop4_config (
    `key` VARCHAR(255) NOT NULL PRIMARY KEY,
    `value` VARCHAR(255) NOT NULL
);

CREATE TABLE xplugin_s360_unzer_shop4_order (
    `jtl_order_id` INT(10) NOT NULL,    /* jtl order table id (kBestellung) */
    `jtl_order_number` VARCHAR(255),    /* jtl order number (cBestellNr) */
    `invoice_id` VARCHAR(255),          /* JTL WaWi Invoice Id */
    `payment_id` VARCHAR(255),          /* payment id from heidelpay */
    `transaction_unique_id` VARCHAR(255),/* unique transaction id from heidelpay */
    `payment_state` VARCHAR(255),       /* payment state from heidelpay (cached) */
    `payment_type_name` VARCHAR(255),   /* payment type name from heidelpay */
    `payment_type_id` VARCHAR(255),     /* payment type id from heidelpay */
    PRIMARY KEY (`jtl_order_id`, `payment_id`)
);

CREATE TABLE xplugin_s360_unzer_shop4_charge (
    `id` INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `order_id`  INT(10) NOT NULL,    /* jtl order table id (kBestellung) */
    `charge_id` VARCHAR(255),        /* charge id from heidelpay */
    `payment_id` VARCHAR(255)       /* payment id from heidelpay */
);
