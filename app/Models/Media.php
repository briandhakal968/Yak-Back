<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $external_id
 * @property string $filename
 * @property string|null $original_name
 * @property string $path
 * @property string|null $mime_type
 * @property string $folder
 * @property int $size
 * @property string|null $file_hash
 */
class Media extends Model
{
    /** @use HasFactory<\Database\Factories\MediaFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'external_id',
        'filename',
        'original_name',
        'path',
        'mime_type',
        'folder',
        'size',
        'file_hash',
    ];
}
