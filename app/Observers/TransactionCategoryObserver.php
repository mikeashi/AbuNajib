<?php

namespace App\Observers;

use App\Models\TransactionCategory;

class TransactionCategoryObserver
{
    /**
     * Handle the TransactionCategory "creating" event.
     */
    public function creating(TransactionCategory $account): void
    {
        if (auth()->hasUser()) {
            $account->user_id = auth()->user()->id;
        }
    }
}
