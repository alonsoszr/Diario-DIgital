<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    //los comentarios quedan pendientes hasta que un administrador los apruebe
    public function store(Request $request, Post $post): RedirectResponse
    {
        abort_unless($post->status === Post::PUBLICADO && $post->published_at <= now(), 404);

        $datos = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:80'],
            'email' => ['required', 'email', 'max:120'],
            'body' => ['required', 'string', 'min:5', 'max:1000'],
        ]);

        $post->comments()->create($datos + ['approved' => false]);

        return redirect()->to(route('posts.show', $post) . '#comentarios')
            ->with('status', 'Gracias por tu comentario. Se publicará cuando sea revisado.');
    }
}
