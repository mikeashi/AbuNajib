<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Observers\TransferObserver;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use App\Models\Scopes\UserScope;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Filament\Forms;

#[ObservedBy([TransferObserver::class])]
#[ScopedBy([UserScope::class])]
class Transfer extends Model
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
        'source_account_id',
        'destination_account_id',
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
        'amount' => 'decimal:2',
        'source_account_id' => 'integer',
        'destination_account_id' => 'integer',
        'user_id' => 'integer',
    ];

    public function sourceAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function destinationAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function plannedTransfer(): HasOne
    {
        return $this->hasOne(PlannedTransfer::class, 'linked_transfer_id');
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
            Forms\Components\Select::make('source_account_id')
                ->native(false)
                ->searchable()
                ->preload()
                ->relationship('sourceAccount', 'name')
                ->required(),
            Forms\Components\Select::make('destination_account_id')
                ->native(false)
                ->searchable()
                ->preload()
                ->relationship('destinationAccount', 'name')
                ->required(),
            Forms\Components\Textarea::make('description')
                ->columnSpanFull(),
        ];
    }
}
