<?php

namespace EricksonReyes\CommandBus;

/**
 * Interface ChainedCommandHandlerInterface
 * @package EricksonReyes\CommandBus
 */
interface ChainedCommandHandlerInterface extends CommandHandlerInterface
{
    /**
     * Send the command to the next handler after the command is handled.
     *
     * @param \EricksonReyes\CommandBus\CommandHandlerInterface $nextCommandHandler
     * @return void
     */
    public function assignNextHandler(CommandHandlerInterface $nextCommandHandler): void;

    /**
     * Returns true if there is a handler to be called after the command is handled.
     * @return bool
     */
    public function hasANextHandler(): bool;
}
