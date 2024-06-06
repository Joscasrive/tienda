<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';
    protected static ?string $label = 'Productos';
    protected static?string $recordTitleAttribute = 'name';
    protected static?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
               Group::make()->schema([
                Section::make('Informacion de Producto')->schema([
                TextInput::make('name')->label('Nombre del producto')->required()->maxLength(255)->live(onBlur:true)
                ->afterStateUpdated(function(string $operation , $state, Set $set){
                    if($operation !== 'create'){
                       return;
                    }
                    $set('slug',Str::slug($state));
                }),
                TextInput::make('slug')->required()->maxLength(255)->disabled()->dehydrated()->unique(Product::class,'slug',ignoreRecord:true),
                MarkdownEditor::make('description')->label('Descripcion')->columnSpanFull()->fileAttachmentsDirectory('products'),

                ])->columns(2),
                Section::make('Imagen')->schema([
                    FileUpload::make('images')->multiple()->directory('products')->maxFiles(5)->reorderable(),
                ])
               ])->columnSpan(2),
               Group::make()->schema([
                Section::make('Precio')->schema([
                    TextInput::make('price')->label('Precio')->numeric()->required()->prefix('$'),
                ]),
                Section::make('Asociacion')->schema([
                   Select::make('category_id')->label('Categoria')->required()->searchable()->preload()->relationship('category','name'),
                   Select::make('brand_id')->label('Marca')->required()->searchable()->preload()->relationship('brand','name'),
                ]),
                Section::make('Estados')->schema([
                    Toggle::make('in_stock')->label('Stock')->required()->default('true'),
                    Toggle::make('is_active')->label('Activo')->required()->default('true'),
                    Toggle::make('is_featured')->label('Destacado')->required(),
                    Toggle::make('on_sale')->label('Venta')->required(),
                ])
                
               ])->columnSpan(1)
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
               TextColumn::make('name')->label('Nombre')->searchable(),
               TextColumn::make('category.name')->label('Categoria')->sortable(),
               TextColumn::make('brand.name')->label('Marca')->sortable(),
               TextColumn::make('price')->money()->label('Precio')->sortable(),
               IconColumn::make('is_featured')->boolean()->label('Destacado'),
               IconColumn::make('in_stock')->boolean()->label('Stock'),
               IconColumn::make('on_sale')->boolean()->label('Venta'),
               IconColumn::make('is_active')->boolean()->label('Activo'),
               TextColumn::make('created_at')->label('Creado')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault:true),
               TextColumn::make('updated_at')->label('Actualizado')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault:true)
            ])
            ->filters([
                SelectFilter::make('category')->label('Categoria')->relationship('category','name'),
                SelectFilter::make('brand')->label('Marca')->relationship('brand','name')
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make(),
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
