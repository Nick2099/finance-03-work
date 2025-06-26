<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Badge extends Model
{
    /** @use HasFactory<\Database\Factories\BadgeFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'badge_id', // Add badge_id to fillable so it can be mass-assigned
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
