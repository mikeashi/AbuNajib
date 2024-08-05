<?php

namespace App\Filament\Resources;

use App\Models\Account;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Tables\Filters;
use Illuminate\Database\Eloquent\Builder;

class Misc
{

    public static function getFromUntilFilter()
    {
        return Filters\Filter::make("date")->form([
            Forms\Components\DatePicker::make("from")
                ->placeholder("Any")
                ->native(false),
            Forms\Components\DatePicker::make("until")
                ->placeholder("Any")
                ->native(false)
        ])->query(function (Builder $query, array $data): Builder {
            return $query
                ->when(
                    $data['from'],
                    fn(Builder $query, $date): Builder => $query->whereDate('date', '>=', $date),
                )
                ->when(
                    $data['until'],
                    fn(Builder $query, $date): Builder => $query->whereDate('date', '<=', $date),
                );
        })->indicateUsing(function (array $data): ?string {
            if ($data['from'] && !$data['until']) {
                return 'Transfers from: ' . Carbon::parse($data['from'])->toFormattedDateString();
            }
            if (!$data['from'] && $data['until']) {
                return 'Transfers until: ' . Carbon::parse($data['until'])->toFormattedDateString();
            }
            if ($data['from'] && $data['until']) {
                return 'Transfers between: ' . Carbon::parse($data['from'])->toFormattedDateString() . ' & ' . Carbon::parse($data['from'])->toFormattedDateString();
            }
            return null;
        })->columnSpanFull();
    }


    public static function getSourceOrDestinationFilter()
    {
        return Filters\Filter::make("account")->form([
            Forms\Components\Select::make("account")
                ->multiple()
                ->options(Account::all()->pluck('name', 'id')->toArray())
                ->native(false)
                ->placeholder("Any")
        ])->query(function (Builder $query, array $data): Builder {
            return $query
                ->when(
                    $data['account'],
                    fn(Builder $query, $id): Builder => $query->whereIn('source_account_id', $id)->orWhereIn('destination_account_id', $id),
                );
        })->indicateUsing(function (array $data): ?string {
            if (!$data['account']) {
                return null;
            }
            return 'Accounts: ' . Account::whereIn('id', $data['account'])->pluck('name')->implode(" & ");
        })
            ->columnSpanFull();
    }
}