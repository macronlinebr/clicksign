<?php

namespace Cyberlpkf\Clicksign\Exceptions;

class InvalidProductionUrlConfigurationException extends \Exception
{
    public static function create(): self
    {
        return new static('ProductionUrl inválida!');
    }
}