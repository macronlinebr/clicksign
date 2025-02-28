<?php

namespace Macronlinebr\Clicksign\Exceptions;

class NoFilialSetException extends \Exception
{
    public static function create(): self
    {
        return new static('filial_id não informada! Utilize o método setFilialId antes de efetuar a requisição para que a configuração correta seja carregada!');
    }
}