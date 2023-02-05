<?php

namespace EricksonReyes\CommandBus\Trait;

use EricksonReyes\CommandBus\CommandBusLoggerInterface;
use EricksonReyes\CommandBus\CommandBusLogType;

/**
 * Trait CommandBusLoggingTrait
 * @package EricksonReyes\CommandBus\Trait
 */
trait CommandBusLoggingTrait
{
    /**
     * @var \EricksonReyes\CommandBus\CommandBusLoggerInterface[]
     */
    private array $commandBusLoggers = [];

    /**
     * Registers a (or another) command bus logger.
     *
     * @param \EricksonReyes\CommandBus\CommandBusLoggerInterface $commandBusLogger
     * @return void
     */
    public function registerCommandBusLogger(CommandBusLoggerInterface $commandBusLogger): void
    {
        $this->commandBusLoggers[] = $commandBusLogger;
    }

    /**
     * Returns an array of command bus loggers.
     *
     * @return \EricksonReyes\CommandBus\CommandBusLoggerInterface[]
     */
    public function commandBusLoggers(): array
    {
        return $this->commandBusLoggers;
    }


    /**
     * @param \EricksonReyes\CommandBus\CommandBusLogType $logType
     * @param string|\Exception $message
     * @return void
     */
    private function logThis(CommandBusLogType $logType, string|Exception $message): void
    {
        foreach ($this->commandBusLoggers() as $commandBusLogger) {
            $commandBusLogger->log($logType, $message);
        }
    }
}
