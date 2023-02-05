<?php

namespace spec\EricksonReyes\CommandBus;

use EricksonReyes\CommandBus\CommandBus;
use EricksonReyes\CommandBus\CommandBusInterface;
use EricksonReyes\CommandBus\CommandBusLoggerInterface;
use EricksonReyes\CommandBus\CommandBusLogType;
use EricksonReyes\CommandBus\CommandHandlerInterface;
use EricksonReyes\CommandBus\Exception\MissingHandleThisMethodException;
use EricksonReyes\CommandBus\Exception\NoAssignedCommandHandlerForCommandException;
use Exception;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use spec\EricksonReyes\CommandBus\Mock\ExceptionThrowingMockCommandHandler;
use spec\EricksonReyes\CommandBus\Mock\MockChainedCommandHandler;
use spec\EricksonReyes\CommandBus\Mock\MockCommand;
use spec\EricksonReyes\CommandBus\Mock\MockCommandHandler;

/**
 * Class CommandBusSpec
 * @package spec\EricksonReyes\CommandBus
 */
class CommandBusSpec extends ObjectBehavior
{
    /**
     * @return void
     */
    public function it_can_be_initialized(): void
    {
        $this->shouldHaveType(CommandBus::class);
        $this->shouldImplement(CommandBusInterface::class);
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function it_accepts_a_command_and_assign_it_to_a_handler(): void
    {
        $command = new MockCommand();
        $commandHandler = new MockCommandHandler();

        $this->assignCommandToHandler(commandName: $command::class, commandHandler: $commandHandler)->shouldBeNull();
        $this->commandHandlers()->shouldHaveKey($command::class);
    }

    /**
     * @return void
     */
    public function it_requires_a_command_handler_to_have_a_handleThis_method(): void
    {
        $command = new MockCommand();
        $commandHandler = new class implements CommandHandlerInterface {
            /**
             * Returns the fully qualified class name of the command it is assigned to handle.
             *
             * @return string
             */
            public function assignedCommand(): string
            {
                return '';
            }

            /**
             * Assigns a fully qualified command class name to this handler.
             *
             * @param string $commandName
             * @return void
             */
            public function assignCommand(string $commandName): void
            {
            }

        };

        $this->shouldThrow(MissingHandleThisMethodException::class)->during(
            method: 'assignCommandToHandler',
            arguments: [
                $command::class,
                $commandHandler
            ]
        );
    }

    /**
     * @param \spec\EricksonReyes\CommandBus\Mock\MockCommand $command
     * @return void
     */
    public function it_rejects_commands_with_no_assigned_handlers(MockCommand $command): void
    {
        $this->shouldThrow(NoAssignedCommandHandlerForCommandException::class)->during(
            'handleThis',
            [
                $command
            ]
        );
    }

    /**
     * @param \spec\EricksonReyes\CommandBus\Mock\MockCommandHandler $commandHandler
     * @return void
     * @throws \Exception
     */
    public function it_handles_commands(MockCommandHandler $commandHandler): void
    {
        $command = new MockCommand();
        $this->assignCommandToHandler($command::class, $commandHandler);

        $commandHandler->handleThis($command)->shouldBeCalled();
        $this->handleThis($command);
    }

    /**
     * @param \spec\EricksonReyes\CommandBus\Mock\MockCommandHandler $commandHandler
     * @return void
     * @throws \Exception
     */
    public function it_returns_acknowledging_handlers(): void
    {
        $command = new MockCommand();
        $commandHandler = new MockCommandHandler();

        $this->assignCommandToHandler($command::class, $commandHandler);
        $this->handleThis($command);

        $this->acknowledgingHandlers()->shouldContain($commandHandler::class);
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function it_can_suppress_exceptions_during_handling_of_commands(): void
    {
        $command = new MockCommand();
        $handler = new ExceptionThrowingMockCommandHandler();

        $this->assignCommandToHandler($command::class, $handler);

        $this->suppressExceptionThrown()->shouldBeNull();
        $this->suppressesCaughtExceptions()->shouldReturn(true);

        $this->shouldNotThrow(Exception::class)->during(
            'handleThis',
            [
                $command
            ]
        );
    }

    /**
     * @throws \PhpSpec\Exception\Exception
     */
    public function it_returns_the_suppressed_exceptions_caught(): void
    {
        $command = new MockCommand();
        $handler = new ExceptionThrowingMockCommandHandler();

        $this->assignCommandToHandler($command::class, $handler);
        $this->suppressExceptionThrown();

        $this->handleThis($command);
        $this->suppressedExceptions()->shouldHaveCount(1);
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function it_can_toggle_exception_suppression(): void
    {
        $command = new MockCommand();
        $handler = new ExceptionThrowingMockCommandHandler();

        $this->assignCommandToHandler($command::class, $handler);
        $this->suppressExceptionThrown();

        $this->throwExceptionsCaught()->shouldBeNull();
        $this->suppressesCaughtExceptions()->shouldReturn(false);

        $this->shouldThrow(Exception::class)->during(
            'handleThis',
            [
                $command
            ]
        );
    }

    /**
     * @param \EricksonReyes\CommandBus\CommandBusLoggerInterface $commandBusLogger
     * @return void
     * @throws \Exception
     */
    public function it_can_have_loggers(CommandBusLoggerInterface $commandBusLogger): void
    {
        $command = new MockCommand();
        $commandHandler = new MockCommandHandler();

        $this->assignCommandToHandler($command::class, $commandHandler);

        $this->registerCommandBusLogger($commandBusLogger)->shouldBeNull();
        $this->commandBusLoggers()->shouldContain($commandBusLogger);
    }

    /**
     * @param \EricksonReyes\CommandBus\CommandBusLoggerInterface $commandBusLogger
     * @return void
     * @throws \Exception
     */
    public function it_can_log_messages(CommandBusLoggerInterface $commandBusLogger): void
    {
        $command = new MockCommand();
        $commandHandler = new MockCommandHandler();

        $this->assignCommandToHandler($command::class, $commandHandler);
        $this->registerCommandBusLogger($commandBusLogger);

        $commandBusLogger->log(
            Argument::type(CommandBusLogType::class),
            Argument::any()
        )->shouldBeCalled();

        $this->handleThis($command);
    }

    /**
     * @param \spec\EricksonReyes\CommandBus\Mock\MockChainedCommandHandler $secondCommandHandler
     * @return void
     * @throws \Exception
     */
    public function it_can_call_chained_command_handlers(
        MockChainedCommandHandler $secondCommandHandler
    ): void {
        $command = new MockCommand();
        $commandHandler = new MockChainedCommandHandler();

        $commandHandler->assignNextHandler($secondCommandHandler->getWrappedObject());
        $secondCommandHandler->handleThis($command)->shouldBeCalled();

        $this->assignCommandToHandler($command::class, $commandHandler);
        $this->handleThis($command);
    }
}
