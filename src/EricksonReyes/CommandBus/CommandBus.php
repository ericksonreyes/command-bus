<?php

namespace EricksonReyes\CommandBus;

use EricksonReyes\CommandBus\Exception\MissingHandleThisMethodException;
use EricksonReyes\CommandBus\Exception\NoAssignedCommandHandlerForCommandException;
use Exception;

/**
 * Class CommandBus
 * @package EricksonReyes\CommandBus
 */
class CommandBus implements CommandBusInterface
{
    /**
     * @var string[]
     */
    private array $acknowledgingHandlers = [];

    /**
     * @var \EricksonReyes\CommandBus\CommandHandlerInterface[]
     */
    private array $commandHandlers = [];

    /**
     * @var bool
     */
    private bool $suppressExceptionsThrown = false;

    /**
     * @var \Exception[]
     */
    private array $exceptionsCaught = [];

    /**
     * @var \EricksonReyes\CommandBus\CommandBusLoggerInterface[]
     */
    private array $commandBusLoggers = [];

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
     * Instructs the command bus to ignore or suppress caught exceptions during command handling.
     *
     * @return void
     */
    public function suppressExceptionThrown(): void
    {
        $this->logThis(
            CommandBusLogType::Warning,
            'Exception throwing was disabled.'
        );
        $this->suppressExceptionsThrown = true;
    }

    /**
     * Instructs the command bus not to ignore or suppress caught exceptions during command handling.
     *
     * @return void
     */
    public function throwExceptionsCaught(): void
    {
        $this->logThis(
            CommandBusLogType::Warning,
            'Exception throwing was enabled.'
        );
        $this->suppressExceptionsThrown = false;
    }

    /**
     * Returns true when all exceptions caught are being ignored or suppressed.
     *
     * @return bool
     */
    public function suppressesCaughtExceptions(): bool
    {
        return $this->suppressExceptionsThrown;
    }

    /**
     * Returns true when all exceptions caught are being thrown.
     *
     * @return bool
     */
    public function throwsCaughtExceptions(): bool
    {
        return $this->suppressExceptionsThrown === false;
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

        $this->exceptionsCaught = [];
        $this->acknowledgingHandlers = [];
        $commandHandler = $this->commandHandlers()[$commandClassName];

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

            $this->exceptionsCaught[] = $exception;
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
            $this->acknowledgingHandlers[] = $commandHandler::class;
        }
    }

    /**
     * Returns a collection of exceptions caught while the shouldIgnoreOrphanedCommands() method is in effect.
     *
     * @return \Exception[]
     */
    public function suppressedExceptions(): array
    {
        return $this->exceptionsCaught;
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
