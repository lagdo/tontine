<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

use Siak\Tontine\Model\Guild;
use Siak\Tontine\Model\Member;

class MemberFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Member::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $isMale = $this->faker->boolean();
        return [
            'name' => $this->faker->name($isMale ? 'male' : 'female'),
            'email' => $this->faker->email(),
            'phone' => '', // $this->faker->phoneNumber(),
            'address' => $this->faker->address(),
            'city' => $this->faker->city(),
            'birthday' => $this->faker->date(),
            'guild_id' => Guild::get()->random(),
        ];
    }
}
