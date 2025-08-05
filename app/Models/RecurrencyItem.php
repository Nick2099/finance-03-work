<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecurrencyItem extends Model
{
    /** @use HasFactory<\Database\Factories\RecurrencyItemFactory> */
    use HasFactory;

    protected $fillable = [
        'recurrency_header_id',
        'group_id',
        'subgroup_id',
        'group_type',
        'amount',
        'note',
        'badges', // Add badges to fillable so it can be mass-assigned
    ];

    public function group()
    {
        return $this->belongsTo(\App\Models\Group::class, 'group_id');
    }

    public function subgroup()
    {
        return $this->belongsTo(\App\Models\Subgroup::class, 'subgroup_id');
    }
}
