<?php

namespace App\Filament\Resources\PlaceResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\AttachAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ActivitiesRelationManager extends RelationManager
{
    protected static string $relationship = 'activities';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                    Forms\Components\TextInput::make('min_price')
                    ->required()
                    ->numeric(),
                    Forms\Components\TextInput::make('max_price')
                    ->required()
                    ->numeric(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('min_price'),
                Tables\Columns\TextColumn::make('max_price')
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                  ->form(fn (AttachAction $action): array => [
                 $action->getRecordSelect(),
                Forms\Components\TextInput::make('min_price')
                ->numeric()
                ->required(),
                Forms\Components\TextInput::make('max_price')
                ->numeric()
                ->required(),
    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
               Tables\Actions\DetachAction::make(),
            ])
            ->bulkActions([
               /* Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),*/
            ]);
    }

   protected function getAttachedNotification(): ?\Filament\Notifications\Notification
{
    return null;
}

protected function getDetachedNotification(): ?\Filament\Notifications\Notification
{
    return null;
}
}
