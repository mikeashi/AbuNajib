<?php

namespace Database\Factories;

use App\Models\Account;
use App\Models\TransactionGroup;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Budget;
use App\Models\PlannedTransaction;
use App\Models\Transaction;
use App\Models\TransactionCategory;
use App\Models\User;
use App\Enums\TransactionType;
class PlannedTransactionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PlannedTransaction::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'amount' => $this->faker->randomFloat(2, 0, 99999999999.99),
            'description' => $this->faker->text(),
            'budget_id' => Budget::factory(),
            'account_id' => Account::factory(),
            'type' => $this->faker->randomElement(TransactionType::class),
            'category_id' => TransactionCategory::factory(),
            'group_id' => TransactionGroup::factory(),
            'linked_transaction_id' => Transaction::factory(),
            'user_id' => User::factory(),
        ];
    }


    public function forUser($userId)
    {
        return $this->state(function (array $attributes) use ($userId) {
            return [
                'budget_id' => Budget::factory()->forUser($userId),
                'account_id' => Account::factory()->forUser($userId),
                'category_id' => TransactionCategory::factory()->forUser($userId),
                'group_id' => TransactionGroup::factory()->forUser($userId),
                'linked_transaction_id' => Transaction::factory()->forUser($userId),
                'user_id' => $userId,
            ];
        });
    }

}
