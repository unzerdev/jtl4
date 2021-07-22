<?php declare(strict_types=1);

namespace Plugin\s360_heidelpay_shop4\Payments\Traits;

use Plugin\s360_heidelpay_shop4\Payments\Interfaces\NotificationInterface;

/**
 * Trait HasState
 * @package Plugin\s360_heidelpay_shop4\Payments\Traits
 */
trait HasState
{
    /**
     * @var int Current State.
     */
    protected $state = NotificationInterface::STATE_VALID;

    /**
     * Get state.
     *
     * @return int
     */
    public function getState(): int
    {
        return $this->state;
    }

    /**
     * Set state.
     *
     * @param int $status
     * @return void
     */
    public function setState(int $status): void
    {
        $this->state = $status;
    }
}
