<?php

namespace App\Filament\Resources\SaleResource\Pages;

use App\Filament\Resources\SaleResource;
use App\Models\Sale;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ListRecords\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListSales extends ListRecords
{
    protected static string $resource = SaleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

       public function getTabs():array
    {
        return [
            'All'=>Tab::make(),
            'This week'=>Tab::make()
            ->modifyQueryUsing(fn (Builder $query) => $query->where('date_start','>=',now()->subWeek()))
            ->badge(Sale::query()->where('date_start','>=',now()->subWeek())->count()),
            'This Month'=>Tab::make()
            ->modifyQueryUsing(fn (Builder $query) => $query->where('date_start','>=',now()->subMonth()))
            ->badge(Sale::query()->where('date_start','>=',now()->subMonth())->count()),
        ];

    }
}
