<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PincodeResource\Pages;
use App\Filament\Resources\PincodeResource\RelationManagers;
use App\Models\Pincode;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PincodeResource extends Resource
{
    protected static ?string $model = Pincode::class;
     protected static ?string $navigationGroup = 'Masters';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('PostOfficeName')
                    ->maxLength(100),
                Forms\Components\TextInput::make('Pincode')
                    ->numeric(),
                Forms\Components\TextInput::make('City')
                    ->maxLength(100),
                Forms\Components\TextInput::make('District')
                    ->maxLength(100),
                Forms\Components\TextInput::make('State')
                    ->maxLength(100),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('PostOfficeName')
                    ->searchable(),
                Tables\Columns\TextColumn::make('Pincode')
                    ->numeric()
                    ->sortable()->searchable(),
                Tables\Columns\TextColumn::make('City')
                    ->searchable(),
                Tables\Columns\TextColumn::make('District')
                    ->searchable(),
                Tables\Columns\TextColumn::make('State')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListPincodes::route('/'),
            'create' => Pages\CreatePincode::route('/create'),
            'edit' => Pages\EditPincode::route('/{record}/edit'),
        ];
    }
}
