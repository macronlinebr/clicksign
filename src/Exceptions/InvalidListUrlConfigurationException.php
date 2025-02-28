<?php

namespace Macronlinebr\Clicksign\Exceptions;

class InvalidListUrlConfigurationException extends \Exception
{
    public static function create(): self
    {
        return new static('ListUrl inválida!');
    }
}