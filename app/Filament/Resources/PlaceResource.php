<?php

namespace App\Filament\Resources;

use Dotswan\MapPicker\Fields\Map;
use App\Filament\Resources\PlaceResource\Pages;
use App\Filament\Resources\PlaceResource\RelationManagers;
use App\Filament\Resources\PlaceResource\RelationManagers\ActivitiesRelationManager;
use App\Models\ActivityType;
use App\Models\Place;
use Closure;
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
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Set;
use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;


class PlaceResource extends Resource
{
    protected static ?string $model = Place::class;

    protected static ?string $navigationIcon = 'heroicon-o-home-modern';

  

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'warning';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('place information')
                ->schema([   
                 Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('address')
                    ->required(),
                Forms\Components\Textarea::make('description')
                    ->required()
                    ->columnSpanFull(),
                 Select::make('governorate')
                ->label('المحافظة')
                ->options([
                    'damascus'   => 'دمشق',
                    'aleppo'     => 'حلب',
                    'homs'       => 'حمص',
                    'hama'       => 'حماة',
                    'latakia'    => 'اللاذقية',
                    'tartous'    => 'طرطوس',
                    'banias'     => 'بانياس',
                    'idlib'      => 'إدلب',
                    'deraa'      => 'درعا',
                    'sweida'     => 'السويداء',
                    'raqqa'      => 'الرقة',
                    'deir_ezzor' => 'دير الزور',
                    'hasakah'    => 'الحسكة',
                    'qamisli'    => 'القامشلي',
                    'palmyra'    => 'تدمر',
                ])
                ->reactive()
                ->afterStateUpdated(function (Set $set, $state, $livewire) {
                    $coordinates = [
                        'damascus'   => ['lat' => 33.5138, 'lng' => 36.2765],
                        'aleppo'     => ['lat' => 36.2021, 'lng' => 37.1343],
                        'homs'       => ['lat' => 34.7306, 'lng' => 36.7090],
                        'hama'       => ['lat' => 35.1318, 'lng' => 36.7578],
                        'latakia'    => ['lat' => 35.5310, 'lng' => 35.7900],
                        'tartus'     => ['lat' => 34.8890, 'lng' => 35.8866],
                        'idlib'      => ['lat' => 35.9306, 'lng' => 36.6339],
                        'raqqa'      => ['lat' => 35.9500, 'lng' => 39.0167],
                        'deir_ezzor' => ['lat' => 35.3333, 'lng' => 40.1500],
                        'daraa'      => ['lat' => 32.6189, 'lng' => 36.1021],
                        'sweida'     => ['lat' => 32.7086, 'lng' => 36.5665],
                        'hasakah'    => ['lat' => 36.4833, 'lng' => 40.7500],
                        'qamisli'    => ['lat' => 37.0522, 'lng' => 41.2220],
                        'palmyra'    => ['lat' => 34.5609, 'lng' => 38.2766],
                        'banias'     => ['lat' => 35.1819, 'lng' => 35.9487],
                    ];

                    if (isset($coordinates[$state])) {
                        $set('latitude', $coordinates[$state]['lat']);
                        $set('longitude', $coordinates[$state]['lng']);
                        $set('location', [
                            'lat' => $coordinates[$state]['lat'],
                            'lng' => $coordinates[$state]['lng'],
                        ]);
                        $livewire->dispatch('refreshMap');
                    }
                }),

            TextInput::make('latitude')->reactive(),
            TextInput::make('longitude')->reactive(),
                SpatieMediaLibraryFileUpload::make('avatar')
                ->multiple()
                ->image()
                ->maxFiles(10)
                ->collection('places')
                ->disk('places')]), 
                Forms\Components\Section::make('Activities')
                ->schema([
                Repeater::make('activitiesData')
                ->relationship()
                ->schema([
                    Select::make('activity_type_id')
                        ->label('Activity')
                        ->options(ActivityType::all()->pluck('name', 'id'))
                        ->required(),
                    TextInput::make('min_price')->numeric()->required(),
                    TextInput::make('max_price')->numeric()->required(),
                ])
                ->mutateRelationshipDataBeforeCreateUsing(function ($data) {
                    return $data;
                })
                ->mutateRelationshipDataBeforeSaveUsing(function ($data) {
                    return $data;
                })
                 ->columns(3)
                ->columnSpanFull()]),
          Map::make('location')
        ->label('الموقع على الخريطة')
                ->zoom(13)
                ->clickable(true)
                ->draggable()
                ->reactive()
                ->afterStateUpdated(function (Set $set, ?array $state) {
                    if ($state) {
                        $set('latitude', $state['lat']);
                        $set('longitude', $state['lng']);
                    }
                })
                ->visible(fn ($get) => filled($get('latitude')) && filled($get('longitude')))
                ->dehydrated(false),
        
                    ]);
                }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('address')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('description')
                    ->searchable()
                    ->sortable(),  
                Tables\Columns\TextColumn::make('latitude')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('longitude')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('governorate')
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
                SpatieMediaLibraryImageColumn::make('avatar')
                ->label('Images')
                ->collection('places')
                ->disk('places'),
                Tables\Columns\TextColumn::make('activities.name')
    
            ])
            ->filters([
                SelectFilter::make('governorate')
                 ->label('المحافظة')
                ->options([
                    'حلب' => 'حلب',
                    'دمشق' => 'دمشق',
                    'حمص' => 'حمص',
                    'حماة' => 'حماة',
                    'اللاذقية' => 'اللاذقية',
                    'طرطوس' => 'طرطوس',
                    'ادلب' => 'ادلب',
                    'بانياس' => 'بانياس',
                    'درعا' => 'درعا',
                    'الرقة' => 'الرقة',
                    'دير الزور' => 'دير الزور',
                 ])
                ->attribute('governorate') // هذا يحدد العمود المسؤول عن الفلترة
                ->native(false),
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
                Tables\Actions\DeleteAction::make()
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
                Section::make('Place Information')
                ->schema([
                TextEntry::make('name')->label('Place Name'),
                TextEntry::make('address')->label('Address'),
                TextEntry::make('description')->label('Description'),
                TextEntry::make('governorate')->label('Governorate')
                ]),
                Section::make('Activities')
                ->schema([
                TextEntry::make('activities.name')->label('Activity Name'),
                TextEntry::make('activitiesData.min_price')->label('Minimum Price'),
                TextEntry::make('activitiesData.max_price')->label('Maximum Price'),
                ]),
                Section::make('Images')
                ->schema([
                SpatieMediaLibraryImageEntry::make('avatar')
                ->label('images')
                ->collection('places')
                ->size(150)
                ->extraImgAttributes(['style' => 'display:flex ; margin:5px ;flex-wrap:wrap; gap:10px; object-fit:cover ; border-radius:8px ;padding:2px'])
                ->columnSpanFull(),
                ])
              
            ]);
        }
    public static function getRelations(): array
    {
        return [
            //ActivitiesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPlaces::route('/'),
            'create' => Pages\CreatePlace::route('/create'),
            'view' => Pages\ViewPlace::route('/{record}'),
            'edit' => Pages\EditPlace::route('/{record}/edit'),
        ];
    }



}
