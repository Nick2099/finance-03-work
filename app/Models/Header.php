<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Header extends Model
{
    /** @use HasFactory<\Database\Factories\HeaderFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date',
        'place_of_purchase',
        'location',
        'note',
        'amount',
        // Add other header fields as needed
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(Item::class);
    }
}
