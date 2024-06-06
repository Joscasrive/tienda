<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Filament\Resources\OrderResource\Widgets\OrderStats;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
    protected function getHeaderWidgets(): array
    {
         return [
            OrderStats::class,
         ];   
    }

   public function  getTabs(): Array
   {
    return [
       null => Tab::make('Todos'),
       'nuevo' => Tab::make('Nuevo')->query(fn($query)=>$query->where('status','nuevo')),
       'procesando' => Tab::make('Procesando')->query(fn($query)=>$query->where('status','procesando')),
       'enviado' => Tab::make('Enviado')->query(fn($query)=>$query->where('status','enviado')),
       'entregado' => Tab::make('Entregado')->query(fn($query)=>$query->where('status','entregado')),
       'cancelado' => Tab::make('Cancelado')->query(fn($query)=>$query->where('status','cancelado')),

    ];
   }
    
}
