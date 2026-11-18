<?php

namespace App\Providers;

use App\Models\Categoria;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\View as BladeView;

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
        View::composer('components.layouts.home', function (BladeView $view): void {
            $view->with('categorias', Categoria::query()->orderBy('nombre')->get(['id', 'nombre']));
        });
    }
}
