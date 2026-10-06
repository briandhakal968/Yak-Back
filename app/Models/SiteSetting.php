<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    protected $table = 'site_settings';

    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'label',
        'description',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    /**
     * Decode a JSON-typed value, falling back to the raw string.
     */
    public function decodedValue(): mixed
    {
        if ($this->type === 'json' && $this->value !== null) {
            $decoded = json_decode($this->value, true);
            return is_array($decoded) ? $decoded : $this->value;
        }
        return $this->value;
    }
}
