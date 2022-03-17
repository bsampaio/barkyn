<?php

namespace Database\Factories;

use App\Model;
use App\Models\Customer;
use App\Models\Pet;
use App\Models\Subscription;
use App\Services\SubscriptionsService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubscriptionFactory extends Factory
{
    protected $model = Subscription::class;

    public function definition(): array
    {
        $basePrice = 0;
        $totalPrice = 0;

    	return [
    	    'base_price' => $basePrice,
            'total_price' => $totalPrice,
            'activated' => false,
            'next_order_date' => Carbon::now()->addDays(20 + $this->faker->numberBetween(1, 30))
    	];
    }
}
