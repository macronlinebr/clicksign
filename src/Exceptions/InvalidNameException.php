<?php

namespace Macronlinebr\Clicksign\Exceptions;

class InvalidNameException extends \Exception
{
    public static function create(): self
    {
        return new static('Nome inválido!');
    }
}