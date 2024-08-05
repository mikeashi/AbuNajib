<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Scopes\UserScope;
use Filament\Forms;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use App\Observers\CategoryBudgetObserver;

#[ObservedBy([CategoryBudgetObserver::class])]
#[ScopedBy([UserScope::class])]
class CategoryBudget extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'amount',
        'account_id',
        'budget_id',
        'category_id',
        'user_id',
        'description'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'planned_amount' => 'decimal:2',
        'actual_amount' => 'decimal:2',
        'budget_id' => 'integer',
        'category_id' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function budget(): BelongsTo
    {
        return $this->belongsTo(Budget::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(TransactionCategory::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public static function getForm()
    {
        return [
            Forms\Components\Select::make('category_id')
                ->relationship('category', 'name')
                ->label('Category')
                ->preload()
                ->native(false)
                ->searchable()
                ->required(),
            Forms\Components\Select::make('account_id')
                ->native(false)
                ->searchable()
                ->preload()
                ->relationship('account', 'name')
                ->required(),
            Forms\Components\TextInput::make('amount')
                ->required()
                ->columnSpanFull()
                ->numeric(),
            Forms\Components\Textarea::make('description')
                ->columnSpanFull(),
        ];
    }
    public function copy(Budget $budget)
    {
        CategoryBudget::create([
            'amount' => $this->amount,
            'description' => $this->description,
            'budget_id' => $budget->id,
            'category_id' => $this->category_id,
            'account_id' => $this->account_id,
            'user_id' => $this->user_id,
        ]);
    }

}
