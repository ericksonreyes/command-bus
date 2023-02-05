<?php

namespace EricksonReyes\CommandBus;

/**
 * Class CommandBusLogType
 * @package EricksonReyes\CommandBus
 */
enum CommandBusLogType
{
    case Information;

    case Warning;

    case Exception;
}
