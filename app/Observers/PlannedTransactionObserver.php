<?php

namespace App\Observers;
use App\Models\PlannedTransaction;

class PlannedTransactionObserver
{
    /**
     * Handle the Budget "creating" event.
     */
    public function creating(PlannedTransaction $transaction): void
    {
        if (auth()->hasUser()) {
            $transaction->user_id = auth()->user()->id;
        }
    }
}
