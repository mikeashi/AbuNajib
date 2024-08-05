<?php

namespace App\Filament\Resources\TransactionResource\Pages;

use App\Enums\TransactionType;
use App\Filament\Resources\TransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
class ManageTransactions extends ManageRecords
{
    protected static string $resource = TransactionResource::class;

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
            'Credit' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('type', TransactionType::Credit)),
            'Debit' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('type', TransactionType::Debit)),
        ];
    }
}
