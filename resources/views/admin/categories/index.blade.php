@extends('layouts.admin')

@section('title', 'Categorías')

@section('content')
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <h1 class="font-serif text-3xl font-bold text-brand">Categorías</h1>
        <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">+ Nueva categoría</a>
    </div>

    <div class="card overflow-x-auto">
        <table class="w-full text-left">
            <caption class="sr-only">Listado de categorías</caption>
            <thead class="bg-subtle text-sm">
                <tr>
                    <th scope="col" class="p-3">Nombre</th>
                    <th scope="col" class="p-3">Descripción</th>
                    <th scope="col" class="p-3 text-right">Noticias</th>
                    <th scope="col" class="p-3"><span class="sr-only">Acciones</span></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($categories as $c)
                    <tr class="border-t border-line align-top">
                        <td class="p-3"><span class="badge {{ $c->badge_classes }}">{{ $c->name }}</span></td>
                        <td class="p-3">{{ $c->description }}</td>
                        <td class="p-3 text-right">{{ $c->posts_count }}</td>
                        <td class="p-3">
                            <div class="flex flex-wrap justify-end gap-2">
                                <a href="{{ route('admin.categories.edit', $c) }}" class="btn btn-secondary">Editar<span class="sr-only"> {{ $c->name }}</span></a>
                                <form method="POST" action="{{ route('admin.categories.destroy', $c) }}"
                                      onsubmit="return confirm('¿Eliminar la categoría {{ $c->name }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-danger">Eliminar<span class="sr-only"> {{ $c->name }}</span></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
