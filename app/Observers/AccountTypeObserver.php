<?php

namespace App\Observers;

use App\Models\AccountType;

class AccountTypeObserver
{
    /**
     * Handle the AccountType "creating" event.
     */
    public function creating(AccountType $accountType): void
    {
        if (auth()->hasUser()) {
            $accountType->user_id = auth()->user()->id;
        }
    }


}
