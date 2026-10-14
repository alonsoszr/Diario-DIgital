<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        $categories = Category::withCount('posts')->orderBy('name')->get();

        return view('admin.categories.index', compact('categories'));
    }

    public function create(): View
    {
        return view('admin.categories.form', ['category' => new Category(['color' => 'azul'])]);
    }

    public function store(Request $request): RedirectResponse
    {
        Category::create($this->datos($request));

        return redirect()->route('admin.categories.index')->with('status', 'Categoría creada correctamente.');
    }

    public function edit(Category $category): View
    {
        return view('admin.categories.form', compact('category'));
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $category->update($this->datos($request, $category));

        return redirect()->route('admin.categories.index')->with('status', 'Categoría actualizada.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        //no se borran categorías con noticias para no dejar noticias huérfanas
        if ($category->posts()->exists()) {
            return back()->with('error', 'No se puede eliminar: la categoría tiene noticias asociadas.');
        }
        $category->delete();

        return back()->with('status', 'Categoría eliminada.');
    }

    private function datos(Request $request, ?Category $category = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:80', Rule::unique('categories', 'name')->ignore($category)],
            'description' => ['nullable', 'string', 'max:255'],
            'color' => ['required', Rule::in(Category::COLORES)],
        ]);
    }
}
