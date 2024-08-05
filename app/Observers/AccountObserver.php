<?php

namespace App\Observers;

use App\Models\Account;

class AccountObserver
{
    /**
     * Handle the Account "creating" event.
     */
    public function creating(Account $account): void
    {
        if (auth()->hasUser()) {
            $account->user_id = auth()->user()->id;
        }
    }
}
