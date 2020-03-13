<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\tag;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Using view composer to set following variables globally
        view()->composer('*',function($view) {
            $view->with('tags', Tag::orderBy('tag', 'desc')->get());
        });
    }
}
