<?php

namespace Macronlinebr\Clicksign\Exceptions;

class NoAccessTokenException extends \Exception
{
    public static function create(): self
    {
        return new static('Chave de acesso não encontrada!');
    }
}