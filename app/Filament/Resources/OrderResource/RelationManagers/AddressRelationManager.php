<?php

namespace App\Filament\Resources\OrderResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AddressRelationManager extends RelationManager
{
    protected static string $relationship = 'address';
    
    protected static ?string $label = 'Direccion';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('first_name')->label('Nombre')->required()->maxLength(255),
                TextInput::make('last_name')->label('Apellido')->required()->maxLength(255),
                TextInput::make('phone')->label('Telefono')->required()->tel()->maxLength(20),
                TextInput::make('street_address')->label('Direccion de Calle')
                    ->required()
                    ->maxLength(255),
                    TextInput::make('city')->label('Ciudad')
                    ->required()
                    ->maxLength(255),
                    TextInput::make('state')->label('Estado')
                    ->required()
                    ->maxLength(255),
                    TextInput::make('zip_code')->label('Codigo Postal')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('street_address')
            ->columns([
                TextColumn::make('first_name')->label('Nombre Completo'),
                TextColumn::make('last_name')->label('Telefono'),
                TextColumn::make('street_address')->label('Direccion de Calle'),
                TextColumn::make('city')->label('Ciudad'),
                TextColumn::make('state')->label('Estado'),
                TextColumn::make('zip_code')->label('Codigo Postal'),

            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
