<?php

namespace App\Filament\Resources\BudgetResource\RelationManagers;

use App\Models\CategoryBudget;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Grouping\Group;
class CategoryBudgetsRelationManager extends RelationManager
{
    protected static string $relationship = 'categoryBudgets';

    protected static ?string $title = 'Categories';

    public function form(Form $form): Form
    {
        return $form
            ->schema(CategoryBudget::getForm());
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('category.name')
                ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('amount')
                    ->numeric(decimalPlaces: 2)
                    ->money('EUR')
                    ->summarize(Sum::make()->numeric(decimalPlaces: 2)->money('EUR'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('account.name')
                    ->label("Account")
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('description')
                    ->searchable()
                    ->searchable()
                    ->limit(40)
                    ->placeholder('no description')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()->slideOver()->label('New Category Budget'),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make()->slideOver(),
                    Tables\Actions\DeleteAction::make(),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])->groups([
                    Group::make('category.group.name')
                        ->titlePrefixedWithLabel(false)
                        ->collapsible(),
                ])
            //->groupingSettingsHidden()
            ->defaultGroup('category.group.name');
    }
}
