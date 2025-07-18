<?php

namespace App\Filament\Widgets;

use App\Models\ActivityType;
use App\Models\Place;
use App\Models\Sale;
use App\Models\User;
use Filament\Support\Enums\IconPosition;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsAdminOverview extends BaseWidget
{
   
    protected static ?int $sort = 2;
    protected function getStats(): array
    {
        return [
        Stat::make('Users', User::count())
            ->description('All users from this website')
             ->color('primary'),
        Stat::make('Places',Place::count())
            ->color('primary')
            ->description('All Places from this website'),
        Stat::make('Sales', Sale::count())
            ->description('All sales from this website')
             ->color('primary'),
        Stat::make('Activities',ActivityType::count())
            ->description('All activities from this website')
             ->color('primary'),    
        ];
    }
}
