<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PlaceResource\Pages;
use App\Filament\Resources\PlaceResource\RelationManagers;
use App\Filament\Resources\PlaceResource\RelationManagers\ActivitiesRelationManager;
use App\Models\Place;
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
                 Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('address')
                    ->required(),
                Forms\Components\Textarea::make('description')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('latitude')
                    ->required()
                    ->numeric()
                    ->rules(['between:-90,+90']),
                Forms\Components\TextInput::make('longitude')
                    ->required()
                    ->numeric()
                    ->rules(['between:-180,+180']),
                Forms\Components\Select::make('governorate')
                    ->options([
                        'حلب'=>'حلب',
                        'دمشق'=>'دمشق',
                        'حماة'=>'حماة',
                        'حمص'=>'حمص',
                        'اللاذقية'=>'اللاذقية',
                        'طرطوس'=>'طرطوس',
                        'ادلب'=>'ادلب',
                        'بانياس'=>'بانياس',
                        'درعا'=>'درعا',
                        'الرقة'=>'الرقة',
                        'دير الزور'=>'دير الزور'
                    ])
                    ->native(false)
                    ->required(),
                SpatieMediaLibraryFileUpload::make('avatar')
                ->label('Place Images')
                 ->multiple()
                ->image()
                ->maxFiles(10)
                ->maxSize(5120) // 5MB لكل ملف
                ->reorderable()
               ->collection('places')
                ->disk('public')
                ->acceptedFileTypes(['image/jpeg', 'image/png']),     
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
                    ->sortable(),
                Tables\Columns\TextColumn::make('longitude')
                    ->numeric()
                    ->sortable(),
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
                ->collection('places')   
            ])
            ->filters([
                SelectFilter::make('governorate')
                ->options([
                    'aleppo',
                    'hama'
                ])
                 ->attribute('governorate'),
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
            ])
              
            ]);
        }
    public static function getRelations(): array
    {
        return [
            ActivitiesRelationManager::class
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
       protected static function afterCreate(Model $record, array $data): void
    {
        // حفظ الملفات المرفوعة بعد إنشاء السجل
        if (isset($data['images'])) {
            $record
                ->addMultipleMediaFromRequest(['images'])
                ->each(function ($fileAdder) {
                    $fileAdder->toMediaCollection('places');
                });
        }
    }

    protected static function afterUpdate(Model $record, array $data): void
    {
        // نفس الكود للتحديث
        if (isset($data['images'])) {
            $record
                ->addMultipleMediaFromRequest(['images'])
                ->each(function ($fileAdder) {
                    $fileAdder->toMediaCollection('places');
                });
        }
    }
}
