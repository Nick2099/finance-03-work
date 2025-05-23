<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Entry extends Model
{
    /** @use HasFactory<\Database\Factories\EntryFactory> */
    use HasFactory;

    protected $fillable = [
        'group_id',
        'subgroup_id',
        'amount',
        'description',
    ];

    /**
     * The attributes that should be cast to native types.
     * Oposite of $fillable, allows all input except the ones listed.
     *
     * @var array
     protected $guarded = [
        'id',
        'created_at',
        'updated_at',
    ];
    */
}
