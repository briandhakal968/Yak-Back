<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Booking model for all service types.
 */
class Booking extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'reference_code',
        'type',
        'status',
        'customer_info',
        'details',
        'price',
        'payment',
        'user_id',
        'booked_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'customer_info' => 'array',
        'details' => 'array',
        'price' => 'array',
        'payment' => 'array',
        'booked_at' => 'datetime',
    ];

    /**
     * The customer who made the booking, if authenticated.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
