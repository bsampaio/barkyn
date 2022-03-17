<?php

namespace Database\Factories;

use App\Model;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Webpatser\Uuid\Uuid;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        $estimatedShipping = Carbon::now()->addDays($this->faker->numberBetween(0, 30)); //About a month to ship
        $wasShipped = $this->faker->boolean;
        $shipDate = $wasShipped ? $estimatedShipping->addDays($this->faker->numberBetween(0,7)) : null; //About a week to ship

    	return [
    	    'estimated_shipping' => $estimatedShipping,
            'ship_date' => $shipDate,
            'description' => $this->faker->paragraph,
            'uuid' => (string) Uuid::generate(4)
    	];
    }
}
