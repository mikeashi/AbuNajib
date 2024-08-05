<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use App\Models\Scopes\UserScope;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Filament\Forms;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use App\Observers\TransactionGroupObserver;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[ObservedBy([TransactionGroupObserver::class])]
#[ScopedBy([UserScope::class])]
class TransactionGroup extends Model
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

    public static function getForm()
    {
        return [
            Forms\Components\TextInput::make('name')
                ->required(),
            Forms\Components\Textarea::make('description')
                ->columnSpanFull(),
        ];
    }

    public function categories(): HasMany
    {
        return $this->hasMany(TransactionCategory::class,'group_id');
    }
}
