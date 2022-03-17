<?php

namespace App\Models;

use App\Contracts\AutoUuidModel;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Webpatser\Uuid\Uuid;

/**
 * @property int $id
 * @property Carbon $estimated_shipping
 * @property Carbon|null $ship_date
 * @property string $description
 * @property Subscription $subscription
 */
class Order extends AutoUuidModel
{
    protected $table = 'orders';

    protected $fillable = [
        'estimated_shipping',
        'ship_date',
        'description'
    ];

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }
}
