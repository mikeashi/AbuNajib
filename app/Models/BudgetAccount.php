<?php

namespace App\Models;

use App\Enums\TransactionCategoryType;
use App\Enums\TransactionType;
use App\Observers\BudgetAccountObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use App\Models\Scopes\UserScope;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;

#[ObservedBy([BudgetAccountObserver::class])]
#[ScopedBy([UserScope::class])]
class BudgetAccount extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'starting_balance',
        'account_id',
        'user_id',
        'budget_id',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'starting_balance' => 'decimal:2',
        'account_id' => 'integer',
        'budget_id' => 'integer',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    public function budget(): BelongsTo
    {
        return $this->belongsTo(Budget::class);
    }


    public function calculatePlannedBalance()
    {
        $credit = 0;
        $debit = 0;

        $credit += $this->budget->plannedTransfers->where('destination_account_id', $this->account_id)->pluck('amount')->sum();

        // Credit Planned Transactions
        $credit += $this->budget
            ->plannedTransactions()
            ->with('category')
            ->where('account_id', $this->account_id)
            ->where('type', TransactionType::Credit)
            ->get()->pluck('amount')->sum();


        // -------------------------

        $debit += $this->budget->plannedTransfers->where('source_account_id', $this->account_id)->pluck('amount')->sum();

        // Debit Planned Transactions
        $debit += $this->budget
            ->plannedTransactions()
            ->with('category')
            ->where('account_id', $this->account_id)
            ->where('type', TransactionType::Debit)
            ->get()->pluck('amount')->sum();



        return $this->starting_balance + $credit - $debit;
    }


    public function calculateActualBalance()
    {
        return $this->starting_balance + $this->account->calculateChangeForBudget($this->budget);
    }

}
