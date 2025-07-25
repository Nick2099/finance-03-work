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
        'recurrency_id',
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

    public function type()
    {
        $firstItem = $this->items()->first();
        return $firstItem ? $firstItem->group_type : null;
    }

    /**
     * Get an array of badge_id => sum(amount) for all items of this header that have that badge.
     * @return array
     */
    public function badges()
    {
        $badgeSums = [];
        foreach ($this->items as $item) {
            if (is_array($item->badges) && !empty($item->badges)) {
                foreach ($item->badges as $badgeId) {
                    if (!isset($badgeSums[$badgeId])) {
                        $badgeSums[$badgeId] = 0;
                    }
                    $badgeSums[$badgeId] += (float)$item->amount;
                }
            }
        }
        // Format as string with 2 decimals, keys as strings
        // foreach ($badgeSums as $id => &$sum) {
        //     $sum = number_format($sum, 2, '.', '');
        // }
        // unset($sum);
        return $badgeSums;
    }
}
