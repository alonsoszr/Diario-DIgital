<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') | Panel · {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <x-accesibilidad-init />
</head>
<body class="flex min-h-screen flex-col">
    <a href="#contenido" class="skip-link">Saltar al contenido principal</a>

    <header class="bg-blue-900 text-white">
        <div class="mx-auto flex max-w-6xl flex-wrap items-center justify-between gap-3 px-4 py-3">
            <p class="font-serif text-xl font-bold">Panel · Diario Digital</p>
            <div class="flex flex-wrap items-center gap-3 text-sm">
                <x-theme-toggle />
                <span>Sesión: {{ auth()->user()->name }}</span>
                <a href="{{ route('home') }}" class="underline hover:text-amber-200">Ver el blog</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="rounded bg-white px-3 py-1 font-semibold text-blue-900 hover:bg-amber-100">Cerrar sesión</button>
                </form>
            </div>
        </div>
        <nav aria-label="Administración" class="bg-blue-950">
            <ul class="mx-auto flex max-w-6xl flex-wrap px-2">
                @foreach ([
                    ['admin.dashboard', 'Resumen', 'admin.dashboard'],
                    ['admin.posts.index', 'Noticias', 'admin.posts.*'],
                    ['admin.categories.index', 'Categorías', 'admin.categories.*'],
                    ['admin.comments.index', 'Comentarios', 'admin.comments.*'],
                ] as [$ruta, $texto, $patron])
                    <li><a href="{{ route($ruta) }}" @if (request()->routeIs($patron)) aria-current="page" @endif
                           class="block px-4 py-3 font-semibold hover:bg-blue-800 {{ request()->routeIs($patron) ? 'bg-blue-800 underline underline-offset-8' : '' }}">{{ $texto }}</a></li>
                @endforeach
            </ul>
        </nav>
    </header>

    <main id="contenido" tabindex="-1" class="mx-auto w-full max-w-6xl flex-1 px-4 py-8">
        @if (session('status'))
            <div role="status" aria-live="polite" class="mb-6 rounded-lg border-l-4 border-green-700 bg-green-50 p-4 font-medium text-green-900">
                <span aria-hidden="true">✔ </span>{{ session('status') }}
            </div>
        @endif
        @if (session('error'))
            <div role="alert" class="mb-6 rounded-lg border-l-4 border-red-700 bg-red-50 p-4 font-medium text-red-900">
                <span aria-hidden="true">⚠ </span>{{ session('error') }}
            </div>
        @endif

        @yield('content')
    </main>
    <x-accesibilidad />
</body>
</html>
