<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecurrencyHeader extends Model
{
    /** @use HasFactory<\Database\Factories\RecurrencyHeaderFactory> */
    use HasFactory;

    protected $fillable = [
        'recurrency_id',
        'date',
        'amount',
        'place_of_purchase',
        'location',
        'note',
    ];

    public function items()
    {
        return $this->hasMany(RecurrencyItem::class);
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
