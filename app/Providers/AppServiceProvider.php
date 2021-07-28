<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

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
            Schema::defaultStringLength(191);

        Validator::extend('filter', function($attribute, $value, $params) {
            foreach ($params as $word) {
                if (stripos($value, $word) !== false) {
                    return false;
                }
            }
            return true;

        }, 'Some words are not allowed!');

        Paginator::useBootstrap();
        //Paginator::defaultView('pagination');
    }
}
