<?php

namespace App\Observers;

use App\Enums\TransactionCategoryType;
use App\Enums\TransactionType;
use App\Models\Account;
use App\Models\Transaction;
use App\Models\TransactionCategory;

class TransactionObserver
{
    /**
     * Handle the Transfer "creating" event.
     */
    public function creating(Transaction $account): void
    {
        if (auth()->hasUser()) {
            $account->user_id = auth()->user()->id;
        }
    }

    /**
     * Handle the Transaction "created" event.
     */
    public function created(Transaction $transaction): void
    {
        if($transaction->type == TransactionType::Credit){
            $transaction->account->balance += $transaction->amount;
        }else{
            $transaction->account->balance -= $transaction->amount;
        }
        $transaction->account->save();
    }

    /**
     * Handle the Transaction "updated" event.
     */
    public function updated(Transaction $transaction): void
    {
        $original = $transaction->getOriginal();
        $originalAccountId = $original['account_id'];
        $originalAccount = Account::find($originalAccountId);

        if ($original['type'] == TransactionType::Credit) {
            $originalAccount->balance -= $original['amount'];
            $originalAccount->save();
        } else {
            $originalAccount->balance += $original['amount'];
            $originalAccount->save();
        }

        // Apply the updated transaction effects
        if ($transaction->type == TransactionType::Credit) {
            $transaction->account->balance += $transaction->amount;
        } else {
            $transaction->account->balance -= $transaction->amount;
        }

        // Save changes to the new account
        $transaction->account->save();
    }

    /**
     * Handle the Transaction "deleted" event.
     */
    public function deleted(Transaction $transaction): void
    {
        if ($transaction->type == TransactionType::Credit) {
            $transaction->account->balance -= $transaction->amount;
        } else {
            $transaction->account->balance += $transaction->amount;
        }
        $transaction->account->save();
    }

    /**
     * Handle the Transaction "restored" event.
     */
    public function restored(Transaction $transaction): void
    {
        if ($transaction->type == TransactionType::Credit) {
            $transaction->account->balance += $transaction->amount;
        } else {
            $transaction->account->balance -= $transaction->amount;
        }
        $transaction->account->save();
    }

    /**
     * Handle the Transaction "force deleted" event.
     */
    public function forceDeleted(Transaction $transaction): void
    {
        // This logic mirrors the deleted event since force deleting should also reverse the transaction's effect.
        if ($transaction->type == TransactionType::Credit) {
            $transaction->account->balance -= $transaction->amount;
        } else {
            $transaction->account->balance += $transaction->amount;
        }
        $transaction->account->save();
    }
}
