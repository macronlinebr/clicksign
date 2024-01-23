<?php

namespace Cyberlpkf\Clicksign\Exceptions;

class NoApiSetException extends \Exception
{
    public static function create(): self
    {
        return new static('api_id não informada! Utilize o método setApiId antes de efetuar a requisição para que a configuração correta seja carregada!');
    }
}