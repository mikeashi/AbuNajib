<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransactionCategoryResource\Pages;
use App\Filament\Resources\TransactionCategoryResource\RelationManagers;
use App\Models\TransactionCategory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Enums\TransactionCategoryType;
use Filament\Tables\Filters\SelectFilter;

class TransactionCategoryResource extends Resource
{
    protected static ?string $navigationGroup = 'Settings';

    protected static ?string $model = TransactionCategory::class;

    protected static ?string $navigationIcon = 'heroicon-o-swatch';

    public static function form(Form $form): Form
    {
        return $form
            ->schema(TransactionCategory::getForm());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('group.name')
                    ->sortable()
                    ->searchable()
                ,
                Tables\Columns\TextColumn::make('description')->searchable()->limit(40)->placeholder('no description')->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('group')->relationship('group', 'name')->native(false)->searchable()->preload()->multiple()
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
            'index' => Pages\ManageTransactionCategories::route('/'),
        ];
    }
}
