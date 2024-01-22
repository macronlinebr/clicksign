<?php

namespace Cyberlpkf\Clicksign\Exceptions;

class InvalidName extends \Exception
{
    public static function create(): self
    {
        return new static('Nome inválido!');
    }
}