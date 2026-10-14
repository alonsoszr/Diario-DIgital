@extends('layouts.blog')

@section('title', 'Resultados de búsqueda')

@section('breadcrumbs')
    <nav aria-label="Ruta de navegación" class="mb-6 text-sm">
        <ol class="flex flex-wrap gap-2 text-muted">
            <li><a href="{{ route('home') }}" class="link">Inicio</a></li>
            <li aria-hidden="true">›</li>
            <li aria-current="page">Búsqueda</li>
        </ol>
    </nav>
@endsection

@section('content')
    <h1 class="mb-4 font-serif text-3xl font-bold text-brand">Resultados de búsqueda</h1>

    @if ($q === '')
        <p class="card p-6 text-lg">Escribe una palabra en el buscador para encontrar noticias.</p>
    @else
        <p role="status" aria-live="polite" class="mb-6 text-lg">
            {{ $posts->total() }} {{ $posts->total() === 1 ? 'resultado' : 'resultados' }} para «<strong>{{ $q }}</strong>».
        </p>

        @if ($posts->isEmpty())
            <div class="card p-6">
                <p class="mb-3 text-lg">No encontramos noticias con esa palabra. Prueba con otro término o revisa la ortografía.</p>
                <a href="{{ route('home') }}" class="btn btn-secondary">Volver al inicio</a>
            </div>
        @else
            <div class="grid gap-6 sm:grid-cols-2">
                @foreach ($posts as $post)
                    @include('blog._card', ['post' => $post])
                @endforeach
            </div>
            <div class="mt-8">{{ $posts->links() }}</div>
        @endif
    @endif
@endsection
