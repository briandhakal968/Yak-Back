<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tour extends Model
{
    use HasFactory;

    protected $table = 'tours';

    protected $fillable = [
        'external_id',
        'title',
        'slug',
        'overview',
        'highlights',
        'itinerary',
        'inclusions',
        'exclusions',
        'gallery',
        'route_map_image',
        'altitude_chart_image',
        'video',
        'faqs',
        'route_info',
        'duration_value',
        'duration_unit',
        'departure_info',
        'price_amount',
        'price_currency',
        'price_label',
        'discount_percent',
        'max_altitude',
        'best_season',
        'group_size',
        'region',
        'category',
        'sub_category',
        'is_popular',
        'status',
        'published_at',
        'trip_grade',
        'country',
        'activities',
    ];

    protected $casts = [
        'highlights' => 'array',
        'itinerary' => 'array',
        'inclusions' => 'array',
        'exclusions' => 'array',
        'gallery' => 'array',
        'faqs' => 'array',
        'route_info' => 'array',
        'departure_info' => 'array',
        'duration_value' => 'integer',
        'price_amount' => 'integer',
        'discount_percent' => 'integer',
        'is_popular' => 'boolean',
        'published_at' => 'datetime',
    ];
}
