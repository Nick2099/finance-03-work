<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subgroup extends Model
{
    /** @use HasFactory<\Database\Factories\SubgroupFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'privacy',
    ];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }
}
