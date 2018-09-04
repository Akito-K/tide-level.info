<?php

namespace MyFacade\Facades;

use Illuminate\Support\Facades\Facade;

class MyMailFacade extends Facade
{

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'mymail';
    }
}