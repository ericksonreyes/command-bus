<?php

namespace EricksonReyes\CommandBus\Trait;

use EricksonReyes\CommandBus\CommandBusLogType;
use Exception;

/**
 * Trait CommandBusExceptionHandlingTrait
 * @package EricksonReyes\CommandBus\Trait
 */
trait CommandBusExceptionHandlingTrait
{
    /**
     * @var bool
     */
    private bool $suppressExceptionsThrown = false;

    /**
     * @var \Exception[]
     */
    private array $exceptionsCaught = [];

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
     * Returns a collection of exceptions caught while the shouldIgnoreOrphanedCommands() method is in effect.
     *
     * @return \Exception[]
     */
    public function suppressedExceptions(): array
    {
        return $this->exceptionsCaught;
    }

    /**
     * Resets exceptions caught array.
     *
     * @return void
     */
    private function resetExceptions(): void
    {
        $this->exceptionsCaught = [];
    }

    /**
     * @param \Exception $exception
     * @return void
     */
    private function recordCaughtException(Exception $exception)
    {
        $this->exceptionsCaught[] = $exception;
    }
}
