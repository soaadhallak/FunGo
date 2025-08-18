<?php

namespace App\Filament\Resources\SaleResource\Pages;

use App\Events\SaleCreated;
use App\Filament\Resources\SaleResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateSale extends CreateRecord
{
    protected static string $resource = SaleResource::class;

    protected function getCreatedNotification(): ?Notification
    {
        return null;
    }

    protected function afterCreate(): void
    {
        $sale=$this->record;
        $title='عرض جديد !';
        $body=' اكتشف العرض الجديد في ' . $sale->place->name;
        $data=['sale_id' => $sale->id , 'place_id'=>$sale->place->id];

        event(new SaleCreated($sale,$title,$body,$data));
    }
}
