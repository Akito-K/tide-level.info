<?php

namespace MyFacade\Facades;

use Illuminate\Support\Facades\Facade;

class QrekiFacade extends Facade
{

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'qreki';
    }
}