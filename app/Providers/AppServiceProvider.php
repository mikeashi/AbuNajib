<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Filament\Tables;
use Filament\Actions;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Tables\Actions\EditAction::configureUsing(function($action){
            return $action->slideOver();
        });

        Tables\Actions\CreateAction::configureUsing(function ($action) {
            return $action->slideOver();
        });


        Actions\CreateAction::configureUsing(function ($action) {
            return $action->slideOver();
        });


    }
}
