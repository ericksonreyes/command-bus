<?php

namespace EricksonReyes\CommandBus;

use Exception;

/**
 * Interface CommandBusLoggerInterface
 * @package EricksonReyes\CommandBus
 */
interface CommandBusLoggerInterface
{

    /**
     * Logs a message or exception.
     *
     * @param \EricksonReyes\CommandBus\CommandBusLogType $logType
     * @param string|\Exception $message
     * @return void
     */
    public function log(CommandBusLogType $logType, string|Exception $message): void;
}
