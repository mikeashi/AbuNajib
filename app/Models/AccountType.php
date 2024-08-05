<?php

namespace App\Models;

use App\Observers\AccountTypeObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Scopes\UserScope;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;

#[ObservedBy([AccountTypeObserver::class])]
#[ScopedBy([UserScope::class])]
class AccountType extends Model
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


    public function accounts(): HasMany
    {
        return $this->hasMany(Account::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }


    public static function getForm()
    {
        return [
            TextInput::make('name')
                ->required(),
            Textarea::make('description')
                ->columnSpanFull(),
        ];
    }
}
