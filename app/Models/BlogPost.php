<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Blog post model.
 */
class BlogPost extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'slug',
        'content',
        'excerpt',
        'featured_image',
        'category_id',
        'author_id',
        'tags',
        'status',
        'seo',
        'reading_time',
        'published_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'featured_image' => 'array',
        'tags' => 'array',
        'seo' => 'array',
        'published_at' => 'datetime',
    ];

    /**
     * Get post excerpt or fallback to meta description or first 150 chars of content.
     */
    public function getExcerptAttribute($value): string
    {
        if (!empty($value)) {
            return $value;
        }

        if (!empty($this->seo['description'])) {
            return $this->seo['description'];
        }

        $plain = trim(preg_replace('/\s+/', ' ', strip_tags($this->content ?? '')));
        return \Illuminate\Support\Str::limit($plain, 150);
    }

    /**
     * Get reading time or auto-calculate from word count.
     */
    public function getReadingTimeAttribute($value): int
    {
        if (!empty($value) && (int)$value > 0) {
            return (int)$value;
        }

        $plain = trim(preg_replace('/\s+/', ' ', strip_tags($this->content ?? '')));
        $wordCount = str_word_count($plain);
        return (int) max(1, ceil($wordCount / 200));
    }

    /**
     * The post category.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(BlogCategory::class, 'category_id');
    }

    /**
     * The post author.
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * Use slug for route model binding.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
