<?php

namespace App\Filament\Resources\ReportResource\RelationManagers;

use App\Models\TransactionCategory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CategoryBudgetsRelationManager extends RelationManager
{
    protected static string $relationship = 'categoryBudgets';
    protected static ?string $title = 'Transactions';

    public function table(Table $table): Table
    {
        return $table
            ->query(function(){return $this->ownerRecord->getRelatedCategories();})
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('planned')
                    ->numeric(decimalPlaces: 2)
                    ->money('EUR')
                    ->state(function(TransactionCategory $category){
                        return $this->ownerRecord->getPlannedBudgetForCategory($category);
                    }),
                Tables\Columns\TextColumn::make('actual')
                    ->numeric(decimalPlaces: 2)
                    ->money('EUR')
                    ->state(function(TransactionCategory $category){
                        return $this->ownerRecord->getActualTransactionAmountForCategory($category);
                    })
                ]);
    }
}
