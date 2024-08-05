<?php

namespace App\Filament\Resources\BudgetResource\Pages;

use App\Filament\Resources\BudgetResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Model;

class ListBudgets extends ListRecords
{
    protected static string $resource = BudgetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->slideOver(false)
            ->disableCreateAnother()
            ->successRedirectUrl(fn(Model $record): string => BudgetResource::getUrl('view', [$record]))
            ,
        ];
    }
}
