<?php
declare(strict_types = 1);

namespace Plugin\s360_heidelpay_shop4\Utils;

use Jtllog;

/**
 * JTL Logger Trait
 * @package Plugin\s360_heidelpay_shop4\Utils
 */
trait JtlLoggerTrait
{
    /**
     * Write a log message as debug.
     *
     * @param mixed $message
     * @param string $context
     * @return void
     */
    public function debugLog($message, string $context = ''): void
    {
        $this->writeLog($message, JTLLOG_LEVEL_DEBUG, $context);
    }

    /**
     * Write a log message as notice.
     *
     * @param mixed $message
     * @param string $context
     * @return void
     */
    public function noticeLog($message, string $context = ''): void
    {
        $this->writeLog($message, JTLLOG_LEVEL_NOTICE, $context);
    }

    /**
     * Write a log message as error.
     *
     * @param mixed $message
     * @param string $context
     * @return void
     */
    public function errorLog($message, string $context = ''): void
    {
        $this->writeLog($message, JTLLOG_LEVEL_ERROR, $context);
    }

    /**
     * Write log entry
     *
     * @param mixed $message
     * @param integer $level
     * @param string $context
     * @return void
     */
    private function writeLog($message, int $level, string $context = ''): void
    {
        if ($context !== '') {
            $context .= ': ';
        }

        if (\is_array($message)) {
            foreach ($message as $msg) {
                Jtllog::writeLog('[Unzer] ' . $context . $msg, $level);
            }

            return;
        }

        if (\is_string($message)) {
            Jtllog::writeLog('[Unzer] ' . $context . $message, $level);
            return;
        }

        Jtllog::writeLog('[Unzer] ' . $context . print_r($message, true), $level);
    }
}
