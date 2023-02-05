<?php

namespace spec\EricksonReyes\CommandBus\Mock;

use EricksonReyes\CommandBus\ChainedCommandHandlerInterface;
use EricksonReyes\CommandBus\CommandHandlerInterface;

/**
 * Class MockChainedCommandHandler
 * @package spec\EricksonReyes\CommandBus\Mock
 */
class MockChainedCommandHandler implements ChainedCommandHandlerInterface
{
    /**
     * @var string
     */
    private string $assignedCommand = '';

    /**
     * @var \EricksonReyes\CommandBus\CommandHandlerInterface
     */
    private CommandHandlerInterface $nextHandler;

    /**
     * Send the command to the next handler after the command is handled.
     *
     * @param \EricksonReyes\CommandBus\CommandHandlerInterface $nextCommandHandler
     * @return void
     */
    public function assignNextHandler(CommandHandlerInterface $nextCommandHandler): void
    {
        $this->nextHandler = $nextCommandHandler;
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
     * Returns true if there is a handler to be called after the command is handled.
     * @return bool
     */
    public function hasANextHandler(): bool
    {
        return $this->nextHandler instanceof CommandHandlerInterface;
    }


    /**
     * @param \spec\EricksonReyes\CommandBus\Mock\MockCommand $command
     * @return void
     */
    public function handleThis(MockCommand $command): void
    {
        if ($this->hasANextHandler()) {
            $this->nextHandler->handleThis($command);
        }
    }


}