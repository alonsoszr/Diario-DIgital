<?php

use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\CommentController as AdminCommentController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PostController as AdminPostController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/* ---------- parte pública ---------- */
Route::get('/', [BlogController::class, 'index'])->name('home');
Route::get('/noticias/{post:slug}', [BlogController::class, 'show'])->name('posts.show');
Route::get('/categorias/{category:slug}', [BlogController::class, 'category'])->name('categories.show');
Route::get('/buscar', [BlogController::class, 'search'])->name('search');
Route::post('/noticias/{post:slug}/comentarios', [CommentController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('comments.store');

/* ---------- acceso y perfil ---------- */
//Breeze redirige a "dashboard" tras iniciar sesión: se envía al panel
Route::get('/dashboard', fn () => redirect()->route('admin.dashboard'))
    ->middleware('auth')->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/* ---------- panel de administración ---------- */
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', DashboardController::class)->name('dashboard');
    Route::resource('noticias', AdminPostController::class)
        ->parameters(['noticias' => 'post'])->names('posts')->except('show');
    Route::resource('categorias', AdminCategoryController::class)
        ->parameters(['categorias' => 'category'])->names('categories')->except('show');
    Route::get('comentarios', [AdminCommentController::class, 'index'])->name('comments.index');
    Route::patch('comentarios/{comment}/aprobar', [AdminCommentController::class, 'approve'])->name('comments.approve');
    Route::delete('comentarios/{comment}', [AdminCommentController::class, 'destroy'])->name('comments.destroy');
});

require __DIR__.'/auth.php';
