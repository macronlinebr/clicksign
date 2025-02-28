<?php

namespace Macronlinebr\Clicksign\Exceptions;

class InvalidNotificationUrlConfiguration extends \Exception
{
    public static function create(): self
    {
        return new static('NotificationUrl inválida!');
    }
}