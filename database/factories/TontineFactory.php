<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

use Siak\Tontine\Model\Tontine;

class TontineFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Tontine::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => rtrim($this->faker->sentence(), '.'),
            'shortname' => rtrim($this->faker->sentence(1), '.'),
            'biography' => $this->faker->paragraph(),
            'email' => $this->faker->companyEmail(),
            'phone' => $this->faker->phoneNumber(),
            'address' => $this->faker->address(),
            'city' => $this->faker->city(),
            'country_code' => 'CM',
            'currency_code' => 'XAF',
        ];
    }
}
