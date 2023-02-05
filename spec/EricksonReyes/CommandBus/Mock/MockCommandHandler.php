<?php

namespace spec\EricksonReyes\CommandBus\Mock;

use EricksonReyes\CommandBus\CommandHandlerInterface;

/**
 * Class MockCommandHandler
 * @package spec\EricksonReyes\CommandBus
 */
class MockCommandHandler implements CommandHandlerInterface
{
    /**
     * @var string
     */
    private string $assignedCommand = '';

    /**
     * Assigns a fully qualified command class name to this handler.
     *
     * @param string $commandName
     * @return void
     */
    public function assignCommand(string $commandName): void
    {
        $this->assignedCommand = $commandName;
    }


    /**
     * Returns the fully qualified class name of the command it is assigned to handle.
     *
     * @return string
     */
    public function assignedCommand(): string
    {
        return $this->assignedCommand;
    }

    /**
     * @param \spec\EricksonReyes\CommandBus\Mock\MockCommand $mockCommand
     * @return void
     */
    public function handleThis(MockCommand $mockCommand): void
    {
    }
}
