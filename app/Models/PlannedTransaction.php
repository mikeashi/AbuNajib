<?php

namespace App\Models;

use App\Enums\TransactionType;
use Filament\Forms\Components\Select;
use Filament\Resources\RelationManagers\RelationManager;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use App\Models\Scopes\UserScope;
use Filament\Forms;
use Filament\Forms\Get;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use App\Observers\PlannedTransactionObserver;

#[ObservedBy([PlannedTransactionObserver::class])]
#[ScopedBy([UserScope::class])]
class PlannedTransaction extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'amount',
        'description',
        'type',
        'budget_id',
        'account_id',
        'category_id',
        'linked_transaction_id',
        'user_id',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'amount' => 'decimal:2',
        'budget_id' => 'integer',
        'account_id' => 'integer',
        'category_id' => 'integer',
        'linked_transaction_id' => 'integer',
        'user_id' => 'integer',
        'type' => TransactionType::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function budget(): BelongsTo
    {
        return $this->belongsTo(Budget::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(TransactionCategory::class);
    }


    public function linkedTransaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }


    public static function getForm()
    {
        return [
            Forms\Components\TextInput::make('amount')
                ->required()
                ->columnSpanFull()
                ->numeric(),
            Forms\Components\Select::make('type')
                ->options(TransactionType::class)
                ->native(false)
                ->default(TransactionType::Debit)
                ->required(),
            Forms\Components\Select::make('category_id')
                ->relationship('category', 'name')
                ->searchable()
                ->createOptionForm(TransactionCategory::getForm())
                ->preload()
                ->required(),
            Forms\Components\Select::make('account_id')
                ->searchable()
                ->preload()
                ->columnSpanFull()
                ->relationship('account', 'name')
                ->required(),
            Forms\Components\Textarea::make('description')
                ->columnSpanFull(),
        ];
    }


    public function link($transaction_id)
    {
        if ($transaction_id == null) {
            $this->linked_transaction_id = null;
            $this->save();
            return true;
        }
        $transaction = Transaction::findOrFail($transaction_id);
        if (
            $transaction->account_id == $this->account_id &&
            $transaction->plannedTransaction == null
        ) {
            $this->linked_transaction_id = $transaction_id;
            $this->save();
            return true;
        }
        return false;
    }


    public function copy(Budget $budget){
        PlannedTransaction::create([
            'amount' => $this->amount,
            'description' => $this->description,
            'type' => $this->type,
            'budget_id' => $budget->id,
            'account_id' => $this->account_id,
            'category_id' => $this->category_id,
            'user_id' => $this->user_id,
        ]);
    }
}
