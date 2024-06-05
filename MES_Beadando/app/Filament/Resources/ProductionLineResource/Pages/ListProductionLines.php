<?php

namespace App\Filament\Resources\ProductionLineResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Filament\Resources\Pages\ListRecords\Tab;
use App\Filament\Resources\ProductionLineResource;


class ListProductionLines extends ListRecords
{
    protected static string $resource = ProductionLineResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
    public function getTabs(): array
    {
        return [
            'All' => Tab::make(),
            'Elérhető' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_avaible', true)),
            'Nem elérhető' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_avaible', false)),
        ];
    }
    
}
