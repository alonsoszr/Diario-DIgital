<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $totales = [
            'publicadas' => Post::where('status', Post::PUBLICADO)->count(),
            'borradores' => Post::where('status', Post::BORRADOR)->count(),
            'pendientes' => Comment::where('approved', false)->count(),
            'vistas' => (int) Post::sum('views'),
        ];
        $masLeidas = Post::with('category')->orderByDesc('views')->limit(5)->get();

        return view('admin.dashboard', compact('totales', 'masLeidas'));
    }
}
