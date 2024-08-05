<?php

namespace App\Filament\Resources\AccountResource\Pages;

use App\Filament\Resources\AccountResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
class ManageAccounts extends ManageRecords
{
    protected static string $resource = AccountResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }


    public function getTabs(): array
    {
        return [
            'All' => Tab::make(),
            'Surplus' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('balance', '>', 0)),
            'Deficit' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('balance', '<', 0)),
            'Empty' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('balance', 0)),
        ];
    }
}
