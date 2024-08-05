<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Account;
use App\Models\Transfer;
use App\Models\User;

class TransferFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Transfer::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'date' => $this->faker->date(),
            'amount' => $this->faker->randomFloat(2, 0, 99999999999.99),
            'description' => $this->faker->text(),
            'user_id' => User::factory(),
            'source_account_id' => Account::factory(),
            'destination_account_id' => Account::factory(),
        ];
    }

    public function forUser($userId)
    {
        return $this->state(function (array $attributes) use ($userId) {
            return [
                'user_id' => $userId,
                'destination_account_id' => Account::factory()->forUser($userId),
                'source_account_id' => Account::factory()->forUser($userId),
            ];
        });
    }


}
