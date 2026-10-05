<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ConnectionsResource\Pages;
use App\Filament\Resources\ConnectionsResource\RelationManagers;
use App\Models\Connections;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\IconColumn;
use Filament\Forms\Components\DateTimePicker;
 
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Tabs;

class ConnectionsResource extends Resource
{
    protected static ?string $model = Connections::class;
    protected static ?int $navigationSort = 6;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                 
Tabs::make('Tabs')
    ->tabs([
        Tabs\Tab::make('Details')
            ->schema([
                DateTimePicker::make('created_at')->format('Y-m-d H:i:s'),
                TextInput::make('applicant_name')->required(),
                TextInput::make('recipent_name')->required(),
                TextInput::make('status')->required(),
                TextInput::make('recipent_business')->required(),
                TextInput::make('applicant_business')->required(),
        ]),
        Tabs\Tab::make('TIMELINE')
            ->schema([
                Repeater::make('timeline')
                    ->schema([
                        
                                TextInput::make('status')->required(),
                        DateTimePicker::make('created_at')
    ->format('Y-m-d H:i:s'),
                        
                        
                    ])
                    ->columns(1),
               ]),
              ]),
            ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('userTableDataFrom.name')
                    ->label('Recipent name'),
                Tables\Columns\TextColumn::make('userTableDataConn.name')
                    ->label('Applicant name'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('businessTypeConn.business_name')
                    ->label('Recipent Business')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('businessTypeFrom.business_name')
                    ->label('Applicant Business')
                    ->sortable(),
                        //   ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\SelectColumn::make('status')
                ->options(config('app.status'))->disabled()
                // Tables\Columns\TextColumn::make('updated_at')
                //     ->dateTime()
                //     ->sortable()
                //     ->toggleable(isToggledHiddenByDefault: true),
                // Tables\Columns\TextColumn::make('updated_by')
                //     ->numeric()
                //     ->sortable(),
                // Tables\Columns\TextColumn::make('from_payment_status')
                //     ->numeric()
                //     ->sortable(),
                // Tables\Columns\TextColumn::make('conn_payment_status')
                //     ->numeric()
                //     ->sortable(),
                // Tables\Columns\TextColumn::make('is_acceptance')
                //     ->numeric()
                //     ->sortable(),
                // Tables\Columns\TextColumn::make('conn_user_data')
                //     ->numeric()
                //     ->sortable(),
                // Tables\Columns\TextColumn::make('from_user_data')
                //     ->numeric()
                //     ->sortable(),
                // Tables\Columns\TextColumn::make('conn_mail_status')
                //     ->numeric()
                //     ->sortable(),
                // Tables\Columns\TextColumn::make('from_mail_status')
                //     ->numeric()
                //     ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                // Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListConnections::route('/'),
            // 'create' => Pages\CreateConnections::route('/create'),
            'view' => Pages\ViewConnections::route('/{record}'),
            // 'edit' => Pages\EditConnections::route('/{record}/edit'),
        ];
    }
}
