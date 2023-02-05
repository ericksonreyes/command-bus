<?php

namespace EricksonReyes\CommandBus;

use EricksonReyes\CommandBus\Exception\MissingHandleThisMethodException;
use EricksonReyes\CommandBus\Exception\NoAssignedCommandHandlerForCommandException;
use EricksonReyes\CommandBus\Trait\CommandBusExceptionHandlingTrait;
use EricksonReyes\CommandBus\Trait\CommandBusLoggingTrait;
use Exception;

/**
 * Class CommandBus
 * @package EricksonReyes\CommandBus
 */
class CommandBus implements CommandBusInterface
{
    use CommandBusLoggingTrait, CommandBusExceptionHandlingTrait;

    /**
     * @var string[]
     */
    private array $acknowledgingHandlers = [];

    /**
     * @var \EricksonReyes\CommandBus\CommandHandlerInterface[]
     */
    private array $commandHandlers = [];


    /**
     * Assigns a command to a handler.
     *
     * @param string $commandName Fully qualified class name of a command.
     * @param CommandHandlerInterface $commandHandler Command handler instance.
     * @return void
     *
     * @throws \Exception
     */
    public function assignCommandToHandler(string $commandName, CommandHandlerInterface $commandHandler): void
    {
        $commandHandlerName = get_class($commandHandler);

        $this->logThis(
            CommandBusLogType::Information,
            sprintf(
                'Assigning %s command to %s command handler.',
                $commandName,
                $commandHandlerName
            )
        );

        if (method_exists($commandHandler, 'handleThis') === false) {
            $logMessage = sprintf(
                'The command handler %s is missing a required handleThis($command) method.',
                $commandHandlerName
            );

            $this->logThis(
                CommandBusLogType::Exception,
                $logMessage
            );

            throw new MissingHandleThisMethodException(
                $logMessage
            );
        }

        $this->commandHandlers[$commandName] = $commandHandler;

        $this->logThis(
            CommandBusLogType::Information,
            sprintf(
                '%s command was assigned to %s command handler.',
                $commandName,
                $commandHandlerName
            )
        );
    }

    /**
     * Returns an array of registered command handlers.
     *
     * @return \EricksonReyes\CommandBus\CommandHandlerInterface[]
     */
    public function commandHandlers(): array
    {
        return $this->commandHandlers;
    }


    /**
     * Sends a command to its designated handler.
     *
     * @param mixed $command Instance of a command.
     * @return void
     *
     * @throws \Exception
     */
    public function handleThis(mixed $command): void
    {
        $commandClassName = get_class($command);


        $this->logThis(
            CommandBusLogType::Information,
            sprintf(
                '%s command was received.',
                $commandClassName
            )
        );

        if (array_key_exists($commandClassName, $this->commandHandlers()) === false) {
            $logMessage = sprintf(
                '%s command was received.',
                $commandClassName
            );

            $this->logThis(
                CommandBusLogType::Exception,
                $logMessage
            );

            throw new NoAssignedCommandHandlerForCommandException(
                $logMessage
            );
        }

        $commandHandler = $this->commandHandlers()[$commandClassName];
        $this->resetExceptions();
        $this->resetAcknowledgingHandlers();

        try {
            $this->logThis(
                CommandBusLogType::Information,
                sprintf(
                    'Sending command to %s command handler.',
                    $commandHandler::class
                )
            );
            $commandHandler->handleThis($command);
        } catch (Exception $exception) {
            $this->logThis(
                CommandBusLogType::Exception,
                sprintf(
                    'Exception encountered: %s',
                    $exception->getMessage()
                )
            );

            $this->recordCaughtException($exception);
            if ($this->throwsCaughtExceptions()) {
                throw new $exception;
            }
        } finally {
            $this->logThis(
                CommandBusLogType::Information,
                sprintf(
                    'Command was handled by %s command handler.',
                    $commandHandler::class
                )
            );
            $this->recordAcknowledgingHandler($commandHandler);
        }
    }


    /**
     * Returns a collection of fully qualified class names of handlers who handled the recently accepted command.
     *
     * @return string[]
     */
    public function acknowledgingHandlers(): array
    {
        return $this->acknowledgingHandlers;
    }

    /**
     * Resets acknowledging handlers
     *
     * @return void
     */
    private function resetAcknowledgingHandlers(): void
    {
        $this->acknowledgingHandlers = [];
    }

    /**
     * Records acknowledging handler
     *
     * @param \EricksonReyes\CommandBus\CommandHandlerInterface $commandHandler
     * @return void
     */
    private function recordAcknowledgingHandler(CommandHandlerInterface $commandHandler): void
    {
        $this->acknowledgingHandlers[] = $commandHandler::class;
    }
}
