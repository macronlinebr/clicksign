<?php

namespace Macronlinebr\Clicksign\Exceptions;

class IntegrationNotEnabledException extends \Exception
{
    public static function create(): self
    {
        return new static('Integração com a Clicksign não habilitada!');
    }
}