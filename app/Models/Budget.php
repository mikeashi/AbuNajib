<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Observers\BudgetObserver;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use App\Models\Scopes\UserScope;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Filament\Forms;
use Illuminate\Support\Facades\DB;


#[ObservedBy([BudgetObserver::class])]
#[ScopedBy([UserScope::class])]

class Budget extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'date',
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
        'user_id' => 'integer',
    ];


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function getForm()
    {
        return [
            Forms\Components\DatePicker::make('date')
                ->default(now())
                ->displayFormat('F, Y')
                ->native(false)
                ->required(),
        ];
    }


    public function accounts(): HasMany
    {
        return $this->hasMany(BudgetAccount::class);
    }


    public function plannedTransfers(): HasMany
    {
        return $this->hasMany(PlannedTransfer::class);
    }

    public function plannedTransactions(): HasMany
    {
        return $this->hasMany(PlannedTransaction::class);
    }

    public function categoryBudgets(): HasMany
    {
        return $this->hasMany(CategoryBudget::class);
    }

    public function getRelatedCategories()
    {
        $planned_transactions = $this->plannedTransactions->pluck('category_id');
        $planned_categories = $this->categoryBudgets->pluck('category_id');
        $transactions = Transaction::with('category')
            ->whereYear('date', $this->date->year)
            ->whereMonth('date', $this->date->month)
            ->get()
            ->pluck('category_id');

        $categories = collect([$planned_transactions, $planned_categories, $transactions])->flatten()->unique();

        return TransactionCategory::whereIn('id', $categories);
    }


    public function getPlannedBudgetForCategory($category)
    {
        $planned_transactions = $this->plannedTransactions->where('category_id', $category->id)->pluck('amount')->sum();
        $planned_categories = $this->categoryBudgets->where('category_id', $category->id)->pluck('amount')->sum();

        return $planned_categories + $planned_transactions;
    }


    public function getActualTransactionAmountForCategory($category)
    {
        return Transaction::with('category')
            ->whereYear('date', $this->date->year)
            ->whereMonth('date', $this->date->month)
            ->where('category_id', $category->id)
            ->get()
            ->pluck('amount')->sum();
    }


    public function copy(){
        try{
            $budget = Budget::create([
                'user_id' => $this->user_id,
                'date' => $this->date->addMonth()
            ]);

            $this->plannedTransactions->each(fn(PlannedTransaction $plannedTransaction) => $plannedTransaction->copy($budget));
            $this->plannedTransfers->each(fn(PlannedTransfer $plannedTransfer) => $plannedTransfer->copy($budget));
            $this->categoryBudgets->each(fn(CategoryBudget $categoryBudget) => $categoryBudget->copy($budget));
            return true;
        }
        catch(Exception $e){
            return false;
        }
    }
}
