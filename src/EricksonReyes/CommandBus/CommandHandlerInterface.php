<?php

namespace EricksonReyes\CommandBus;

/**
 * Interface CommandHandlerInterface
 * @package EricksonReyes\CommandBus
 */
interface CommandHandlerInterface
{

    /**
     * Returns the fully qualified class name of the command it is assigned to handle.
     *
     * @return string
     */
    public function assignedCommand(): string;

    /**
     * Assigns a fully qualified command class name to this handler.
     *
     * @param string $commandName
     * @return void
     */
    public function assignCommand(string $commandName): void;
}
