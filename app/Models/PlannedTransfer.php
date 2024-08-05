<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Scopes\UserScope;
use Filament\Forms;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use App\Observers\PlannedTransfersObserver;

#[ObservedBy([PlannedTransfersObserver::class])]
#[ScopedBy([UserScope::class])]
class PlannedTransfer extends Model
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
        'budget_id',
        'source_account_id',
        'destination_account_id',
        'linked_transfer_id',
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
        'source_account_id' => 'integer',
        'destination_account_id' => 'integer',
        'linked_transfer_id' => 'integer',
        'user_id' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function budget(): BelongsTo
    {
        return $this->belongsTo(Budget::class);
    }

    public function sourceAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'source_account_id');
    }

    public function destinationAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'destination_account_id');
    }

    public function linkedTransfer(): BelongsTo
    {
        return $this->belongsTo(Transfer::class, 'linked_transfer_id');
    }


    public static function getForm()
    {
        return [
            Forms\Components\Select::make('source_account_id')
                ->native(false)
                ->searchable()
                ->preload()
                ->columnSpan(1)
                ->relationship('sourceAccount', 'name')
                ->required(),
            Forms\Components\Select::make('destination_account_id')
                ->native(false)
                ->searchable()
                ->columnSpan(1)
                ->preload()
                ->relationship('destinationAccount', 'name')
                ->required(),
            Forms\Components\TextInput::make('amount')
                ->required()
                ->columnSpanFull()
                ->numeric(),
            Forms\Components\Textarea::make('description')
                ->columnSpanFull(),
        ];
    }


    public function link($transfer_id)
    {
        if ($transfer_id == null) {
            $this->linked_transfer_id = null;
            $this->save();
            return true;
        }
        $transfer = Transfer::findOrFail($transfer_id);
        if (
            $transfer->source_account_id == $this->source_account_id &&
            $transfer->destination_account_id == $this->destination_account_id &&
            $transfer->plannedTransfer == null
        ) {
            $this->linked_transfer_id = $transfer_id;
            $this->save();
            return true;
        }
        return false;
    }

    public function copy(Budget $budget)
    {
        PlannedTransfer::create([
            'amount' => $this->amount,
            'description' => $this->description,
            'budget_id' => $budget->id,
            'source_account_id' => $this->source_account_id,
            'destination_account_id' => $this->destination_account_id,
            'user_id' => $this->user_id,
        ]);
    }
}
