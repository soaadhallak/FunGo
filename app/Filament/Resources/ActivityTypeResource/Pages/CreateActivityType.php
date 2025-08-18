<?php

namespace App\Filament\Resources\ActivityTypeResource\Pages;

use App\Filament\Resources\ActivityTypeResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateActivityType extends CreateRecord
{
    protected static string $resource = ActivityTypeResource::class;

    protected function getCreatedNotification(): ?Notification
    {
        return null;
    }
}
