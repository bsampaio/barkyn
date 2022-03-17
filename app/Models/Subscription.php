<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property double $base_price
 * @property double $total_price
 * @property Carbon $next_order_date
 * @property Customer $customer
 * @property Pet[]|null $pets
 * @property Order[]|null $orders
 */
class Subscription extends Model
{
    use HasFactory;

    const PRICE__UNIT = 10;
    const PRICE__DISCOUNT_PERCENTAGE = 5;

    protected $table = 'subscriptions';

    protected $fillable = [
        'base_price',
        'total_price',
        'activated'
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function pets(): HasMany
    {
        return $this->hasMany(Pet::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
