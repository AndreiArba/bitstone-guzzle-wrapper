<?php

namespace Bitstone\GuzzleWrapper;

use Illuminate\Support\ServiceProvider;

class HttpServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Register any application services.
     *
     * @return void
     */

    public function register()
    {
        $this->app->bind('http', 'Bitstone\GuzzleWrapper\Dispatcher');
    }

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {

    }
}