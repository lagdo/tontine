<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

use Siak\Tontine\Model\Charge;

use function trim;

class ChargeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Charge::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $type = $this->faker->randomElement([0, 1]);
        return [
            'name' => trim($this->faker->sentence(4), '.'),
            'type' => $type,
            'amount' => $this->faker->randomNumber($type === 0 ? 3 : 1, true) * 100,
            'period' => $type === 0 ? $this->faker->randomElement([1, 2, 3]) : 0,
        ];
    }
}
