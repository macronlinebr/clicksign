<?php

namespace Cyberlpkf\Clicksign\Exceptions;

class InvalidUpdateDocumentUrlConfigurationException extends \Exception
{
    public static function create(): self
    {
        return new static('UpdateDocumentUrl inválida!');
    }
}