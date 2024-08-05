<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Account;
use App\Models\Budget;
use App\Models\BudgetAccount;

class BudgetAccountFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = BudgetAccount::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'starting_balance' => $this->faker->randomFloat(2, 0, 99999999999.99),
            'ending_balance' => $this->faker->randomFloat(2, 0, 99999999999.99),
            'user_id' => User::factory(),
            'account_id' => Account::factory(),
            'budget_id' => Budget::factory(),
        ];
    }


    public function forUser($userId)
    {
        return $this->state(function (array $attributes) use ($userId) {
            return [
                'user_id' => $userId,
                'account_id' => Account::factory()->forUser($userId),
                'budget_id' => Budget::factory()->forUser($userId),
            ];
        });
    }


}
