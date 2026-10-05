<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SchemeResource\Pages;
use App\Filament\Resources\SchemeResource\RelationManagers;
use App\Models\Scheme;
use App\Models\SchemesMaster;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Get;

class SchemeResource extends Resource
{
    protected static ?string $model = Scheme::class;
    protected static ?string $navigationGroup = 'MSME Corner';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                
                // Forms\Components\Select::make('category_id')->options(SchemesMaster::all()->pluck('name'.'id')),
                
                Forms\Components\Select::make('category_id')->options(SchemesMaster::all()->pluck('name','id'))->required(),
                
                Forms\Components\Toggle::make('show_home')->required()->live(),
                
                Forms\Components\FileUpload::make('image')->disk('local')->directory('scheme_media')->maxSize(500)->preserveFilenames()->hidden(fn (Get $get): bool => ! $get('show_home')),
                
                Forms\Components\RichEditor::make('home_text')->toolbarButtons([
        // 'attachFiles',
        'blockquote',
        'bold',
        'bulletList',
        'codeBlock',
        'h2',
        'h3',
        'italic',
        'link',
        'orderedList',
        'redo',
        'strike',
        'underline',
        'undo',
    ])->hidden(fn (Get $get): bool => ! $get('show_home')),
                
                Forms\Components\Repeater::make('details')
                    ->schema([
                        Forms\Components\TextInput::make('name')->required(),
                        Forms\Components\TextInput::make('function')->required(),
                        Forms\Components\TextInput::make('link')->required(),
                    ])->columns(3)
                 ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                //
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
            'index' => Pages\ListSchemes::route('/'),
            'create' => Pages\CreateScheme::route('/create'),
            'edit' => Pages\EditScheme::route('/{record}/edit'),
        ];
    }
}
