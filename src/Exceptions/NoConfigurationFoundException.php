<?php

namespace Macronlinebr\Clicksign\Exceptions;

class NoConfigurationFoundException extends \Exception
{
    public static function create(): self
    {
        return new static('Configuração de acesso a clicksign não encontrada!');
    }
}