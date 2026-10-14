@extends('layouts.blog')

@section('title', $post->title)
@section('description', $post->excerpt)

@section('breadcrumbs')
    <nav aria-label="Ruta de navegación" class="mb-6 text-sm">
        <ol class="flex flex-wrap gap-2 text-muted">
            <li><a href="{{ route('home') }}" class="link">Inicio</a></li>
            <li aria-hidden="true">›</li>
            <li><a href="{{ route('categories.show', $post->category) }}" class="link">{{ $post->category->name }}</a></li>
            <li aria-hidden="true">›</li>
            <li aria-current="page" class="truncate">{{ \Illuminate\Support\Str::limit($post->title, 50) }}</li>
        </ol>
    </nav>
@endsection

@section('content')
    <article>
        <header class="mb-6">
            <p class="mb-3"><a href="{{ route('categories.show', $post->category) }}" class="badge {{ $post->category->badge_classes }}">{{ $post->category->name }}</a></p>
            <h1 class="mb-3 font-serif text-4xl font-bold leading-tight text-ink">{{ $post->title }}</h1>
            <p class="text-lg text-ink">{{ $post->excerpt }}</p>
            <p class="mt-3 text-sm text-muted">
                Por {{ $post->author->name }} ·
                <time datetime="{{ $post->published_at->toDateString() }}">{{ $post->published_at->translatedFormat('d \d\e F \d\e Y') }}</time>
                · {{ $post->reading_time }} min de lectura · {{ number_format($post->views) }} lecturas
            </p>
        </header>

        @if ($post->image_url)
            <figure class="mb-6">
                <img src="{{ $post->image_url }}" alt="{{ $post->image_alt }}" width="1200" height="675"
                     class="aspect-video w-full rounded-xl object-cover" fetchpriority="high">
            </figure>
        @endif

        <div class="prose-news">
            @foreach (preg_split('/\R{2,}/', trim($post->body)) as $parrafo)
                <p>{!! nl2br(e($parrafo)) !!}</p>
            @endforeach
        </div>
    </article>

    <section id="comentarios" aria-labelledby="titulo-comentarios" class="mt-12">
        <h2 id="titulo-comentarios" class="mb-4 font-serif text-2xl font-bold text-brand">
            Comentarios ({{ $comentarios->count() }})
        </h2>

        @forelse ($comentarios as $c)
            <article class="card mb-3 p-4">
                <h3 class="font-semibold">{{ $c->name }}
                    <span class="text-sm font-normal text-muted">· <time datetime="{{ $c->created_at->toDateString() }}">{{ $c->created_at->diffForHumans() }}</time></span>
                </h3>
                <p class="mt-1 whitespace-pre-line text-ink">{{ $c->body }}</p>
            </article>
        @empty
            <p class="mb-4 text-ink">Sé la primera persona en comentar.</p>
        @endforelse

        <form action="{{ route('comments.store', $post) }}" method="POST" class="card mt-6 space-y-4 p-5" novalidate>
            @csrf
            <h3 class="font-serif text-xl font-bold">Deja tu comentario</h3>
            <p class="hint">Los comentarios se publican después de ser revisados. Los campos con * son obligatorios.</p>

            @if ($errors->any())
                <div role="alert" class="rounded-lg border-l-4 border-red-700 bg-red-50 p-3 font-medium text-red-900">
                    Revisa los campos marcados: hay {{ $errors->count() }} {{ $errors->count() === 1 ? 'error' : 'errores' }}.
                </div>
            @endif

            @foreach ([['name', 'Nombre *', 'text', 'name'], ['email', 'Correo electrónico * (no se publica)', 'email', 'email']] as [$campo, $etiqueta, $tipo, $auto])
                <div>
                    <label for="c-{{ $campo }}" class="label">{{ $etiqueta }}</label>
                    <input id="c-{{ $campo }}" name="{{ $campo }}" type="{{ $tipo }}" value="{{ old($campo) }}" autocomplete="{{ $auto }}"
                           required class="field @error($campo) border-red-700 @enderror"
                           @error($campo) aria-invalid="true" aria-describedby="e-{{ $campo }}" @enderror>
                    @error($campo) <p id="e-{{ $campo }}" class="error-text">⚠ {{ $message }}</p> @enderror
                </div>
            @endforeach

            <div>
                <label for="c-body" class="label">Comentario *</label>
                <textarea id="c-body" name="body" rows="4" maxlength="1000" required class="field @error('body') border-red-700 @enderror"
                          @error('body') aria-invalid="true" aria-describedby="e-body" @enderror>{{ old('body') }}</textarea>
                @error('body') <p id="e-body" class="error-text">⚠ {{ $message }}</p> @enderror
            </div>

            <button type="submit" class="btn btn-primary">Enviar comentario</button>
        </form>
    </section>

    @if ($relacionadas->isNotEmpty())
        <section aria-labelledby="relacionadas" class="mt-12">
            <h2 id="relacionadas" class="mb-4 font-serif text-2xl font-bold text-brand">Noticias relacionadas</h2>
            <div class="grid gap-6 sm:grid-cols-2">
                @foreach ($relacionadas->take(2) as $rel)
                    @include('blog._card', ['post' => $rel->setRelation('category', $post->category)])
                @endforeach
            </div>
        </section>
    @endif
@endsection
