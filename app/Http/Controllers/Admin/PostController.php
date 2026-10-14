<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PostRequest;
use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PostController extends Controller
{
    public function index(): View
    {
        $posts = Post::with('category')->withCount('comments')
            ->latest('updated_at')->paginate(10);

        return view('admin.posts.index', compact('posts'));
    }

    public function create(): View
    {
        return view('admin.posts.form', [
            'post' => new Post(['status' => Post::BORRADOR]),
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function store(PostRequest $request): RedirectResponse
    {
        $datos = $request->safe()->except('image');
        if ($request->hasFile('image')) {
            $datos['image'] = $request->file('image')->store('noticias', 'public');
        }
        $request->user()->posts()->create($datos);

        return redirect()->route('admin.posts.index')->with('status', 'Noticia creada correctamente.');
    }

    public function edit(Post $post): View
    {
        return view('admin.posts.form', [
            'post' => $post,
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function update(PostRequest $request, Post $post): RedirectResponse
    {
        $datos = $request->safe()->except('image');
        if ($request->hasFile('image')) {
            if ($post->image) {
                Storage::disk('public')->delete($post->image);
            }
            $datos['image'] = $request->file('image')->store('noticias', 'public');
        }
        $post->update($datos);

        return redirect()->route('admin.posts.index')->with('status', 'Noticia actualizada.');
    }

    public function destroy(Post $post): RedirectResponse
    {
        if ($post->image) {
            Storage::disk('public')->delete($post->image);
        }
        $post->delete();

        return redirect()->route('admin.posts.index')->with('status', 'Noticia eliminada.');
    }
}
