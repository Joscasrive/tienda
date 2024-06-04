<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Filament\Resources\OrderResource\RelationManagers\AddressRelationManager;
use App\Models\Order;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Number;

use function Pest\Laravel\get;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?string $label = 'Pedidos';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()->schema([
                    Section::make('Informacion de Pedido')->schema([
                        Select::make('user_id')->label('Cliente')->relationship('user','name')->searchable()->preload()->required(),
                        Select::make('payment_method')->label('Metodo de Pago')->required()->options([
                            'efectivo' => 'Efectivo',
                            'tarjeta' => 'Tarjeta',
                            'paypal' => 'Paypal',
                            
                        ]),
                        Select::make('payment_status')->label('Estado del Pago')->options([
                            'pendiente' => 'Pendiente',
                            'pagado' => 'Pagado',
                            'fallido' => 'Fallido',
                        ])->default('pendiente'),
                        ToggleButtons::make('status')->label('Estado')->inline()->required()->default('nuevo')
                        ->options([
                            'nuevo' => 'Nuevo',
                            'procesando' => 'Procesando',
                            'enviado' => 'Enviado',
                            'entregado' => 'Entregado',
                            'cancelado' => 'Cancelado'
                        ])->colors([
                            'nuevo' => 'info',
                            'procesando' => 'warnig',
                            'enviado' => 'success',
                            'entregado' => 'success',
                            'cancelado' => 'danger'  
                        ])->icons([
                            'nuevo' => 'heroicon-m-sparkles',
                            'procesando' => 'heroicon-m-arrow-path',
                            'enviado' => 'heroicon-m-truck',
                            'entregado' => 'heroicon-m-check-badge',
                            'cancelado' => 'heroicon-m-x-circle'

                        ]),
                        Select::make('currency')->label('Moneda')
                        ->options([
                        'USD' => 'Dólar Estadounidense',
                        'EUR' => 'Euro',
                        'Bs' => 'Bolivares'
                        ])->default('USD')->required(),
                        Select::make('shipping_method')->label('Método de Envío')
                        ->options([
                            'oficina' => 'Oficina',
                            'domicilio' => 'Domicilio',
                            'mrw' => 'MRW',
                            'zoom' => 'Zoom'
                        ])->required(),
                        Textarea::make('notes')->label('Nota')->columnSpanFull()
                    ])->columns(2),
                    Section::make('Encargar artículos')->schema([
                        Repeater::make('items')->label('Articulos')->relationship()->schema([
                            Select::make('product_id')->label('Producto')
                            ->relationship('product','name')
                            ->searchable()->preload()->required()
                            ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                            ->columnSpan(4)->reactive()
                            ->afterStateUpdated(fn($state,Set $set)=>$set('unit_amount',Product::find($state)?->price ?? 0))
                            ->afterStateUpdated(fn($state,Set $set)=>$set('total_amount',Product::find($state)?->price ?? 0)),
                            TextInput::make('quantity')->label('Cantidad')->numeric()->required()->default(1)->minValue(1)->columnSpan(2)->reactive()
                            ->afterStateUpdated(fn($state,Set $set ,Get $get)=>$set('total_amount',$state*$get('unit_amount'))),
                            TextInput::make('unit_amount')->label('Precio')->numeric()->required()->disabled()->dehydrated()->columnSpan(3),
                            TextInput::make('total_amount')->label('Total')->numeric()->required()->columnSpan(3)->disabled()->dehydrated(),
                            
                        ])->columns(12),
                        Placeholder::make('grand_total_placeholder')
                        ->label('Total')
                        ->content(function(Get $get, Set $set) {
                            $total = 0;
                            foreach ($get('items') as $item) {
                                $total += $item['total_amount'];
                            }
                            $set('grand_total',$total);
                            return Number::currency($total);
                        }),
                       
                    ]),
                    Hidden::make('grand_total')->default(0)
                ])->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')->label('Cliente')->sortable()->searchable(),
                TextColumn::make('grand_total')->label('Total')->sortable()->money(),
                TextColumn::make('payment_method')->label('Metodo de Pago')->sortable()->searchable(),
                TextColumn::make('payment_status')->label('Estado')->sortable()->searchable(),
                TextColumn::make('currency')->label('Moneda')->sortable()->searchable(),
                SelectColumn::make('status')->label('Estado')->options([
                    'nuevo' => 'Nuevo',
                    'procesando' => 'Procesando',
                    'enviado' => 'Enviado',
                    'entregado' => 'Entregado',
                    'cancelado' => 'Cancelado'
                ])->sortable()->searchable(),
                TextColumn::make('shipping_method')->label('Metodo de Envio')->sortable()->searchable(),
                TextColumn::make('created_at')->label('Fecha de creación')->sortable()->searchable()
                ->toggleable(isToggledHiddenByDefault:true),
                TextColumn::make('updated_at')->label('Fecha de actualizacion')->sortable()->searchable()
                ->toggleable(isToggledHiddenByDefault:true),
                
            ])
            ->filters([
                //
            ])
            ->actions([
               ActionGroup::make([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make()
               ])
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
            AddressRelationManager::class
        ];
    }
    public  static function getNavigationBadge(): ?string
    {
        return Static::getModel()::count();
    }
    public static function getNavigationBadgeColor(): string|array|null
    {
        return  Static::getModel()::count() >10 ? 'danger' : 'success';
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'view' => Pages\ViewOrder::route('/{record}'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
