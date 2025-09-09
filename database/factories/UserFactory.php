<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = User::class; //Класс модели

    public function definition(): array
    {
        return [
            'user_id'     => $this->faker->numberBetween(100_000, 1_000_000),
            'username'    => $this->faker->userName,
            'first_name'  => $this->faker->firstName,
            'last_name'   => $this->faker->lastName,
            'lastmessage' => '',
            'is_premium'  => $this->faker->numberBetween(0, 1),
            'blocked'     => $this->faker->numberBetween(0, 1),
        ];
    }
}
