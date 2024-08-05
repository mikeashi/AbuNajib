<?php

namespace App\Observers;

use App\Models\Account;
use App\Models\Budget;
use Carbon\Carbon;


class BudgetObserver
{
    /**
     * Handle the Budget "creating" event.
     */
    public function creating(Budget $budget): void
    {
        if (auth()->hasUser()) {
            $budget->user_id = auth()->user()->id;
        }

        $budget->date = Carbon::parse($budget->date)->startOfMonth();
    }

    public function created(Budget $budget): void
    {
        Account::createBudgetAccounts($budget);
    }
}
