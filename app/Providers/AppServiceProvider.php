<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Paginator::defaultView('vendor.pagination.blog');

        //datos de la barra lateral en caché (eficiencia); se invalidan al cambiar noticias o categorías
        View::composer('layouts.blog', function ($view) {
            $view->with('menuCategorias', Cache::remember('sidebar.categorias', 600, fn () => Category::withCount([
                'posts as published_count' => fn ($q) => $q->published(),
            ])->orderBy('name')->get()));

            $view->with('masLeidas', Cache::remember('sidebar.mas_leidas', 600, fn () => Post::published()
                ->orderByDesc('views')->limit(5)->get(['id', 'title', 'slug', 'views'])));
        });

        $limpiar = function () {
            Cache::forget('sidebar.categorias');
            Cache::forget('sidebar.mas_leidas');
        };
        Post::saved($limpiar);
        Post::deleted($limpiar);
        Category::saved($limpiar);
        Category::deleted($limpiar);
    }
}
