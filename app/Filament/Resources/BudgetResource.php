<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BudgetResource\Pages;
use App\Filament\Resources\BudgetResource\RelationManagers;
use App\Models\Budget;
use Exception;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Notifications\Notification;

class BudgetResource extends Resource
{
    protected static ?string $navigationGroup = 'Planning';

    protected static ?string $model = Budget::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    public static function form(Form $form): Form
    {
        return $form
            ->schema(Budget::getForm());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('date')
                    ->date("F, Y")
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                Misc::getFromUntilFilter(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make()->slideOver(false),
                    Tables\Actions\Action::make("replicate")
                    ->requiresConfirmation()
                    ->label('Replicate')
                    ->icon('heroicon-m-square-2-stack')
                    ->action(function(Budget $budget){
                        if($budget->copy()){
                                Notification::make()
                                    ->success()
                                    ->title('Budget replicated')
                                    ->body('The Budget has been replicated successfully.')
                                    ->send();
                        }else{
                                Notification::make()
                                    ->danger()
                                    ->title('Error')
                                    ->body('The Budget already exists.')
                                    ->send();
                        }
                    }),
                    Tables\Actions\DeleteAction::make(),
                   
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\PlannedTransfersRelationManager::class,
            RelationManagers\PlannedTransactionsRelationManager::class,
            RelationManagers\CategoryBudgetsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBudgets::route('/'),
            //'create' => Pages\CreateBudget::route('/create'),
            //'edit' => Pages\EditBudget::route('/{record}/edit'),
            'view' => Pages\ViewBudget::route('/{record}'),
        ];
    }
}
