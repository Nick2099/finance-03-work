<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recurrency extends Model
{
    /** @use HasFactory<\Database\Factories\RecurrencyFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'base',
        'frequency', // 1 = weekly, 2 = monthly, 3 = yearly
        'rule', // e.g., 1 for first occurrence, 2 for second, etc.
        'day_of_month', // 1-31
        'day_of_week', // 0 = Sunday, 1 = Monday, ..., 6 = Saturday
        'month', // 0 = January, 1 = February, ..., 11 = December
        'number_of_occurrences',
        'occurrences_end_date',
        'occurrences_number',
        'occurrences_dates', // JSON array of dates
    ];

    public function header()
    {
        return $this->hasOne(RecurrencyHeader::class);
    }
}
