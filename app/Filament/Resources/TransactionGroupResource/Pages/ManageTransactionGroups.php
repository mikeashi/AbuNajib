<?php

namespace App\Filament\Resources\TransactionGroupResource\Pages;

use App\Filament\Resources\TransactionGroupResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageTransactionGroups extends ManageRecords
{
    protected static string $resource = TransactionGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
