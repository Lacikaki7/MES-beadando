<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Order;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Shipment;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\OrderResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\OrderResource\RelationManagers;
use Filament\Tables\Enums\FiltersLayout;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;
    protected static ?string $navigationGroup = 'Production management';

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Section::make()
            ->schema([
                Forms\Components\Select::make('customer_id')
                ->label('Customer')
                ->relationship('customer', 'name')
                ->required(),
                Select::make('product_id')
                ->label('Product')
                ->options(Product::all()->pluck('name', 'id'))
                ->searchable()
                ->required(),
            TextInput::make('quantity')->required(),
            Select::make('status')
            ->options([
                'pending' => 'pending',
                'in production' => 'in production',
                'completed' => 'completed',
                'shipped' => 'shipped',
                'delivered' => 'delivered',
                'declined' => 'declined',
            ])
            ->required()
            ->default('pending'),
            ])->columns(2)
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id'),
                Tables\Columns\TextColumn::make('customer.name')->label('Customer'),
                TextColumn::make('product.name')->label('Product'),
                TextColumn::make('quantity'),
                TextColumn::make('status'),
                TextColumn::make('created_at')->dateTime(),
                TextColumn::make('updated_at')->dateTime(),
            ])
            ->filters([
                SelectFilter::make('customer_id')
                    ->options(
                        Customer::all()->pluck('name', 'id')->toArray() // Adjusted to use Customer model
                    )
                    ->label('Customer'),
                SelectFilter::make('product_id')
                    ->options(
                        Product::all()->pluck('name', 'id')->toArray() // Adjusted to use Product model
                    )
                    ->label('Product'),
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Pending',
                        'in_production' => 'In Production',
                        'completed' => 'Completed',
                        'shipped' => 'Shipped',
                        'delivered' => 'Delivered',
                        'declined' => 'Declined',
                    ])
                    ->query(function (Builder $query, array $data) {
                        if ($status = $data['value'] ?? null) {
                            $query->where('status', $status);
                        }
                    })
                ], layout:FiltersLayout::AboveContent)->filtersFormColumns(3)
            ->actions([
                Tables\Actions\ActionGroup::make([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->before(function (Tables\Actions\DeleteAction $action, $record) {
                        $shipments = Shipment::where('order_id', $record->id)->exists();
                        if ($shipments) {
                            Notification::make()
                                ->title('Error')
                                ->body('Cannot delete order with existing shipments.')
                                ->danger()
                                ->send();

                            $action->cancel();
                        }
                    }),
                ]),
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
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
