<?php

namespace Cyberlpkf\Clicksign\Exceptions;

class InvalidPathException extends \Exception
{
    public static function create(): self
    {
        return new static('Path não informado!');
    }
}