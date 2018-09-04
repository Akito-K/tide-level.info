<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class MyFacadeServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('myfunctions', function($app) {
            return new \MyFacade\MyFunctionsFacade;
        }
        );

        $this->app->bind('myhtml', function($app) {
            return new \MyFacade\MyHTMLFacade;
        }
        );

        $this->app->bind('myform', function($app) {
            return new \MyFacade\MyFormFacade;
        }
        );

        $this->app->bind('mymail', function($app) {
            return new \MyFacade\MyMailFacade;
        }
        );

        $this->app->bind('qreki', function($app) {
            return new \MyFacade\QrekiFacade;
        }
        );
        //
    }
}
