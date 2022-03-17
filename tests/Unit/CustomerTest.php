<?php

use App\Models\Customer;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class CustomerTest extends TestCase
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

    /**
     * Creates a customer and stores it at the database.
     * @test
     * @return void
     */
    public function testCreateCustomer()
    {
        $customer = Customer::factory()->create();

        $this->assertNotNull($customer);
    }

    /**
     * Creates a customer and deletes it.
     * @test
     * @return void
     */
    public function testDeletesCustomer()
    {
        $customer = Customer::factory()->create();
        $deleted = $customer->delete();
        $this->assertTrue($deleted, "The Customer has been deleted successfully.");
    }

    /**
     * Creates a customer and edits its data.
     * @test
     * @return void
     */
    public function testEditCustomer()
    {
        $updatedData = [
            'name' => 'Breno Grillo',
            'email' => 'brenogrillo@gmail.com',
            'gender' => Customer::GENDER__MALE,
            'birth_date' => \Carbon\Carbon::now()
        ];

        $updated = $this->customer->update($updatedData);

        $this->assertTrue($updated);

        $this->assertEquals($updatedData['name'], $this->customer->name);
        $this->assertEquals($updatedData['email'], $this->customer->email);
        $this->assertEquals($updatedData['gender'], $this->customer->gender);
        $this->assertEquals($updatedData['birth_date']->format('Y-m-d'), $this->customer->birth_date->format('Y-m-d'));
    }

    /**
     * Retrieves all customers.
     * @test
     * @return void
     */
    public function testGetAllCustomers()
    {
        $amount = 10;
        Customer::factory($amount)->create();

        $customers = Customer::all();
        $this->assertGreaterThanOrEqual($amount, $customers->count());
    }
}
