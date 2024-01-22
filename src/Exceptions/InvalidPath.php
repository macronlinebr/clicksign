<?php

namespace Cyberlpkf\Clicksign\Exceptions;

class InvalidPath extends \Exception
{
    public static function create(): self
    {
        return new static('Path não informado!');
    }
}