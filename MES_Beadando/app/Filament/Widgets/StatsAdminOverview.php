<?php

namespace App\Filament\Widgets;

use App\Models\Customer;
use App\Models\ProductionLine;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsAdminOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Users', User::query()->count())
            ->description('All Users')
            ->descriptionIcon('heroicon-m-arrow-trending-up'),
        Stat::make('All Production Lines', ProductionLine::query()->count())
            ->description('All Production Lines')
            ->descriptionIcon('heroicon-m-arrow-trending-down'),
        Stat::make('Customers', Customer::query()->count())
            ->description('All Customers')
            ->descriptionIcon('heroicon-m-arrow-trending-up'),
        ];
    }
}
