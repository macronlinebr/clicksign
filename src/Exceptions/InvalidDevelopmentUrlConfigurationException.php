<?php

namespace Macronlinebr\Clicksign\Exceptions;

class InvalidDevelopmentUrlConfigurationException extends \Exception
{
    public static function create(): self
    {
        return new static('DevelopmentUrl inválida!');
    }
}