<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Post extends Model
{
    use HasFactory;

    public const BORRADOR = 'draft';
    public const PUBLICADO = 'published';

    protected $fillable = [
        'category_id', 'user_id', 'title', 'slug', 'excerpt', 'body',
        'image', 'image_alt', 'status', 'published_at',
    ];

    protected function casts(): array
    {
        return ['published_at' => 'datetime'];
    }

    protected static function booted(): void
    {
        static::saving(function (Post $post) {
            //slug único a partir del título
            if (empty($post->slug) || $post->isDirty('title')) {
                $base = Str::slug($post->title);
                $slug = $base;
                $i = 2;
                while (static::where('slug', $slug)->where('id', '!=', $post->id ?? 0)->exists()) {
                    $slug = $base . '-' . $i++;
                }
                $post->slug = $slug;
            }

            //al publicar por primera vez se registra la fecha
            if ($post->status === self::PUBLICADO && empty($post->published_at)) {
                $post->published_at = now();
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /* ---------- relaciones ---------- */

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function approvedComments(): HasMany
    {
        return $this->comments()->where('approved', true)->latest();
    }

    /* ---------- scopes ---------- */

    //solo noticias publicadas cuya fecha de publicación ya llegó
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', self::PUBLICADO)
            ->where('published_at', '<=', now());
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (! $term) {
            return $query;
        }
        $like = '%' . addcslashes($term, '%_\\') . '%';

        return $query->where(fn ($q) => $q->where('title', 'like', $like)
            ->orWhere('excerpt', 'like', $like)
            ->orWhere('body', 'like', $like));
    }

    /* ---------- accesores ---------- */

    public function getImageUrlAttribute(): ?string
    {
        return $this->image ? Storage::url($this->image) : null;
    }

    //minutos de lectura aproximados (200 palabras por minuto)
    public function getReadingTimeAttribute(): int
    {
        return max(1, (int) ceil(str_word_count(strip_tags($this->body)) / 200));
    }
}
