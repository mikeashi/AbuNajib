<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Account;
use App\Models\AccountType;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Account>
 */
class AccountFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Account::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'balance' => $this->faker->randomFloat(2, 0, 99999999999.99),
            'description' => $this->faker->text(),
            'account_type_id' => AccountType::factory(),
            'user_id' => User::factory(),
        ];
    }

    public function forUser($userId)
    {
        return $this->state(function (array $attributes) use ($userId) {
            return [
                'user_id' => $userId,
                'account_type_id' => AccountType::factory()->forUser($userId),
            ];
        });
    }
}
