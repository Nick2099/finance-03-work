<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    /** @use HasFactory<\Database\Factories\ItemFactory> */
    use HasFactory;

        protected $fillable = [
        'header_id',
        'group_id',
        'subgroup_id',
        'group_type',
        'amount',
        'note',
        'badges', // Add badges to fillable so it can be mass-assigned
    ];

    protected $casts = [
        'badges' => 'array', // Automatically cast badges to array
    ];

    public function header()
    {
        return $this->belongsTo(Header::class);
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function subgroup()
    {
        return $this->belongsTo(Subgroup::class);
    }
}
