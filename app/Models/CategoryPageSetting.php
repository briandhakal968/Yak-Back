<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Per-category listing page hero settings (trekking, peak-climbing, mountain-biking).
 */
class CategoryPageSetting extends Model
{
    use HasFactory;

    protected $table = 'category_page_settings';

    protected $fillable = [
        'category',
        'banner_image',
        'banner_alt',
    ];
}
