<?php

namespace App\Filament\Resources\PlaceresourceResource\Widgets;

use App\Models\Place;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsPlaceOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
             Stat::make('Places',Place::count())
            ->description('All Places from this website'),
        ];
    }
}
