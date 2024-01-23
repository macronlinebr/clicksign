<?php

namespace Cyberlpkf\Clicksign\Exceptions;

class InvalidSignerUrlConfigurationException extends \Exception
{
    public static function create(): self
    {
        return new static('SignerUrl inválida!');
    }
}