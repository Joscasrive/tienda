<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Filament\Resources\UserResource\RelationManagers\OrdersRelationManager;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static?string $label = 'Usuarios';
    protected static?string $recordTitleAttribute = 'name';
    protected static?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
               Forms\Components\TextInput::make('name')
               ->label('Nombre')
               ->required(),
               Forms\Components\TextInput::make('email')
               ->label('Correo Electronico')
               ->email()
               ->maxlength(255)
               ->unique(ignoreRecord:true)
               ->required(),
               Forms\Components\DateTimePicker::make('email_verified_at')
               ->label('Verificar Email')
               ->default(now()),
               Forms\Components\TextInput::make('password')
               ->password()
               ->dehydrated(fn($state) => filled($state)) // No requiere el campo de contraseña si está vacío
                ->required(fn($record) => $record === null),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
               Tables\Columns\TextColumn::make('name')
               ->label('Nombre')
               ->searchable(),
               Tables\Columns\TextColumn::make('email')
               ->label('Correo')
               ->searchable(),
               Tables\Columns\TextColumn::make('email_verified_at')
               ->label('Verificacion')
               ->sortable()
               ->datetime(),
               Tables\Columns\TextColumn::make('created_at')
               ->label('Creado')
               ->datetime()
               ->sortable(),
               


            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                

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
             
            OrdersRelationManager::class
           
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
