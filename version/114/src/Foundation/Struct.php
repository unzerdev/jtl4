<?php
declare(strict_types = 1);

namespace Plugin\s360_heidelpay_shop4\Foundation;

use JsonSerializable;

/**
 * Abstract Struct Class
 * @package Plugin\s360_heidelpay_shop4\Foundation
 */
abstract class Struct implements JsonSerializable
{
    use JsonSerializableTrait;
}
