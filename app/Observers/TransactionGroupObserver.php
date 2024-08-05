<?php

namespace App\Observers;

use App\Models\TransactionGroup;

class TransactionGroupObserver
{
    /**
     * Handle the TransactionGroup "creating" event.
     */
    public function creating(TransactionGroup $transactionGroup): void
    {
        if (auth()->hasUser()) {
            $transactionGroup->user_id = auth()->user()->id;
        }
    }
}
