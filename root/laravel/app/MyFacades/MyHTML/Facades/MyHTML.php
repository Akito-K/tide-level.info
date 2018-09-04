<?php

namespace MyFacade\Facades;

use Illuminate\Support\Facades\Facade;

class MyHTMLFacade extends Facade
{

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'myhtml';
    }
}