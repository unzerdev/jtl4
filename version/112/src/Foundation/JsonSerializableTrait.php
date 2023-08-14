<?php
declare(strict_types = 1);

namespace Plugin\s360_heidelpay_shop4\Foundation;

/**
 * Json Serializable Trait
 * @package Plugin\s360_heidelpay_shop4\Foundation
 */
trait JsonSerializableTrait
{
    /**
     * Serialize struct to json.
     *
     * @return array
     */
    public function jsonSerialize(): array
    {
        $vars = get_object_vars($this);
        $this->convertDateTimePropertiesToJsonStringRepresentation($vars);

        return utf8_convert_recursive($vars);
    }

    /**
     * Convert Date Time Properties to match JSON String Representation.
     *
     * @param array $array
     * @return void
     */
    protected function convertDateTimePropertiesToJsonStringRepresentation(array &$array): void
    {
        foreach ($array as &$value) {
            if ($value instanceof \DateTimeInterface) {
                $value = $value->format(\DateTime::RFC3339_EXTENDED);
            }
        }
    }
}
