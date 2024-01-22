<?php

namespace Cyberlpkf\Clicksign\Exceptions;

class InvalidEmail extends \Exception
{
    public static function create(): self
    {
        return new static('Endereço de e-mail inválido!');
    }
}