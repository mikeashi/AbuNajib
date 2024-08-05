<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransferResource\Pages;
use App\Filament\Resources\TransferResource\RelationManagers;
use App\Models\Transfer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Filters;

class TransferResource extends Resource
{
    protected static ?string $navigationGroup = 'Tracking';

    protected static ?string $model = Transfer::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrows-right-left';

    public static function form(Form $form): Form
    {
        return $form
            ->schema(Transfer::getForm());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('date')
                    ->date()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->searchable()
                    ->numeric(decimalPlaces: 2)
                    ->money('EUR')
                    //->summarize(Tables\Columns\Summarizers\Sum::make())
                    ->sortable(),
                Tables\Columns\TextColumn::make('sourceAccount.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('destinationAccount.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\IconColumn::make('linked')
                    ->state(function (Transfer $transaction) {
                        return $transaction->plannedTransfer != null;
                    })
                    ->boolean()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('description')
                    ->searchable()
                    ->limit(40)
                    ->placeholder('no description')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Misc::getSourceOrDestinationFilter(),
                Filters\SelectFilter::make('sourceAccount')
                    ->relationship('sourceAccount', 'name')
                    ->multiple()
                    ->native(false)
                    ->searchable()
                    ->preload(),
                Filters\SelectFilter::make('destinationAccount')
                    ->relationship('destinationAccount', 'name')
                    ->native(false)
                    ->multiple()
                    ->searchable()
                    ->preload(),

                Misc::getFromUntilFilter(),

                Filters\Filter::make('linked')
                    ->form(
                        [
                            Forms\Components\Select::make('is_linked')
                                ->options([
                                    'Linked' => 'Linked',
                                    'Not Linked' => 'Not Linked'
                                ])
                                ->label('Linked')
                                ->placeholder("All")
                                ->native(false)
                        ]
                    )
                    ->label('Linked')
                    ->columnSpanFull()
                    ->query(function (Builder $query, array $data): Builder {
                        $query->when(
                            $data['is_linked'] == "Not Linked",
                            fn(Builder $query, $date) => ($query->doesntHave(Transfer::plannedTransfer())),
                        );

                        $query->when(
                            $data['is_linked'] == "Linked",
                            fn(Builder $query, $date) => ($query->has(Transfer::plannedTransfer())),
                        );

                        return $query;
                    })->indicateUsing(function (array $data): ?string {
                        if ($data['is_linked'])
                            return "Showing: " . $data['is_linked'];
                        return null;
                    })
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make()->slideOver(),
                    Tables\Actions\DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->filtersFormColumns(2)->defaultSort('date', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageTransfers::route('/'),
        ];
    }
}
