<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CommentController extends Controller
{
    public function index(): View
    {
        $pendientes = Comment::with('post')->where('approved', false)->latest()->get();
        $aprobados = Comment::with('post')->where('approved', true)->latest()->paginate(10);

        return view('admin.comments.index', compact('pendientes', 'aprobados'));
    }

    public function approve(Comment $comment): RedirectResponse
    {
        $comment->update(['approved' => true]);

        return back()->with('status', 'Comentario aprobado.');
    }

    public function destroy(Comment $comment): RedirectResponse
    {
        $comment->delete();

        return back()->with('status', 'Comentario eliminado.');
    }
}
