<?php

namespace App\Filament\Resources\ReportResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Models\BudgetAccount;

class AccountsRelationManager extends RelationManager
{
    protected static string $relationship = 'accounts';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('starting_balance')
                    ->numeric()
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('account.name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('starting_balance')
                    ->label('Starting')
                    ->numeric(decimalPlaces: 2)
                    ->money('EUR')
                    ->color("gray")
                    ->searchable()
                    ->sortable(),
                TextColumn::make('planned')
                    ->label('Planned')
                    ->state(fn(BudgetAccount $budgetAccount) => $budgetAccount->calculatePlannedBalance())
                    ->numeric(decimalPlaces: 2)
                    ->money('EUR')
                    ->color(function (BudgetAccount $budgetAccount, string $state): string {
                        if ($state == $budgetAccount->starting_balance)
                            return "gray";
                        return "white";
                    })
                    ->searchable()
                    ->sortable(),
                TextColumn::make('real')
                    ->label('Real')
                    ->state(fn(BudgetAccount $budgetAccount) => $budgetAccount->calculateActualBalance())
                    ->numeric(decimalPlaces: 2)
                    ->money('EUR')
                    ->color(function (BudgetAccount $budgetAccount, string $state): string {
                        if ($state == $budgetAccount->calculatePlannedBalance())
                        {
                            if($state == $budgetAccount->starting_balance){
                                return "gray";
                            }

                            return "success";
                        }
                        return "danger";
                    })
                    ->searchable()
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                ]),
            ]);
    }
}
