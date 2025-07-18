<?php

namespace App\Filament\Widgets;

use App\Models\Place;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class PlacesChart extends ChartWidget
{
    protected static ?string $heading = 'Chart';
    protected static ?int $sort = 4;

    protected function getData(): array
    {
       $data = Trend::model(Place::class)
        ->between(
            start: now()->startOfMonth(),
            end: now()->endOfMonth(),
        )
        ->perDay()
        ->count();

    return [
        'datasets' => [
            [
                'label' => 'Places',
                'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
            ],
        ],
        'labels' => $data->map(fn (TrendValue $value) => $value->date),
    ];
    }

    public function getDescription(): ?string
        {
            return 'The number of places published per day.';
        }

    protected function getType(): string
    {
        return 'line';
    }
}
