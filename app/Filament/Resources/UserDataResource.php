<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserDataResource\Pages;
use App\Filament\Resources\UserDataResource\RelationManagers;
use App\Models\UserData;
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
use Filament\Forms\Components\Placeholder;
use Illuminate\Support\Collection;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Support\Facades\DB;

class UserDataResource extends Resource
{
    protected static ?string $model = UserData::class;
    protected static ?string $navigationGroup = 'User Data';
    protected static ?string $navigationLabel = 'New';
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
                        ->pluck('name', 'id')->put('1', 'Others'))
                    ->required()
                    ->live('debounce:1000'),

                Forms\Components\TextInput::make('others')
                    ->required()
                    ->maxLength(100)->hidden(fn(Get $get): bool =>  $get('business_category') != '1'),

                Forms\Components\Select::make('business_sub_category')->options(SubBusinessCategory::all()->pluck('name', 'id'))->live('debounce:1000')->required()->hidden(fn(Get $get): bool =>  $get('business_type')),


                Forms\Components\TextInput::make('specialization')
                    ->required()
                    ->maxLength(255)->live('debounce:1000'),
                Forms\Components\TextInput::make('contact_no')
                    ->required()
                    ->regex('/^[0-9]{10,15}$/')
                    ->maxLength(15),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    // ->different('email')
                    ->unique(ignoreRecord: true)
                    ->regex('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/')
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
                    ->numeric()->live('debounce:1000'),
                Forms\Components\TextInput::make('document_no')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('want_to')
                    ->label('I want to connect to')
                    ->required()
                    ->maxLength(255),
                Forms\Components\FileUpload::make('document_file')
                    ->required()
                    ->acceptedFileTypes(['application/pdf'])
                    ->image()
                    ->multiple()
                    ->disk('local')->directory('userdata')
                    ->columnSpanFull()
                    ->downloadable(),
                Forms\Components\TextInput::make('state_city')
                    ->required()
                    ->maxLength(255)->live('debounce:1000'),
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
                            $str = 'We are a ' . $get('type_organization') . ' and a ' . $get('msme_type') . ' MSME firm in ' . $businessCategory . ' ' . $subbusinessCategory . ' with a ' . $get('experience') . ' years of experience and specialization in ' . '' . $get('specialization') . ' operating from ' . $get('city') . ' ' . $get('state') . ' and we also provide service to ' . $get('state_city') . '. I want to connect to ' . $get('want_to') . '.';
                        } else {
                            $str = 'We are a ' . $get('type_organization') . ' and a ' . $get('msme_type') . ' MSME firm in ' . $businessCategory . ' with a ' . $get('experience') . ' years of experience and specialization in ' . '' . $get('specialization') . ' operating from ' . $get('city') . ' ' . $get('state') . ' and we also provide service to ' . $get('state_city') . '. I want to connect to ' . $get('want_to') . '.';
                        }
                        $set('business_description', $str);
                        return $str;;
                    }),

                // Forms\Components\TextInput::make('business_description') ->columnSpanFull(),


                Toggle::make('approve')->onColor('success')->offColor('danger')->live(),

                Textarea::make('remark')->hidden(fn(Get $get): bool =>  $get('approve'))->columnSpanFull(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->defaultPaginationPageOption(5)
            ->paginationPageOptions([5, 10, 25, 50])
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('organization_name')
                    ->searchable(),

                Tables\Columns\TextColumn::make('email')
                    ->searchable(),

                Tables\Columns\TextColumn::make('received_from')
                    ->label('Received From')
                    ->state(function (UserData $record): string {
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
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListUserData::route('/'),
            // 'create' => Pages\CreateUserData::route('/create'),
            'edit' => Pages\EditUserData::route('/{record}/edit'),
        ];
    }
}
