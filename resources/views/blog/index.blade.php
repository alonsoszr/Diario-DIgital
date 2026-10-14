@extends('layouts.blog')

@section('title', 'Últimas noticias')

@section('content')
    <h1 class="sr-only">Últimas noticias</h1>

    @if ($destacada)
        <article class="card mb-8" aria-labelledby="destacada-titulo">
            @if ($destacada->image_url)
                <img src="{{ $destacada->image_url }}" alt="{{ $destacada->image_alt }}" width="1200" height="675"
                     class="aspect-video w-full object-cover" fetchpriority="high">
            @endif
            <div class="p-6">
                <p class="mb-2 flex items-center gap-3">
                    <span class="badge bg-amber-100 text-amber-900 ring-amber-700">★ Destacada</span>
                    <a href="{{ route('categories.show', $destacada->category) }}" class="badge {{ $destacada->category->badge_classes }}">{{ $destacada->category->name }}</a>
                </p>
                <h2 id="destacada-titulo" class="mb-3 font-serif text-3xl font-bold leading-tight">
                    <a href="{{ route('posts.show', $destacada) }}" class="text-ink hover:text-brand hover:underline">{{ $destacada->title }}</a>
                </h2>
                <p class="mb-4 text-lg text-ink">{{ $destacada->excerpt }}</p>
                <a href="{{ route('posts.show', $destacada) }}" class="btn btn-primary">Leer la noticia<span class="sr-only">: {{ $destacada->title }}</span></a>
            </div>
        </article>
    @endif

    @if ($posts->isEmpty() && ! $destacada)
        <p class="card p-6 text-lg">Todavía no hay noticias publicadas. Vuelve pronto.</p>
    @endif

    <h2 class="mb-4 font-serif text-2xl font-bold text-brand">Más noticias</h2>
    <div class="grid gap-6 sm:grid-cols-2">
        @foreach ($posts as $post)
            @include('blog._card', ['post' => $post])
        @endforeach
    </div>

    <div class="mt-8">{{ $posts->links() }}</div>
@endsection
