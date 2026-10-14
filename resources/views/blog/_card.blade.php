{{-- Tarjeta de noticia reutilizable --}}
<article class="card flex h-full flex-col">
    <a href="{{ route('posts.show', $post) }}" tabindex="-1" aria-hidden="true">
        @if ($post->image_url)
            <img src="{{ $post->image_url }}" alt="" width="600" height="338" loading="lazy" decoding="async"
                 class="aspect-video w-full object-cover">
        @else
            <div class="flex aspect-video w-full items-center justify-center bg-gradient-to-br from-blue-800 to-blue-950 font-serif text-5xl text-white/80">
                {{ mb_substr($post->category->name, 0, 1) }}
            </div>
        @endif
    </a>
    <div class="flex flex-1 flex-col p-5">
        <p class="mb-2"><a href="{{ route('categories.show', $post->category) }}" class="badge ring-1 {{ $post->category->badge_classes }}">{{ $post->category->name }}</a></p>
        <h3 class="mb-2 font-serif text-xl font-bold leading-snug">
            <a href="{{ route('posts.show', $post) }}" class="text-ink hover:text-brand hover:underline">{{ $post->title }}</a>
        </h3>
        <p class="mb-4 flex-1 text-ink">{{ $post->excerpt }}</p>
        <p class="text-sm text-muted">
            <time datetime="{{ $post->published_at->toDateString() }}">{{ $post->published_at->translatedFormat('d \d\e F \d\e Y') }}</time>
            · {{ $post->reading_time }} min de lectura
        </p>
    </div>
</article>
