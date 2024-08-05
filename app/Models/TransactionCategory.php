<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use App\Models\Scopes\UserScope;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use App\Observers\TransactionCategoryObserver;
use Filament\Forms;

#[ObservedBy([TransactionCategoryObserver::class])]
#[ScopedBy([UserScope::class])]
class TransactionCategory extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'group_id',
        'user_id',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(TransactionGroup::class,'group_id');
    }


    public static function getForm()
    {
        return [
            Forms\Components\TextInput::make('name')
                ->required(),
            Forms\Components\Select::make('group_id')
                ->relationship('group','name')
                ->searchable()
                ->preload()
                ->required(),
            Forms\Components\Textarea::make('description')
                ->columnSpanFull(),
        ];
    }
}
