<?php

namespace Database\Factories;

use App\Model;
use App\Models\Customer;
use App\Models\Pet;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class PetFactory extends Factory
{
    protected $model = Pet::class;

    public function definition(): array
    {
        $gender = $this->faker->randomElement(Pet::getGenders());

        return [
            'name' => $this->faker->firstName($this->getFullGender($gender)),
            'gender' => $gender,
            'lifestage' => $this->getRandomLifestage(),
        ];
    }

    private function getRandomLifestage()
    {
        $stages = [
            'Puppy',
            'Adult',
            'Senior',
            'Weight'
        ];


        $stage = $this->faker->randomElement($stages);
        if($stage === 'Weight') {
            return $this->faker->numberBetween(1, 155) . ' kg(s)';
        }

        return $stage;
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
