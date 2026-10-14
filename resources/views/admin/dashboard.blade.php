@extends('layouts.admin')

@section('title', 'Resumen')

@section('content')
    <h1 class="mb-6 font-serif text-3xl font-bold text-brand">Resumen del blog</h1>

    <dl class="mb-10 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        @foreach ([
            ['Noticias publicadas', $totales['publicadas']],
            ['Borradores', $totales['borradores']],
            ['Comentarios pendientes', $totales['pendientes']],
            ['Lecturas totales', number_format($totales['vistas'])],
        ] as [$etiqueta, $valor])
            <div class="card p-5">
                <dt class="text-sm font-semibold text-muted">{{ $etiqueta }}</dt>
                <dd class="mt-1 text-4xl font-bold text-brand">{{ $valor }}</dd>
            </div>
        @endforeach
    </dl>

    <div class="mb-8 flex flex-wrap gap-3">
        <a href="{{ route('admin.posts.create') }}" class="btn btn-primary">+ Nueva noticia</a>
        <a href="{{ route('admin.comments.index') }}" class="btn btn-secondary">Moderar comentarios</a>
    </div>

    <section aria-labelledby="top5">
        <h2 id="top5" class="mb-3 font-serif text-2xl font-bold text-brand">Las 5 noticias más leídas</h2>
        <div class="card overflow-x-auto">
            <table class="w-full text-left">
                <caption class="sr-only">Noticias ordenadas por número de lecturas</caption>
                <thead class="bg-subtle text-sm">
                    <tr><th scope="col" class="p-3">Título</th><th scope="col" class="p-3">Categoría</th><th scope="col" class="p-3 text-right">Lecturas</th></tr>
                </thead>
                <tbody>
                    @foreach ($masLeidas as $p)
                        <tr class="border-t border-line">
                            <td class="p-3">{{ $p->title }}</td>
                            <td class="p-3">{{ $p->category->name }}</td>
                            <td class="p-3 text-right font-semibold">{{ number_format($p->views) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
@endsection
