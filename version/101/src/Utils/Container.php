<?php
declare(strict_types = 1);

namespace Plugin\s360_heidelpay_shop4\Utils;

use InvalidArgumentException;

/**
 * Simple Service Container
 *
 * @package Plugin\s360_heidelpay_shop4\Utils
 */
class Container
{
    private const TYPE_FACTORY = 'factory';
    private const TYPE_SINGLETON = 'singleton';

    /**
     * @var array Registered services
     */
    protected $container = [];

    /**
     * @var array Singleton Instances of registered services.
     */
    protected $instances = [];

    /**
     * @var self|null Container Instance
     */
    private static $instance = null;

    /**
     * Get Container Instance.
     *
     * @return self
     */
    public static function getInstance(): self
    {
        self::$instance = self::$instance ?? new static();
        return self::$instance;
    }

    /**
     * Add a new service
     *
     * @param string $service
     * @param callable $factory
     * @return self
     */
    public function add(string $service, callable $factory): self
    {
        $this->container[$service] = ['type' => self::TYPE_FACTORY, 'factory' => $factory];
        return $this;
    }

    /**
     * Add a new singleton service
     *
     * @param string $service
     * @param callable $singleton
     * @return self
     */
    public function addSingleton(string $service, callable $singleton): self
    {
        $this->container[$service] = ['type' => self::TYPE_SINGLETON, 'factory' => $singleton];
        return $this;
    }

    /**
     * Get an instance of a service.
     *
     * @throws InvalidArgumentException if the service could not be found in the container
     * @param string $service
     * @param array $params
     * @return mixed
     */
    public function make(string $service, array $params = [])
    {
        if (!array_key_exists($service, $this->container)) {
            throw new InvalidArgumentException($service . ' could not be found in the container');
        }

        $factory = $this->container[$service];

        if ($factory['type'] == self::TYPE_SINGLETON) {
            $this->instances[$service] = $this->instances[$service]
                ?? call_user_func_array($factory['factory'], $params);

            return $this->instances[$service];
        }

        return call_user_func_array($factory['factory'], $params);
    }
}
