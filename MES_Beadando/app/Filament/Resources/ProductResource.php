<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Product;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\DateTimePicker;
use App\Filament\Resources\ProductResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\ProductionLine;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;
    protected static ?string $navigationGroup = 'MES';

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                ->schema([
                    TextInput::make('name')->required(),
                    Select::make('status')
                    ->options([
                        'pending' => 'pending',
                        'in production' => 'in production',
                        'produced' => 'produced',
                        'delivered' => 'delivered',
                    ])->required(),
                    Select::make('current_location')
                        ->label('Current Location')
                        ->options(ProductionLine::all()->pluck('name', 'name'))
                        ->searchable()
                        ->required(),
                    Select::make('target_location')
                        ->label('Target Location')
                        ->options(ProductionLine::all()->pluck('name', 'name'))
                        ->searchable()
                        ->nullable(),
                    DateTimePicker::make('estimated_completion_time')->nullable(),
                    Textarea::make('history')->nullable(),
                ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id'),
                TextColumn::make('name'),
                TextColumn::make('status'),
                TextColumn::make('current_location'),
                TextColumn::make('target_location'),
                TextColumn::make('estimated_completion_time')->dateTime(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
