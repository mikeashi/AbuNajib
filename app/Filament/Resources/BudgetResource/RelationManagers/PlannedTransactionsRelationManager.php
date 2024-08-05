<?php

namespace App\Filament\Resources\BudgetResource\RelationManagers;

use App\Enums\TransactionType;
use App\Models\PlannedTransaction;
use App\Models\Transaction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Grouping\Group;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\Summarizers\Sum;

class PlannedTransactionsRelationManager extends RelationManager
{
    protected static string $relationship = 'plannedTransactions';

    protected static ?string $title = 'Transactions';


    public function form(Form $form): Form
    {
        return $form
            ->schema(PlannedTransaction::getForm());
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('linkedTransaction.amount')
                    ->numeric(decimalPlaces: 2)
                    ->money('EUR')
                    ->default("-")
                    ->color(function (PlannedTransaction $transaction, string $state): string {
                        if ($state == '-')
                            return "gray";
                        if ($state == $transaction->amount)
                            return "success";
                        return "danger";
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->numeric(decimalPlaces: 2)
                    ->money('EUR')
                    ->summarize(Sum::make()->numeric(decimalPlaces: 2)->money('EUR'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->searchable()
                    ->badge()
                    ->sortable()
                    ->color(function (TransactionType $state): string {
                        if ($state == TransactionType::Credit)
                            return "success";
                        return "danger";
                    }),
                Tables\Columns\TextColumn::make('category.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('account.name')
                    ->searchable()
                    ->label("Account")
                    ->sortable(),
               
               
                Tables\Columns\TextColumn::make('description')
                    ->searchable()
                    ->limit(40)
                    ->placeholder('no description')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()->slideOver()->label('New Transaction'),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make()
                        ->slideOver()
                        ->form(PlannedTransaction::getForm()),
                    Tables\Actions\Action::make('link')->icon('heroicon-o-link')
                        ->modal()
                        ->form(
                            [
                                Forms\Components\Select::make('linked_transaction_id')
                                    ->key("linked_transaction_id")
                                    ->label("Transaction")
                                    ->native(false)
                                    ->options(
                                        function (RelationManager $livewire, PlannedTransaction $transaction) {
                                            $budget = $livewire->getOwnerRecord();
                                            return Transaction::with('account')
                                                ->whereYear('date', $budget->date->year)
                                                ->whereMonth('date', $budget->date->month)
                                                ->where('account_id', $transaction->account_id)
                                                ->where('category_id', $transaction->category_id)
                                                ->WhereDoesntHave(Transaction::plannedTransaction())
                                                ->get()
                                                ->mapWithKeys(fn($transaction) => [
                                                    $transaction->id => "{$transaction->date->translatedFormat(Infolist::$defaultDateDisplayFormat)}, {$transaction->amount}"
                                                ]);
                                        }
                                    )
                                    ->searchable()
                            ]
                        )->action(function (PlannedTransaction $transaction, $livewire) {
                            $id = $livewire->mountedTableActionsData[0]["linked_transaction_id"];
                            if ($transaction->link($id)) {
                                Notification::make()
                                    ->title('Saved')
                                    ->success()
                                    ->send();
                            } else {
                                Notification::make()
                                    ->title('Error')
                                    ->danger()
                                    ->send();
                            }
                        }),
                    Tables\Actions\DeleteAction::make()
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
            ->defaultGroup('category.group.name')
            ;
    }
}
