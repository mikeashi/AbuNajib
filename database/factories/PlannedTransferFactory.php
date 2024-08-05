<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Account;
use App\Models\Budget;
use App\Models\PlannedTransfer;
use App\Models\Transfer;
use App\Models\User;

class PlannedTransferFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PlannedTransfer::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'amount' => $this->faker->randomFloat(2, 0, 99999999999.99),
            'description' => $this->faker->text(),
            'budget_id' => Budget::factory(),
            'source_account_id' => Account::factory(),
            'destination_account_id' => Account::factory(),
            'linked_transfer_id' => Transfer::factory(),
            'user_id' => User::factory(),
        ];
    }


    public function forUser($userId)
    {
        return $this->state(function (array $attributes) use ($userId) {
            return [
                'budget_id' => Budget::factory()->forUser($userId),
                'source_account_id' => Account::factory()->forUser($userId),
                'destination_account_id' => Account::factory()->forUser($userId),
                'linked_transfer_id' => Transfer::factory()->forUser($userId),
                'user_id' => $userId,
            ];
        });
    }

}
