<?php

namespace Cyberlpkf\Clicksign\Exceptions;

class InvalidKeyException extends \Exception
{
    public static function create(): self
    {
        return new static('Chave do documento inválida!');
    }
}