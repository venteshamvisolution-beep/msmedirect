<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NewsResource\Pages;
use App\Filament\Resources\NewsResource\RelationManagers;
use App\Models\News;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\DatePicker;

class NewsResource extends Resource
{
    protected static ?string $model = News::class;
    protected static ?string $navigationGroup = 'MSME Corner';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('link')
                    ->required()
                    ->maxLength(255),
                DatePicker::make('date'),
                Forms\Components\Toggle::make('show_home')->required(),
                Forms\Components\FileUpload::make('media')->disk('local')->directory('news_media')->maxSize(500)->preserveFilenames(),
            ]);
    }

   public static function table(Table $table): Table
{
    return $table
        ->defaultSort('date', 'desc') // 🔥 latest first
        ->columns([
            Tables\Columns\TextColumn::make('name')
                ->searchable(),

            Tables\Columns\ImageColumn::make('media')
                ->disk('local'),

            Tables\Columns\ToggleColumn::make('show_home'),

            Tables\Columns\TextColumn::make('date')
                ->date()
                ->sortable(), // optional, but recommended
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
            'index' => Pages\ListNews::route('/'),
            'create' => Pages\CreateNews::route('/create'),
            'edit' => Pages\EditNews::route('/{record}/edit'),
        ];
    }
}
