<?php

namespace Macronlinebr\Clicksign\Exceptions;

class InvalidSignerKeyException extends \Exception
{
    public static function create(): self
    {
        return new static('Chave do assinante inválida!');
    }
}