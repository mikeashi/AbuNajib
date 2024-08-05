<?php

namespace App\Filament\Resources\BudgetResource\RelationManagers;

use App\Models\PlannedTransfer;
use App\Models\Transfer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Notifications\Notification;
use Filament\Infolists\Infolist;

class PlannedTransfersRelationManager extends RelationManager
{
    protected static string $relationship = 'plannedTransfers';

    protected static ?string $title = 'Transfers';


    public function form(Form $form): Form
    {
        return $form
            ->schema(PlannedTransfer::getForm());
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('linkedTransfer.amount')
                    ->label('Transfer')
                    ->numeric(decimalPlaces: 2)
                    ->money('EUR')
                    ->default("-")
                    ->color(function (PlannedTransfer $transfer, string $state): string {
                        if ($state == '-')
                            return "gray";
                        if ($state == $transfer->amount)
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
                Tables\Columns\TextColumn::make('sourceAccount.name')
                    ->label("Source")
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('destinationAccount.name')
                    ->label("Destination")
                    ->searchable()
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
                Tables\Actions\CreateAction::make()
                    ->slideOver()
                    ->label("New Transfer"),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make()
                        ->slideOver()
                        ->form(PlannedTransfer::getForm()),
                    Tables\Actions\Action::make('link')->icon('heroicon-o-link')
                        ->modal()
                        ->form(
                            [
                                Forms\Components\Select::make('source_account_id')
                                    ->key("source_account_id")
                                    ->label("Transfer")
                                    ->native(false)
                                    ->options(
                                        function (RelationManager $livewire, PlannedTransfer $transfer) {
                                            $budget = $livewire->getOwnerRecord();
                                            return Transfer::with(['sourceAccount', 'destinationAccount'])
                                                ->whereYear('date', $budget->date->year)
                                                ->whereMonth('date', $budget->date->month)
                                                ->where('source_account_id', $transfer->source_account_id)
                                                ->where('destination_account_id', $transfer->destination_account_id)
                                                ->WhereDoesntHave($transfer->plannedTransfer())
                                                ->get()
                                                ->mapWithKeys(fn($transfer) => [
                                                    $transfer->id => "{$transfer->date->translatedFormat(Infolist::$defaultDateDisplayFormat)}, {$transfer->amount}"
                                                ]);
                                        }
                                    )
                                    ->searchable()
                            ]
                        )->action(function (PlannedTransfer $transfer, $livewire) {
                            $id = $livewire->mountedTableActionsData[0]["source_account_id"];
                            if ($transfer->link($id)) {
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
            ]);
    }
}
