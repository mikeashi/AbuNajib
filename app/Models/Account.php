<?php

namespace App\Models;

use App\Enums\TransactionType;
use App\Observers\AccountObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Scopes\UserScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[ObservedBy([AccountObserver::class])]
#[ScopedBy([UserScope::class])]
class Account extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'balance',
        'description',
        'account_type_id',
        'user_id',
    ];


    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'balance' => 'decimal:2',
        'account_type_id' => 'integer',
        'user_id' => 'integer',
    ];

    public function accountType(): BelongsTo
    {
        return $this->belongsTo(AccountType::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function createBudgetAccount(Budget $budget)
    {
        BudgetAccount::create([
            'budget_id' => $budget->id,
            'account_id' => $this->id,
            'starting_balance' => $this->balance,
            'user_id' => $budget->user_id,
        ]);
    }

    public function sourceTransfers(): HasMany
    {
        return $this->hasMany(Transfer::class, 'source_account_id');
    }

    public function destinationTransfers(): HasMany
    {
        return $this->hasMany(Transfer::class, 'destination_account_id');
    }


    public function Transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }


    public static function createBudgetAccounts(Budget $budget)
    {
        Account::all()
            ->each(fn(Account $account) => $account->createBudgetAccount($budget));
    }


    public function calculateTotalDebitTransfers(Budget $budget){
       return Transfer::with('account')
        ->whereYear('date', $budget->date->year)
        ->whereMonth('date', $budget->date->month)
        ->where('source_account_id', $this->id)
        ->pluck('amount')->sum();
    }

    public function calculateTotalCreditTransfers(Budget $budget)
    {
        return Transfer::with('account')
            ->whereYear('date', $budget->date->year)
            ->whereMonth('date', $budget->date->month)
            ->where('destination_account_id', $this->id)
            ->pluck('amount')->sum();
    }


    public function calculateTotalDebitTransactions(Budget $budget)
    {
        return Transaction::with('account')
            ->whereYear('date', $budget->date->year)
            ->whereMonth('date', $budget->date->month)
            ->where('account_id', $this->id)
            ->where('type', TransactionType::Debit)
            ->pluck('amount')->sum();
    }

    public function calculateTotalCreditTransactions(Budget $budget)
    {
        return Transaction::with('account')
            ->whereYear('date', $budget->date->year)
            ->whereMonth('date', $budget->date->month)
            ->where('account_id', $this->id)
            ->where('type', TransactionType::Credit)
            ->pluck('amount')->sum();
    }


    public function calculateChangeForBudget(Budget $budget){
        $totalDebitTransfers = $this->calculateTotalDebitTransfers($budget);
        $totalCreditTransfers = $this->calculateTotalCreditTransfers($budget);

        $totalDebitTransaction = $this->calculateTotalDebitTransactions($budget);
        $totalCreditTransaction = $this->calculateTotalCreditTransactions($budget);


        return $totalCreditTransaction + $totalCreditTransfers - $totalDebitTransaction - $totalDebitTransfers;
    }
}
