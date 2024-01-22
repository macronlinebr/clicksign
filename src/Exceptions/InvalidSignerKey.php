<?php

namespace Cyberlpkf\Clicksign\Exceptions;

class InvalidSignerKey extends \Exception
{
    public static function create(): self
    {
        return new static('Chave do assinante inválida!');
    }
}