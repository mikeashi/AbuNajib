<?php

namespace App\Observers;

use App\Models\Account;
use App\Models\Transfer;

class TransferObserver
{
    /**
     * Handle the Transfer "creating" event.
     */
    public function creating(Transfer $account): void
    {
        if (auth()->hasUser()) {
            $account->user_id = auth()->user()->id;
        }
    }

    /**
     * Handle the Transfer "created" event.
     */
    public function created(Transfer $transfer): void
    {
        $transfer->sourceAccount->balance -= $transfer->amount;
        $transfer->sourceAccount->save();

        $transfer->destinationAccount->balance += $transfer->amount;
        $transfer->destinationAccount->save();
    }

    /**
     * Handle the Transfer "updated" event.
     */
    public function updated(Transfer $transfer): void
    {
        $original = $transfer->getOriginal();

        // If the source account changed
        if ($original['source_account_id'] !== $transfer->source_account_id) {
            $oldSource = Account::find($original['source_account_id']);
            $oldSource->balance += $original['amount'];
            $oldSource->save();

            $transfer->sourceAccount->balance -= $transfer->amount;
            $transfer->sourceAccount->save();
        }

        // If the destination account changed
        if ($original['destination_account_id'] !== $transfer->destination_account_id) {
            $oldDestination = Account::find($original['destination_account_id']);
            $oldDestination->balance -= $original['amount'];
            $oldDestination->save();

            $transfer->destinationAccount->balance += $transfer->amount;
            $transfer->destinationAccount->save();
        }

        // If the amount changed but not the accounts
        if ($original['amount'] !== $transfer->amount) {
            $amountDifference = $transfer->amount - $original['amount'];

            $transfer->sourceAccount->balance -= $amountDifference;
            $transfer->sourceAccount->save();

            $transfer->destinationAccount->balance += $amountDifference;
            $transfer->destinationAccount->save();
        }
    }

    /**
     * Handle the Transfer "deleted" event.
     */
    public function deleted(Transfer $transfer): void
    {
        $transfer->sourceAccount->balance += $transfer->amount;
        $transfer->sourceAccount->save();

        $transfer->destinationAccount->balance -= $transfer->amount;
        $transfer->destinationAccount->save();
    }

    /**
     * Handle the Transfer "restored" event.
     */
    public function restored(Transfer $transfer): void
    {
        $transfer->sourceAccount->balance -= $transfer->amount;
        $transfer->sourceAccount->save();

        $transfer->destinationAccount->balance += $transfer->amount;
        $transfer->destinationAccount->save();
    }

    /**
     * Handle the Transfer "force deleted" event.
     */
    public function forceDeleted(Transfer $transfer): void
    {
        $transfer->sourceAccount->balance += $transfer->amount;
        $transfer->sourceAccount->save();

        $transfer->destinationAccount->balance -= $transfer->amount;
        $transfer->destinationAccount->save();
    }
}
