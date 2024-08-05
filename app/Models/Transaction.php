<?php

namespace App\Models;

use App\Enums\TransactionType;
use App\Observers\TransactionObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use App\Models\Scopes\UserScope;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Filament\Forms;


#[ObservedBy([TransactionObserver::class])]
#[ScopedBy([UserScope::class])]

class Transaction extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'date',
        'amount',
        'description',
        'type',
        'category_id',
        'account_id',
        'user_id',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'date' => 'date',
        'type' => TransactionType::class,
        'amount' => 'decimal:2',
        'category_id' => 'integer',
        'account_id' => 'integer',
        'user_id' => 'integer',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(TransactionCategory::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }


    public static function getForm()
    {
        return [
            Forms\Components\DatePicker::make('date')
                ->native(false)
                ->default(now())
                ->required(),
            Forms\Components\TextInput::make('amount')
                ->required()
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


    public function plannedTransaction(): HasOne
    {
        return $this->hasOne(PlannedTransaction::class, 'linked_transaction_id');
    }
}
