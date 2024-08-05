<?php

namespace App\Filament\Resources;

use App\Enums\TransactionType;
use App\Filament\Resources\TransactionResource\Pages;
use App\Filament\Resources\TransactionResource\RelationManagers;
use App\Models\Transaction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Filters;

class TransactionResource extends Resource
{
    protected static ?string $navigationGroup = 'Tracking';
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    public static function form(Form $form): Form
    {
        return $form
            ->schema(Transaction::getForm());
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
                    ->numeric()
                    ->searchable()
                    ->numeric(decimalPlaces: 2)
                    ->money('EUR')
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
                    ->sortable(),
                Tables\Columns\IconColumn::make('linked')
                    ->state(function (Transaction $transaction) {
                        return $transaction->plannedTransaction != null;
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
                Filters\SelectFilter::make("account")->relationship('account', 'name')->searchable()->preload()->multiple()->native(false),
                Filters\SelectFilter::make("Group")->relationship('category.group', 'name')->searchable()->preload()->multiple()->native(false),
                Filters\SelectFilter::make("category")->relationship('category', 'name')->searchable()->preload()->multiple()->native(false),
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
                    ->query(function (Builder $query, array $data): Builder {
                        $query->when(
                            $data['is_linked'] == "Not Linked",
                            fn(Builder $query, $date) => ($query->doesntHave(Transaction::plannedTransaction())),
                        );

                        $query->when(
                            $data['is_linked'] == "Linked",
                            fn(Builder $query, $date) => ($query->has(Transaction::plannedTransaction())),
                        );

                        return $query;
                    })->indicateUsing(function (array $data): ?string {
                        if ($data['is_linked'])
                            return "Showing: " . $data['is_linked'];
                        return null;
                    }),
                Misc::getFromUntilFilter(),
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
            ])->filtersFormColumns(2)->groups([
                    'category.group.name',
                ])->defaultSort('date', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageTransactions::route('/'),
        ];
    }
}
