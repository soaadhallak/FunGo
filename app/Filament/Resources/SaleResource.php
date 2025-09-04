<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SaleResource\Pages;
use App\Filament\Resources\SaleResource\RelationManagers;
use App\Models\Sale;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Form;
use Filament\Infolists\Components\Section;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
 use Filament\Infolists\Infolist;
 use Filament\Infolists\Components\TextEntry;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;

class SaleResource extends Resource
{
    protected static ?string $model = Sale::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-trending-down';


    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'warning';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Sale In formation')
                ->schema([
                Forms\Components\Textarea::make('title')
                    ->required(), 
                Forms\Components\Textarea::make('body')
                    ->required(),               
                Forms\Components\DateTimePicker::make('date_start')
                    ->required()
                    ->native(false)
                    ->displayFormat('d-m-y'),
                Forms\Components\DateTimePicker::make('date_end')
                    ->required()
                    ->native(false)
                    ->displayFormat('d-m-y'),
                Forms\Components\Select::make('place_id')
                ->relationship(name:'place',titleAttribute:'name')
                ->searchable()
                ->native(false)
                ->preload()
                ->required(),
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('body')
                    ->sortable()
                    ->searchable(),    
                Tables\Columns\TextColumn::make('date_start')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('date_end')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('notified_at')
                    ->dateTime()
                    ->sortable()
                    ->hidden(true),
                Tables\Columns\TextColumn::make('place.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('place')
                ->relationship('place','name')
                ->searchable()
                ->preload(),
                Filter::make('created_at')
                 ->form([
                    DatePicker::make('created_from')
                    ->native(false),
                    DatePicker::make('created_until')
                    ->native(false),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                     return $query
                ->when(
                $data['created_from'],
                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                    )
                ->when(
                $data['created_until'],
                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
            );
    })
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

      public static function infolist(Infolist $infolist): Infolist    
        {
            return $infolist
            ->schema([
                Section::make('Sale Information')
                ->schema([
                TextEntry::make('title')->label('Title'),
                TextEntry::make('body')->label('Body'),
                TextEntry::make('date_start')->label('Start'),
                TextEntry::make('date_end')->label('End'),
                TextEntry::make('place.name')->label('Place Name')
            ])
              
            ]);
        }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSales::route('/'),
            'create' => Pages\CreateSale::route('/create'),
            //'view' => Pages\ViewSale::route('/{record}'),
            'edit' => Pages\EditSale::route('/{record}/edit'),
        ];
    }
}
