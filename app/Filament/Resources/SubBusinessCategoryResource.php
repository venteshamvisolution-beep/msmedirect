<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SubBusinessCategoryResource\Pages;
use App\Filament\Resources\SubBusinessCategoryResource\RelationManagers;
use App\Models\SubBusinessCategory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Select;

class SubBusinessCategoryResource extends Resource
{
    protected static ?string $model = SubBusinessCategory::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Masters';
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                 Select::make('category_id')->options([
                                            'Products' => 'Products'
                                        ])->required(),
                Forms\Components\TextInput::make('name')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('category_id')
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            'index' => Pages\ListSubBusinessCategories::route('/'),
            'create' => Pages\CreateSubBusinessCategory::route('/create'),
            'edit' => Pages\EditSubBusinessCategory::route('/{record}/edit'),
        ];
    }
}
