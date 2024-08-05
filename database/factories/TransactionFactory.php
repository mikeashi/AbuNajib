<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Account;
use App\Models\Transaction;
use App\Models\TransactionCategory;
use App\Models\User;
use App\Enums\TransactionType;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Transaction::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'date' => $this->faker->date(),
            'amount' => $this->faker->randomFloat(2, 0, 99999999999.99),
            'description' => $this->faker->text(),
            'type' => $this->faker->randomElement(TransactionType::class),
            'category_id' => TransactionCategory::factory(),
            'account_id' => Account::factory(),
            'user_id' => User::factory(),
        ];
    }


    public function forUser($userId)
    {
        return $this->state(function (array $attributes) use ($userId) {
            return [
                'user_id' => $userId,
                'account_id' => Account::factory()->forUser($userId),
                'category_id' => TransactionCategory::factory()->forUser($userId),
            ];
        });
    }
}
