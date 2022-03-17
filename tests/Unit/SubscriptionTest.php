<?php

use App\Models\Customer;
use App\Models\Order;
use App\Models\Subscription;
use App\Models\Pet;
use App\Services\SubscriptionsService;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class SubscriptionTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @var Customer|null
     */
    protected $customer = null;
    protected $faker = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->customer = Customer::factory()->create();
        $this->faker = new Faker\Generator();
    }


    /**
     * @return Subscription
     */
    private function generateSubscription()
    {
        /**
         * @var Subscription $subscription
         */
        $subscription = Subscription::factory()->for(
            Customer::factory()
        )->has(
            Pet::factory()->count($this->faker->numberBetween(1,4))
        )->has(
            Order::factory()
        )->create();

        return $subscription;
    }

    /**
     * @test
     * @return void
     */
    public function testCreateSubscription()
    {
        $subscription = $this->generateSubscription();

        $this->assertNotNull($subscription);
    }

    /**
     * Creates prices and activate the subscription
     * @return void
     * @throws \App\Exceptions\CantUpdateSubscriptionPrices
     * @throws \App\Exceptions\SubscriptionMinimumAmountOfPetsNotReached
     */
    public function testActivateSubscription()
    {
        $subscription = $this->generateSubscription();

        $activated = SubscriptionsService::activateSubscription($subscription);

        $this->assertTrue($activated);
        $this->assertGreaterThanOrEqual(Subscription::PRICE__UNIT, $subscription->base_price);
    }
}
