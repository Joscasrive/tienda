<?php

namespace App\Filament\Resources\OrderResource\Widgets;

use App\Models\Order;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;

class OrderStats extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Nuevos', Order::query()->where('status','nuevo')->count()),
            Stat::make('Procesando', Order::query()->where('status','procesando')->count()),
            Stat::make('Enviado', Order::query()->where('status','enviado')->count()),
            Stat::make('Promedio', Number::currency(Order::query()->avg('grand_total'))),
            
           
        ];
    }
}
