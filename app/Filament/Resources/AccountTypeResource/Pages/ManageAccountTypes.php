<?php

namespace App\Filament\Resources\AccountTypeResource\Pages;

use App\Filament\Resources\AccountTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageAccountTypes extends ManageRecords
{
    protected static string $resource = AccountTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
