<?php

namespace Macronlinebr\Clicksign\Exceptions;

class InvalidDocumentKeyException extends \Exception
{
    public static function create(): self
    {
        return new static('Chave do documento inválida!');
    }
}