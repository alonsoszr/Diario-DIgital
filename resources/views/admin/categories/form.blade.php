@extends('layouts.admin')

@php $editando = $category->exists; @endphp
@section('title', $editando ? 'Editar categoría' : 'Nueva categoría')

@section('content')
    <h1 class="mb-6 font-serif text-3xl font-bold text-brand">{{ $editando ? 'Editar categoría' : 'Nueva categoría' }}</h1>

    @if ($errors->any())
        <div role="alert" class="mb-6 rounded-lg border-l-4 border-red-700 bg-red-50 p-4 text-red-900">
            <p class="font-semibold">No se pudo guardar. Corrige lo siguiente:</p>
            <ul class="mt-2 list-inside list-disc">
                @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" novalidate class="card max-w-xl space-y-5 p-6"
          action="{{ $editando ? route('admin.categories.update', $category) : route('admin.categories.store') }}">
        @csrf
        @if ($editando) @method('PUT') @endif
        <p class="hint">Los campos con * son obligatorios.</p>

        <div>
            <label for="name" class="label">Nombre *</label>
            <input id="name" name="name" type="text" maxlength="80" required value="{{ old('name', $category->name) }}"
                   class="field @error('name') border-red-700 @enderror" @error('name') aria-invalid="true" aria-describedby="e-name" @enderror>
            @error('name') <p id="e-name" class="error-text">⚠ {{ $message }}</p> @enderror
        </div>

        <div>
            <label for="description" class="label">Descripción</label>
            <input id="description" name="description" type="text" maxlength="255" value="{{ old('description', $category->description) }}" class="field">
            @error('description') <p class="error-text">⚠ {{ $message }}</p> @enderror
        </div>

        <div>
            <label for="color" class="label">Color de la etiqueta *</label>
            <select id="color" name="color" required class="field max-w-xs">
                @foreach (\App\Models\Category::COLORES as $color)
                    <option value="{{ $color }}" @selected(old('color', $category->color) === $color)>{{ ucfirst($color) }}</option>
                @endforeach
            </select>
            <p class="hint">La categoría siempre se identifica también por su nombre, no solo por el color.</p>
        </div>

        <div class="flex flex-wrap gap-3">
            <button type="submit" class="btn btn-primary">{{ $editando ? 'Guardar cambios' : 'Crear categoría' }}</button>
            <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
@endsection
