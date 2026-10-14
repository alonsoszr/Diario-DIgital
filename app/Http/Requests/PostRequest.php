<?php

namespace App\Http\Requests;

use App\Models\Post;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->is_admin;
    }

    public function rules(): array
    {
        $post = $this->route('post');

        return [
            'category_id' => ['required', 'exists:categories,id'],
            'title' => ['required', 'string', 'min:5', 'max:150'],
            'excerpt' => ['required', 'string', 'min:10', 'max:300'],
            'body' => ['required', 'string', 'min:30'],
            'status' => ['required', Rule::in([Post::BORRADOR, Post::PUBLICADO])],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            //el texto alternativo es obligatorio cuando hay imagen (accesibilidad)
            'image_alt' => [
                Rule::requiredIf(fn () => $this->hasFile('image') || ($post && $post->image)),
                'nullable', 'string', 'max:200',
            ],
        ];
    }

    public function attributes(): array
    {
        return [
            'category_id' => 'categoría', 'title' => 'título', 'excerpt' => 'resumen',
            'body' => 'contenido', 'status' => 'estado', 'image' => 'imagen',
            'image_alt' => 'texto alternativo',
        ];
    }
}
