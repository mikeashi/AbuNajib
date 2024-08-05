<?php

namespace App\Observers;
use App\Models\BudgetAccount;

class BudgetAccountObserver
{
    /**
     * Handle the Budget "creating" event.
     */
    public function creating(BudgetAccount $budgetAccount): void
    {
        if (auth()->hasUser()) {
            $budgetAccount->user_id = auth()->user()->id;
        }
    }
}
