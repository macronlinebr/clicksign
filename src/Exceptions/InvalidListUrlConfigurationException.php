<?php

namespace Cyberlpkf\Clicksign\Exceptions;

class InvalidListUrlConfigurationException extends \Exception
{
    public static function create(): self
    {
        return new static('ListUrl inválida!');
    }
}