<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NewUserDataResource\Pages;
use App\Filament\Resources\NewUserDataResource\RelationManagers;
use App\Models\NewUserData;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Models\BusinessCategory;
use App\Models\SubBusinessCategory;
use Filament\Forms\Components\CheckboxList;
use Illuminate\Support\Collection;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Support\Facades\DB;


class NewUserDataResource extends Resource
{
    protected static ?string $model = NewUserData::class;
    protected static ?string $navigationGroup = 'User Data';
    protected static ?string $navigationLabel = 'Update';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('organization_name')
                    ->maxLength(255),
                Forms\Components\Select::make('business_type')->options(config('app.business_type'))->required()->live(),
                Forms\Components\TextInput::make('type_organization')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('msme_type')
                    ->required()
                    ->maxLength(100),
                Forms\Components\Select::make('business_category')
                    ->options(fn(Get $get): Collection => BusinessCategory::query()
                        ->where('type', $get('business_type'))
                        ->pluck('name', 'id'))
                    ->required(),

                Forms\Components\Select::make('business_sub_category')->options(SubBusinessCategory::all()->pluck('name', 'id'))->required()->hidden(fn(Get $get): bool =>  $get('business_type')),


                Forms\Components\TextInput::make('specialization')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('contact_no')
                    ->required()
                    ->maxLength(15),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('pincode')
                    ->required()
                    ->live('debounce:1000')
                    ->afterStateUpdated(function (Get $get, Set $set, ?string $old, ?string $state) {
                        $city = DB::table('pincode')->where('Pincode', $state)->value('City');
                        $state = DB::table('pincode')->where('Pincode', $state)->value('State');
                        $set('city', $city);
                        $set('state', $state);
                    })
                    ->numeric(),
                Forms\Components\TextInput::make('city')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('state')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('experience')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('document_no')
                    ->required()
                    ->maxLength(255),
                Forms\Components\FileUpload::make('document_file')
                    ->required()
                    ->multiple()
                    ->disk('local')->directory('userdata')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('state_city')
                    ->required()
                    ->maxLength(255),
                CheckboxList::make('business_size')->columnSpanFull()
                    ->options([
                        'startup' => 'Startup',
                        'micro' => 'Micro',
                        'small' => 'Small',
                        'medium' => 'Medium',
                        'beyond-sme' => 'Beyond Sme'
                    ]),

                Placeholder::make('business_description')->columnSpanFull()
                    ->content(function (Set $set, Get $get, UserData $record): string {
                        $businessCategory = BusinessCategory::query()->where('id', $get('business_category'))->value('name');
                        if ($get('business_type') == 0) {
                            $subbusinessCategory = SubBusinessCategory::query()->where('id', $get('business_sub_category'))->value('name');
                            $str = 'We are a ' . $get('organization_name') . ' and a ' . $get('msme_type') . ' MSME firm in ' . $businessCategory . ' ' . $subbusinessCategory . ' with a ' . $get('experience') . ' years of experience and specialization in ' . '' . $get('specialization') . ' operating from ' . $get('city') . ' ' . $get('state') . ' and we also provide service to ' . $get('state_city') . '.';
                        } else {
                            $str = 'We are a ' . $get('organization_name') . ' and a ' . $get('msme_type') . ' MSME firm in ' . $businessCategory . ' with a ' . $get('experience') . ' years of experience and specialization in ' . '' . $get('specialization') . ' operating from ' . $get('city') . ' ' . $get('state') . ' and we also provide service to ' . $get('state_city') . '.';
                        }
                        $set('business_description', $str);
                        return $str;;
                    }),

                Forms\Components\TextInput::make('business_description')->hidden(),
                Toggle::make('approve')->onColor('success')->offColor('danger')->live(),

                Textarea::make('remark')->hidden(fn(Get $get): bool =>  $get('approve'))->columnSpanFull(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('organization_name')
                    ->searchable(),
                // Tables\Columns\TextColumn::make('business_type')
                //     ->numeric()
                //     ->sortable(),
                // Tables\Columns\TextColumn::make('type_organization')
                //     ->searchable(),
                // Tables\Columns\TextColumn::make('msme_type')
                //     ->searchable(),
                // Tables\Columns\TextColumn::make('business_category')
                //     ->numeric()
                //     ->sortable(),
                // Tables\Columns\TextColumn::make('business_sub_category')
                //     ->numeric()
                //     ->sortable(),
                // Tables\Columns\TextColumn::make('specialization')
                //     ->searchable(),
                // Tables\Columns\TextColumn::make('contact_no')
                //     ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('received_from')
                    ->label('Received From')
                    ->state(function (NewUserData $record): string {
                        return ($record->is_bulk_user || !empty($record->msme_business_data_id)) ? 'Bulk Mail' : 'Normal';
                    })
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Bulk Mail' => 'info',
                        'Normal' => 'gray',
                    })
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderBy('is_bulk_user', $direction);
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime('d M Y')
                    ->sortable(),
                // Tables\Columns\TextColumn::make('pincode')
                //     ->numeric()
                //     ->sortable(),
                // Tables\Columns\TextColumn::make('city')
                //     ->searchable(),
                // Tables\Columns\TextColumn::make('state')
                //     ->searchable(),
                // Tables\Columns\TextColumn::make('experience')
                //     ->numeric()
                //     ->sortable(),
                // Tables\Columns\TextColumn::make('document_no')
                //     ->searchable(),
                // Tables\Columns\TextColumn::make('state_city')
                //     ->searchable(),
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
            'index' => Pages\ListNewUserData::route('/'),
            'create' => Pages\CreateNewUserData::route('/create'),
            'edit' => Pages\EditNewUserData::route('/{record}/edit'),
        ];
    }
}
