@extends('layouts.admin')

@php $editando = $post->exists; @endphp
@section('title', $editando ? 'Editar noticia' : 'Nueva noticia')

@section('content')
    <h1 class="mb-6 font-serif text-3xl font-bold text-brand">{{ $editando ? 'Editar noticia' : 'Nueva noticia' }}</h1>

    @if ($errors->any())
        <div role="alert" class="mb-6 rounded-lg border-l-4 border-red-700 bg-red-50 p-4 text-red-900">
            <p class="font-semibold">No se pudo guardar. Corrige lo siguiente:</p>
            <ul class="mt-2 list-inside list-disc">
                @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" enctype="multipart/form-data" novalidate class="card max-w-3xl space-y-5 p-6"
          action="{{ $editando ? route('admin.posts.update', $post) : route('admin.posts.store') }}">
        @csrf
        @if ($editando) @method('PUT') @endif
        <p class="hint">Los campos con * son obligatorios.</p>

        <div>
            <label for="title" class="label">Título *</label>
            <input id="title" name="title" type="text" maxlength="150" required value="{{ old('title', $post->title) }}"
                   class="field @error('title') border-red-700 @enderror" @error('title') aria-invalid="true" aria-describedby="e-title" @enderror>
            @error('title') <p id="e-title" class="error-text">⚠ {{ $message }}</p> @enderror
        </div>

        <div>
            <label for="category_id" class="label">Categoría *</label>
            <select id="category_id" name="category_id" required class="field @error('category_id') border-red-700 @enderror"
                    @error('category_id') aria-invalid="true" aria-describedby="e-category_id" @enderror>
                <option value="">Selecciona una categoría</option>
                @foreach ($categories as $c)
                    <option value="{{ $c->id }}" @selected(old('category_id', $post->category_id) == $c->id)>{{ $c->name }}</option>
                @endforeach
            </select>
            @error('category_id') <p id="e-category_id" class="error-text">⚠ {{ $message }}</p> @enderror
        </div>

        <div>
            <label for="excerpt" class="label">Resumen * <span class="font-normal">(máx. 300 caracteres)</span></label>
            <textarea id="excerpt" name="excerpt" rows="2" maxlength="300" required
                      class="field @error('excerpt') border-red-700 @enderror" @error('excerpt') aria-invalid="true" aria-describedby="e-excerpt" @enderror>{{ old('excerpt', $post->excerpt) }}</textarea>
            @error('excerpt') <p id="e-excerpt" class="error-text">⚠ {{ $message }}</p> @enderror
        </div>

        <div>
            <label for="body" class="label">Contenido *</label>
            <textarea id="body" name="body" rows="10" required aria-describedby="h-body"
                      class="field @error('body') border-red-700 @enderror">{{ old('body', $post->body) }}</textarea>
            <p id="h-body" class="hint">Separa los párrafos con una línea en blanco.</p>
            @error('body') <p class="error-text">⚠ {{ $message }}</p> @enderror
        </div>

        <fieldset class="space-y-4 rounded-lg border border-line p-4">
            <legend class="px-2 font-semibold">Imagen (opcional)</legend>
            @if ($post->image_url)
                <img src="{{ $post->image_url }}" alt="Imagen actual: {{ $post->image_alt }}" width="240" height="135" class="rounded-lg">
            @endif
            <div>
                <label for="image" class="label">Archivo de imagen</label>
                <input id="image" name="image" type="file" accept="image/jpeg,image/png,image/webp" aria-describedby="h-image"
                       class="field py-2 @error('image') border-red-700 @enderror">
                <p id="h-image" class="hint">Formatos JPG, PNG o WEBP. Tamaño máximo 2 MB.</p>
                @error('image') <p class="error-text">⚠ {{ $message }}</p> @enderror
            </div>
            <div>
                <label for="image_alt" class="label">Texto alternativo de la imagen <span class="font-normal">(obligatorio si hay imagen)</span></label>
                <input id="image_alt" name="image_alt" type="text" maxlength="200" aria-describedby="h-alt"
                       value="{{ old('image_alt', $post->image_alt) }}" class="field @error('image_alt') border-red-700 @enderror">
                <p id="h-alt" class="hint">Describe la imagen para personas que usan lector de pantalla.</p>
                @error('image_alt') <p class="error-text">⚠ {{ $message }}</p> @enderror
            </div>
        </fieldset>

        <div>
            <label for="status" class="label">Estado *</label>
            <select id="status" name="status" required class="field max-w-xs">
                <option value="draft" @selected(old('status', $post->status) === 'draft')>Borrador (no visible)</option>
                <option value="published" @selected(old('status', $post->status) === 'published')>Publicado</option>
            </select>
        </div>

        <div class="flex flex-wrap gap-3">
            <button type="submit" class="btn btn-primary">{{ $editando ? 'Guardar cambios' : 'Crear noticia' }}</button>
            <a href="{{ route('admin.posts.index') }}" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
@endsection
