<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Order;
use App\Models\Product;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Carbon\CarbonImmutable;
use App\Models\ProductionLine;
use Filament\Resources\Resource;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\MarkdownEditor;
use App\Filament\Resources\ProductResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ProductResource\RelationManagers;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;
    protected static ?string $navigationGroup = 'Production management';

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make([
                    Step::make('Details')
                    ->schema([
                        TextInput::make('name')->required(),
    /*                     Select::make('status')
                        ->options([
                            'pending' => 'pending',
                            'in production' => 'in production',
                            'produced' => 'produced',
                            'delivered' => 'delivered',
                        ])->required(), */
                        TextInput::make('price')
                            ->label('Price')
                            ->prefix('$')
                            ->numeric()
                            ->required(),
                        ])->columnSpan('full'),
                    Step::make('Locations')
                    ->schema([
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
                        ]),
                    Step::make('Others')
                    ->schema([
                        DatePicker::make('published_at')->nullable()
                        ->default(now())
                        ->format('Y-m-d'),
                        DatePicker::make('estimated_completion_time')->nullable()
                        ->default(now())
                        ->format('Y-m-d'),
                        Textarea::make('history')->nullable()->label('Description'),
                    ])
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id'),
                TextColumn::make('name')
                ->searchable(),
/*                 TextColumn::make('status'), */
                TextColumn::make('current_location'),
                TextColumn::make('history')->label('Description')
                ->searchable(),
                TextColumn::make('price')
                ->prefix('$'),
                TextColumn::make('estimated_completion_time')->dateTime('Y-m-d'),
            ])
            ->filters([
                // Price Filters Group
                Tables\Filters\Filter::make('price')
                    ->label('Price')
                    ->form([
                        Forms\Components\Select::make('price')
                            ->options([
                                'less_than_50' => '< $50',
                                '50_150' => '$50 - $150',
                                '150_500' => '$150 - $500',
                                'greater_than_500' => '$500 <',
                            ]),
                    ])
                    ->query(function (Builder $query, array $data) {
                        switch ($data['price'] ?? null) {
                            case 'less_than_50':
                                $query->where('price', '<', 50);
                                break;
                            case '50_150':
                                $query->whereBetween('price', [50, 150]);
                                break;
                            case '150_500':
                                $query->whereBetween('price', [150, 500]);
                                break;
                            case 'greater_than_500':
                                $query->where('price', '>', 500);
                                break;
                        }
                    }),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\DeleteAction::make()
                        ->before(function ($action, $record) {
                            $orders = Order::where('product_id', $record->id)->exists();
                            if ($orders) {
                                Notification::make()
                                    ->title('Error')
                                    ->body('Cannot delete product with existing orders.')
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
