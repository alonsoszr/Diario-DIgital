<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;

    //colores disponibles para la etiqueta (todos con contraste AA sobre su fondo)
    public const COLORES = ['azul', 'verde', 'rojo', 'ambar', 'morado', 'gris'];

    protected $fillable = ['name', 'slug', 'description', 'color'];

    protected static function booted(): void
    {
        //el slug se genera solo a partir del nombre
        static::saving(function (Category $category) {
            if (empty($category->slug) || $category->isDirty('name')) {
                $category->slug = Str::slug($category->name);
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    //clases completas (Tailwind no detecta nombres armados) con contraste AA
    public function getBadgeClassesAttribute(): string
    {
        return match ($this->color) {
            'verde' => 'bg-green-100 text-green-900 ring-green-700',
            'rojo' => 'bg-red-100 text-red-900 ring-red-700',
            'ambar' => 'bg-amber-100 text-amber-900 ring-amber-700',
            'morado' => 'bg-purple-100 text-purple-900 ring-purple-700',
            'gris' => 'bg-slate-200 text-slate-900 ring-slate-600',
            default => 'bg-blue-100 text-blue-900 ring-blue-700',
        };
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }
}
