<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Budget;
use App\Models\CategoryBudget;
use App\Models\TransactionCategory;
use App\Models\User;

class CategoryBudgetFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CategoryBudget::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'planned_amount' => $this->faker->randomFloat(2, 0, 99999999999.99),
            'actual_amount' => $this->faker->randomFloat(2, 0, 99999999999.99),
            'user_id' => User::factory(),
            'budget_id' => Budget::factory(),
            'category_id' => TransactionCategory::factory(),
        ];
    }

    public function forUser($userId)
    {
        return $this->state(function (array $attributes) use ($userId) {
            return [
                'user_id' => $userId,
                'budget_id' => Budget::factory()->forUser($userId),
                'category_id' => TransactionCategory::factory()->forUser($userId),
            ];
        });
    }
}
