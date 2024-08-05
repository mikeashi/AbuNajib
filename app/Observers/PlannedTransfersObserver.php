<?php

namespace App\Observers;
use App\Models\PlannedTransfer;

class PlannedTransfersObserver
{
    /**
     * Handle the Budget "creating" event.
     */
    public function creating(PlannedTransfer $transaction): void
    {
        if (auth()->hasUser()) {
            $transaction->user_id = auth()->user()->id;
        }
    }
}
