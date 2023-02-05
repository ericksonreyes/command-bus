<?php

namespace spec\EricksonReyes\CommandBus\Mock;

use EricksonReyes\CommandBus\CommandHandlerInterface;

/**
 * Class ExceptionThrowingMockCommandHandler
 * @package spec\EricksonReyes\CommandBus\Mock
 */
class ExceptionThrowingMockCommandHandler implements CommandHandlerInterface
{
    /**
     * @var string
     */
    private string $assignedCommand = '';

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
     * @param \spec\EricksonReyes\CommandBus\Mock\MockCommand $command
     * @return void
     */
    public function handleThis(MockCommand $command): void
    {
        throw new \RuntimeException(
            sprintf(
                'This is a mock exception thrown during handling of %s',
                $command::class
            )
        );
    }
}
