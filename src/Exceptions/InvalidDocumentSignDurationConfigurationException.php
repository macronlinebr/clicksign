<?php

namespace Cyberlpkf\Clicksign\Exceptions;

class InvalidDocumentSignDurationConfigurationException extends \Exception
{
    public static function create(): self
    {
        return new static('DocumentSignDuration deve ser maior que zero!');
    }
}