<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Product;
use App\Models\Shipment;
use Filament\Forms\Form;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use App\Models\ProductionLine;
use Filament\Resources\Resource;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ProductionLineResource\Pages;
use App\Filament\Resources\ProductionLineResource\RelationManagers;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\IconColumn;

class ProductionLineResource extends Resource
{
    protected static ?string $model = ProductionLine::class;
    protected static ?string $navigationGroup = 'Production management';

    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Section::make()
            ->schema([
                TextInput::make('name')->required(),
                TextInput::make('location')->required(),
                Toggle::make('is_avaible'),
                Textarea::make('current_task')->nullable(),
            ])->columns(2)

        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id'),
                TextColumn::make('name'),
                TextColumn::make('location'),
                IconColumn::make('is_avaible')->boolean(),
                TextColumn::make('current_task'),
                TextColumn::make('created_at')->dateTime(),
                TextColumn::make('updated_at')->dateTime(),
            ])
            ->filters([
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->before(function (Tables\Actions\DeleteAction $action, $record) {
                        $productsUsingCurrentLocation = Product::where('current_location', $record->id)->exists();
                        $productsUsingTargetLocation = Product::where('target_location', $record->id)->exists();

                        if ($productsUsingCurrentLocation || $productsUsingTargetLocation) {
                            Notification::make()
                                ->title('Error')
                                ->body('Cannot delete production line that is in use by products.')
                                ->danger()
                                ->send();

                            $action->cancel();
                        }
                    }),
                ]),
            ], position: Tables\Enums\ActionsPosition::BeforeColumns)
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
            'index' => Pages\ListProductionLines::route('/'),
            'create' => Pages\CreateProductionLine::route('/create'),
            'edit' => Pages\EditProductionLine::route('/{record}/edit'),
        ];
    }
}
