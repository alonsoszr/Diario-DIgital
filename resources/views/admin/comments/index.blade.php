@extends('layouts.admin')

@section('title', 'Comentarios')

@section('content')
    <h1 class="mb-6 font-serif text-3xl font-bold text-brand">Moderación de comentarios</h1>

    <section aria-labelledby="pendientes" class="mb-10">
        <h2 id="pendientes" class="mb-3 font-serif text-2xl font-bold">Pendientes ({{ $pendientes->count() }})</h2>
        @forelse ($pendientes as $c)
            <article class="card mb-3 p-4">
                <p class="font-semibold">{{ $c->name }} <span class="font-normal text-muted">&lt;{{ $c->email }}&gt;</span></p>
                <p class="text-sm text-muted">En: {{ $c->post->title }}</p>
                <p class="my-2 whitespace-pre-line">{{ $c->body }}</p>
                <div class="flex flex-wrap gap-2">
                    <form method="POST" action="{{ route('admin.comments.approve', $c) }}">
                        @csrf @method('PATCH')
                        <button type="submit" class="btn btn-primary">Aprobar<span class="sr-only"> comentario de {{ $c->name }}</span></button>
                    </form>
                    <form method="POST" action="{{ route('admin.comments.destroy', $c) }}" onsubmit="return confirm('¿Eliminar este comentario?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger">Eliminar<span class="sr-only"> comentario de {{ $c->name }}</span></button>
                    </form>
                </div>
            </article>
        @empty
            <p class="card p-5">No hay comentarios pendientes. ¡Todo al día!</p>
        @endforelse
    </section>

    <section aria-labelledby="aprobados">
        <h2 id="aprobados" class="mb-3 font-serif text-2xl font-bold">Aprobados</h2>
        @forelse ($aprobados as $c)
            <article class="card mb-3 flex flex-wrap items-start justify-between gap-3 p-4">
                <div>
                    <p class="font-semibold">{{ $c->name }} <span class="badge bg-green-100 text-green-900 ring-green-700">✔ Publicado</span></p>
                    <p class="text-sm text-muted">En: {{ $c->post->title }}</p>
                    <p class="mt-1 whitespace-pre-line">{{ $c->body }}</p>
                </div>
                <form method="POST" action="{{ route('admin.comments.destroy', $c) }}" onsubmit="return confirm('¿Eliminar este comentario?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger">Eliminar<span class="sr-only"> comentario de {{ $c->name }}</span></button>
                </form>
            </article>
        @empty
            <p class="card p-5">Todavía no hay comentarios aprobados.</p>
        @endforelse
        <div class="mt-4">{{ $aprobados->links() }}</div>
    </section>
@endsection
