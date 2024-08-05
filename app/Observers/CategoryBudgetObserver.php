<?php

namespace App\Observers;
use App\Models\CategoryBudget;

class CategoryBudgetObserver
{
    /**
     * Handle the Budget "creating" event.
     */
    public function creating(CategoryBudget $categoryBudget): void
    {
        if (auth()->hasUser()) {
            $categoryBudget->user_id = auth()->user()->id;
        }
    }
}
