@extends('layouts.blog')

@section('title', $category->name)

@section('breadcrumbs')
    <nav aria-label="Ruta de navegación" class="mb-6 text-sm">
        <ol class="flex flex-wrap gap-2 text-muted">
            <li><a href="{{ route('home') }}" class="link">Inicio</a></li>
            <li aria-hidden="true">›</li>
            <li aria-current="page">{{ $category->name }}</li>
        </ol>
    </nav>
@endsection

@section('content')
    <h1 class="mb-1 font-serif text-3xl font-bold text-brand">{{ $category->name }}</h1>
    @if ($category->description)
        <p class="mb-6 text-lg text-ink">{{ $category->description }}</p>
    @endif

    @if ($posts->isEmpty())
        <p class="card p-6 text-lg">No hay noticias publicadas en esta categoría por ahora.</p>
    @else
        <div class="grid gap-6 sm:grid-cols-2">
            @foreach ($posts as $post)
                @include('blog._card', ['post' => $post->setRelation('category', $category)])
            @endforeach
        </div>
        <div class="mt-8">{{ $posts->links() }}</div>
    @endif
@endsection
