<?php

use App\Models\Customer;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class SubscriptionTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @var Customer|null
     */
    protected $customer = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->customer = Customer::factory()->create();
    }


}
