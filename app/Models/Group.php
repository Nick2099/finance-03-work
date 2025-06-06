<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    /** @use HasFactory<\Database\Factories\GroupFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'type',
        'privacy',
    ];

    public function subgroups()
    {
        return $this->hasMany(Subgroup::class);
    }

    public function collection()
    {
        return $this->belongsTo(Collection::class);
    }
}
