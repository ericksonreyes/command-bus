<?php

namespace EricksonReyes\CommandBus;

/**
 * Interface CommandBusLoggerAwareInterface
 * @package EricksonReyes\CommandBus
 */
interface CommandBusLoggerAwareInterface
{
    /**
     * Registers a (or another) command bus logger.
     *
     * @param \EricksonReyes\CommandBus\CommandBusLoggerInterface $commandBusLogger
     * @return void
     */
    public function registerCommandBusLogger(CommandBusLoggerInterface $commandBusLogger): void;

    /**
     * Returns an array of command bus loggers.
     *
     * @return \EricksonReyes\CommandBus\CommandBusLoggerInterface[]
     */
    public function commandBusLoggers(): array;
}
