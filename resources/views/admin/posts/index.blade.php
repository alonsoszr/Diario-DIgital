@extends('layouts.admin')

@section('title', 'Noticias')

@section('content')
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <h1 class="font-serif text-3xl font-bold text-brand">Noticias</h1>
        <a href="{{ route('admin.posts.create') }}" class="btn btn-primary">+ Nueva noticia</a>
    </div>

    <div class="card overflow-x-auto">
        <table class="w-full text-left">
            <caption class="sr-only">Listado de noticias</caption>
            <thead class="bg-subtle text-sm">
                <tr>
                    <th scope="col" class="p-3">Título</th>
                    <th scope="col" class="p-3">Categoría</th>
                    <th scope="col" class="p-3">Estado</th>
                    <th scope="col" class="p-3 text-right">Lecturas</th>
                    <th scope="col" class="p-3"><span class="sr-only">Acciones</span></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($posts as $post)
                    <tr class="border-t border-line align-top">
                        <td class="p-3 font-medium">{{ $post->title }}</td>
                        <td class="p-3">{{ $post->category->name }}</td>
                        <td class="p-3">
                            @if ($post->status === 'published')
                                <span class="badge bg-green-100 text-green-900 ring-green-700">✔ Publicado</span>
                            @else
                                <span class="badge bg-slate-200 text-slate-900 ring-slate-600">✎ Borrador</span>
                            @endif
                        </td>
                        <td class="p-3 text-right">{{ number_format($post->views) }}</td>
                        <td class="p-3">
                            <div class="flex flex-wrap justify-end gap-2">
                                <a href="{{ route('admin.posts.edit', $post) }}" class="btn btn-secondary">Editar<span class="sr-only"> {{ $post->title }}</span></a>
                                <form method="POST" action="{{ route('admin.posts.destroy', $post) }}"
                                      onsubmit="return confirm('¿Eliminar esta noticia? Esta acción no se puede deshacer.')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-danger">Eliminar<span class="sr-only"> {{ $post->title }}</span></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="p-6 text-center">Aún no hay noticias. Crea la primera.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-6">{{ $posts->links() }}</div>
@endsection
