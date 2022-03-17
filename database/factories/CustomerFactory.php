<?php

namespace Database\Factories;

use App\Model;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class CustomerFactory extends Factory
{
    protected $model = Customer::class;

    public function definition(): array
    {
        $gender = $this->faker->randomElement(Customer::getGenders());

        return [
            'name' => $this->faker->name($this->getFullGender($gender)),
            'email' => $this->faker->unique()->safeEmail,
            'gender' => $gender,
            'birth_date' => $this->faker->dateTimeThisCentury
        ];
    }

    private function getFullGender($gender): ?string
    {
        $fullGender = strtolower(Customer::getFullGender($gender));
        if($fullGender === Customer::GENDER__UNDECLARED__FULL) {
            return null;
        }

        return $fullGender;
    }
}
