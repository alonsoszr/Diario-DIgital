<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="@yield('description', 'Diario Digital: noticias de política, economía, deportes, tecnología, cultura y salud.')">
    <title>@yield('title') | {{ config('app.name') }}</title>
    <link rel="icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32'%3E%3Crect width='32' height='32' rx='6' fill='%231e40af'/%3E%3Ctext x='16' y='23' font-size='20' font-family='Georgia' text-anchor='middle' fill='white'%3ED%3C/text%3E%3C/svg%3E">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <x-accesibilidad-init />
</head>
<body class="flex min-h-screen flex-col">
    <a href="#contenido" class="skip-link">Saltar al contenido principal</a>

    <header class="border-b-4 border-blue-800 bg-surface">
        <div class="mx-auto flex max-w-6xl flex-wrap items-center justify-between gap-4 px-4 py-4">
            <a href="{{ route('home') }}" class="font-serif text-3xl font-bold tracking-tight text-brand">
                Diario<span class="text-amber-700 dark:text-amber-300"> Digital</span>
            </a>

            <form action="{{ route('search') }}" method="GET" role="search" class="flex w-full gap-2 sm:w-auto">
                <label for="buscador" class="sr-only">Buscar noticias</label>
                <input id="buscador" type="search" name="q" value="{{ request('q') }}" maxlength="100"
                       placeholder="Buscar noticias…" class="field sm:w-64">
                <button type="submit" class="btn btn-primary">Buscar</button>
            </form>
        </div>

        <nav aria-label="Categorías" class="bg-blue-900">
            <ul class="mx-auto flex max-w-6xl flex-wrap gap-x-1 px-2">
                <li><a href="{{ route('home') }}" @if (request()->routeIs('home')) aria-current="page" @endif
                       class="block px-3 py-3 font-semibold text-white hover:bg-blue-800 {{ request()->routeIs('home') ? 'underline underline-offset-8' : '' }}">Inicio</a></li>
                @foreach ($menuCategorias as $cat)
                    @php $actual = request()->routeIs('categories.show') && request()->route('category')?->id === $cat->id; @endphp
                    <li><a href="{{ route('categories.show', $cat) }}" @if ($actual) aria-current="page" @endif
                           class="block px-3 py-3 font-semibold text-white hover:bg-blue-800 {{ $actual ? 'underline underline-offset-8' : '' }}">{{ $cat->name }}</a></li>
                @endforeach
                <li class="ml-auto flex items-center gap-1">
                    <x-theme-toggle />
                    @auth
                        <a href="{{ route('admin.dashboard') }}" class="block px-3 py-3 font-semibold text-amber-200 hover:bg-blue-800">Panel</a>
                    @else
                        <a href="{{ route('login') }}" class="block px-3 py-3 font-semibold text-amber-200 hover:bg-blue-800">Ingresar</a>
                    @endauth
                </li>
            </ul>
        </nav>
    </header>

    <div class="mx-auto w-full max-w-6xl flex-1 px-4 py-8">
        @yield('breadcrumbs')

        @if (session('status'))
            <div role="status" aria-live="polite" class="mb-6 rounded-lg border-l-4 border-green-700 bg-green-50 p-4 font-medium text-green-900">
                <span aria-hidden="true">✔ </span>{{ session('status') }}
            </div>
        @endif

        <div class="grid gap-8 lg:grid-cols-3">
            <main id="contenido" tabindex="-1" class="lg:col-span-2">
                @yield('content')
            </main>

            <aside aria-label="Barra lateral" class="space-y-6">
                <section aria-labelledby="mas-leidas" class="card p-5">
                    <h2 id="mas-leidas" class="mb-3 font-serif text-xl font-bold text-brand">Más leídas</h2>
                    <ol class="space-y-3">
                        @forelse ($masLeidas as $i => $item)
                            <li class="flex gap-3">
                                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-amber-100 font-bold text-amber-900" aria-hidden="true">{{ $i + 1 }}</span>
                                <a href="{{ route('posts.show', $item) }}" class="link no-underline hover:underline">
                                    {{ $item->title }}
                                    <span class="block text-sm font-normal text-muted">{{ number_format($item->views) }} lecturas</span>
                                </a>
                            </li>
                        @empty
                            <li class="text-muted">Aún no hay noticias leídas.</li>
                        @endforelse
                    </ol>
                </section>

                <section aria-labelledby="lista-categorias" class="card p-5">
                    <h2 id="lista-categorias" class="mb-3 font-serif text-xl font-bold text-brand">Categorías</h2>
                    <ul class="space-y-2">
                        @foreach ($menuCategorias as $cat)
                            <li class="flex items-center justify-between">
                                <a href="{{ route('categories.show', $cat) }}" class="link">{{ $cat->name }}</a>
                                <span class="text-sm text-muted">{{ $cat->published_count }} <span class="sr-only">noticias</span></span>
                            </li>
                        @endforeach
                    </ul>
                </section>
            </aside>
        </div>
    </div>

    <footer class="bg-slate-900 text-slate-200">
        <div class="mx-auto max-w-6xl px-4 py-6 text-sm">
            <p>© {{ date('Y') }} Diario Digital — Blog de noticias autoadministrable. Proyecto académico de Pruebas y Calidad de Software.</p>
        </div>
    </footer>
    <x-accesibilidad />
</body>
</html>
