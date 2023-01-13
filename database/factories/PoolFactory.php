<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

use Siak\Tontine\Model\Pool;

use function trim;

class PoolFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Pool::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $amount = $this->faker->randomNumber(2, false) * 1000;
        return [
            'title' => 'Liste de ' . $amount,
            'amount' => $amount,
            'notes' => trim($this->faker->sentence(4), '.'),
        ];
    }
}
