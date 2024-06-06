<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\OrderResource;
use App\Models\Order;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class UltimosPedidos extends BaseWidget
{  
   //cambiar nombre del titulo
   protected  int|string| array $columnSpan ='full';
   protected static ?int $sort =2;

   
    public function table(Table $table): Table
    {
        return $table
            ->query(OrderResource::getEloquentQuery())
            ->defaultPaginationPageOption(5)
            ->defaultSort('created_at','desc')
            ->columns([
                TextColumn::make('id')->label('Id')->searchable(),
                TextColumn::make('grand_total')->label('Total')->money(),
                TextColumn::make('status')->label('Status')->badge()
                ->color(fn(string $state):string => match($state){
                             'nuevo' => 'info',
                            'procesando' => 'warnig',
                            'enviado' => 'success',
                            'entregado' => 'success',
                            'cancelado' => 'danger'  
                })
                ->icon(fn(string $state):string => match($state){
                    'nuevo' => 'heroicon-m-sparkles',
                    'procesando' => 'heroicon-m-arrow-path',
                    'enviado' => 'heroicon-m-truck',
                    'entregado' => 'heroicon-m-check-badge',
                    'cancelado' => 'heroicon-m-x-circle' 
                })->sortable(),
                TextColumn::make('payment_method')->label('Metodo de Pago')->sortable(),
                TextColumn::make('payment_status')->label('Estado')->sortable(),
                TextColumn::make('created_at')->label('Fecha de Creación')->sortable(),

            ])->actions([
                Tables\Actions\Action::make('Ver Pedido')
                ->url(fn(Order $record):string=>OrderResource::getUrl('view',['record'=>$record]))->icon('heroicon-o-eye'),

            ]);
    }
}
