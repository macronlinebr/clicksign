<?php

namespace Macronlinebr\Clicksign\Exceptions;

class InvalidDocumentUrlConfigurationException extends \Exception
{
    public static function create(): self
    {
        return new static('DocumentUrl inválida!');
    }
}