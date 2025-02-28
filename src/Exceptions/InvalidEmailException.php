<?php

namespace Macronlinebr\Clicksign\Exceptions;

class InvalidEmailException extends \Exception
{
    public static function create(): self
    {
        return new static('Endereço de e-mail inválido!');
    }
}