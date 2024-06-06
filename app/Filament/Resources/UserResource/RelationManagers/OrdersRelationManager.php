<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use App\Filament\Resources\OrderResource;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrdersRelationManager extends RelationManager
{
    protected static string $relationship = 'orders';
    protected static ?string $label = 'Pedidos';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
             
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
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

            ])
            ->filters([
                //
            ])
            ->headerActions([
                // Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
                Action::make('Ver Pedido')->url(fn(Order $record):string => OrderResource::getUrl('view',['record'=>$record]))
                ->color('info')->icon('heroicon-o-eye'),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
