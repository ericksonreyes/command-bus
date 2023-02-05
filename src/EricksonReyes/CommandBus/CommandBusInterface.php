<?php

namespace EricksonReyes\CommandBus;

/**
 * Interface CommandBusInterface
 * @package EricksonReyes\CommandBus
 */
interface CommandBusInterface extends CommandBusLoggerAwareInterface
{

    /**
     * Assigns a command to a handler.
     *
     * @param string $commandName Fully qualified class name of a command.
     * @param CommandHandlerInterface $commandHandler Command handler instance.
     * @return void
     *
     * @throws \Exception
     */
    public function assignCommandToHandler(string $commandName, CommandHandlerInterface $commandHandler): void;

    /**
     * Returns an array of registered command handlers.
     *
     * @return \EricksonReyes\CommandBus\CommandHandlerInterface[]
     */
    public function commandHandlers(): array;

    /**
     * Instructs the command bus to ignore commands with no assigned handler instance and do not throw any exceptions.
     *
     * @return void
     */
    public function suppressExceptionThrown(): void;

    /**
     * Instructs the command bus to throw an exception when there is no handler instance assigned to a command to be
     * handled.
     *
     * @return void
     */
    public function throwExceptionsCaught(): void;

    /**
     * Sends a command to its designated handler.
     *
     * @param mixed $command Instance of a command.
     * @return void
     *
     * @throws \Exception
     */
    public function handleThis(mixed $command): void;

    /**
     * Returns a collection of exceptions caught while the shouldIgnoreOrphanedCommands() method is in effect.
     *
     * @return \Exception[]
     */
    public function suppressedExceptions(): array;

    /**
     * Returns a collection of fully qualified class names of handlers who handled the recently accepted command.
     *
     * @return string[]
     */
    public function acknowledgingHandlers(): array;

    /**
     * Returns true when all exceptions caught are being ignored or suppressed.
     *
     * @return bool
     */
    public function suppressesCaughtExceptions(): bool;

    /**
     * Returns true when all exceptions caught are being thrown.
     *
     * @return bool
     */
    public function throwsCaughtExceptions(): bool;
}
