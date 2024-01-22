<?php

namespace Cyberlpkf\Clicksign;

use Illuminate\Support\Facades\Facade;

class ClicksignFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() : string
    {
        return 'clicksign';
    }
}
