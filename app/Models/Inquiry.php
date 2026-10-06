<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Plan Your Trip inquiry model.å
 */
class Inquiry extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'travel_group',
        'number_of_people',
        'date_type',
        'arrival_date',
        'departure_date',
        'travel_month',
        'destination',
        'activity',
        'packages',
        'accommodation',
        'exercise',
        'full_name',
        'country',
        'email',
        'phone',
        'message',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'packages' => 'array',
        'arrival_date' => 'date',
        'departure_date' => 'date',
    ];
}
