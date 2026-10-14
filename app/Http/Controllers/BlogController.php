<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BlogController extends Controller
{
    private const POR_PAGINA = 9;

    //portada: noticia destacada (la más reciente) + listado paginado
    public function index(): View
    {
        $base = Post::published()->with('category')->latest('published_at');

        $destacada = null;
        if (request()->integer('page', 1) === 1) {
            $destacada = (clone $base)->first();
        }

        $posts = $base
            ->when($destacada, fn ($q) => $q->where('id', '!=', $destacada->id))
            ->paginate(self::POR_PAGINA);

        return view('blog.index', compact('destacada', 'posts'));
    }

    public function show(Post $post): View
    {
        //los borradores y las publicaciones futuras no existen para el público
        abort_unless($post->status === Post::PUBLICADO && $post->published_at <= now(), 404);

        //el contador de vistas suma una sola vez por sesión
        $clave = 'visto.' . $post->id;
        if (! session()->has($clave)) {
            $post->increment('views');
            session()->put($clave, true);
        }

        $post->load('category', 'author');
        $comentarios = $post->approvedComments()->get();
        $relacionadas = Post::published()
            ->where('category_id', $post->category_id)
            ->where('id', '!=', $post->id)
            ->latest('published_at')->limit(3)->get();

        return view('blog.show', compact('post', 'comentarios', 'relacionadas'));
    }

    public function category(Category $category): View
    {
        $posts = $category->posts()->published()->latest('published_at')->paginate(self::POR_PAGINA);

        return view('blog.category', compact('category', 'posts'));
    }

    public function search(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));
        abort_if(mb_strlen($q) > 100, 422);

        $posts = $q === ''
            ? Post::query()->whereRaw('1 = 0')->paginate(self::POR_PAGINA)
            : Post::published()->with('category')->search($q)
                ->latest('published_at')->paginate(self::POR_PAGINA)->withQueryString();

        return view('blog.search', compact('posts', 'q'));
    }
}
