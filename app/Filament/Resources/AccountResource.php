<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AccountResource\Pages;
use App\Filament\Resources\AccountResource\RelationManagers;
use App\Models\Account;
use App\Models\AccountType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AccountResource extends Resource
{
    protected static ?string $navigationGroup = 'Tracking';

    protected static ?string $model = Account::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-library';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required(),
                Forms\Components\TextInput::make('balance')
                    ->required()
                    ->numeric()
                    ->disabled()
                    ->default(0),
                Forms\Components\Textarea::make('description')
                    ->columnSpanFull(),
                Forms\Components\Select::make('account_type_id')
                    ->relationship('accountType', 'name')
                    ->native(false)
                    ->searchable()
                    ->preload()
                    ->createOptionForm(AccountType::getForm())
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                     ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('balance')
                    ->numeric(decimalPlaces: 2)
                    ->money('EUR')
                    ->color(function (string $state): string {
                        if ($state > 0)
                            return "success";
                        if ($state < 0)
                            return "danger";
                        return "gray";
                    })
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('accountType.name')
                    ->searchable()
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('description')
                    ->searchable()
                    ->limit(40)
                    ->placeholder('no description')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filters\SelectFilter::make('accountType')
                    ->relationship('accountType', 'name')
                    ->multiple()
                    ->native(false)
                    ->searchable()
                    ->preload(),
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
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageAccounts::route('/'),
        ];
    }
}
