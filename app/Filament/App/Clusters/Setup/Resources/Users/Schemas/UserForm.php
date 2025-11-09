<?php

namespace App\Filament\App\Clusters\Setup\Resources\Users\Schemas;

use App\Models\Branch;
use Awcodes\Palette\Forms\Components\ColorPicker;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('first_name')
                    ->label('Ime')
                    ->required()
                    ->maxLength(255),

                TextInput::make('last_name')
                    ->label('Prezime')
                    ->required()
                    ->maxLength(255),

                TextInput::make('titule')
                    ->label('Titula/Potpis')
                    ->columnSpanFull(),

                ToggleButtons::make('gender_id')
                    ->label('Spol')
                    ->inline()
                    ->default(3)
                    ->options([
                        1 => 'Muški',
                        2 => 'Ženski',
                        3 => 'Neodabrano'
                    ]),

                TextInput::make('oib')
                    ->label('OIB')
                    ->unique('users', 'oib', ignoreRecord: true, modifyRuleUsing: function ($rule) {
                        return $rule->where('organisation_id', auth()->user()->organisation_id);
                    })
                    ->validationMessages([
                        'unique' => 'OIB se već koristi'
                    ])
                    ->live(true)
                    ->minLength(11)
                    ->maxLength(11),

                DatePicker::make('date_of_birth')
                    ->label('Datum rođenja'),

                TextInput::make('name')
                    ->label('Korisničko ime')
                    ->unique('users', 'name', ignoreRecord: true, modifyRuleUsing: function ($rule) {
                        return $rule->where('organisation_id', auth()->user()->organisation_id);
                    })
                    ->validationMessages([
                        'unique' => 'Korisničko ime se već koristi'
                    ])
                    ->required()
                    ->maxLength(255),

                TextInput::make('email')
                    ->email()
                    ->prefixIcon('heroicon-o-at-symbol')
                    ->unique('users', 'email', ignoreRecord: true, modifyRuleUsing: function ($rule) {
                        return $rule->where('organisation_id', auth()->user()->organisation_id);
                    })
                    ->validationMessages([
                        'unique' => 'Email adresa se već koristi'
                    ])
                    ->required()
                    ->maxLength(255),

                Select::make('branches')
                    ->label('Poslovnice')
                    ->relationship('branches', 'name')
                    ->multiple()
                    ->preload()
                    ->validationMessages([
                        'required' => 'Potrebno je odabrati min. 1 poslovnicu'
                    ])
                    ->afterStateUpdated(function (Select $component) {
                        $select = $component->getContainer()->getComponent('primary_branch_id');
                        $select->state(array_key_first($select->getOptions()));
                    })
                    ->live()
                    ->required(),

                Select::make('primary_branch_id')
                    ->label('Primarna poslovnica')
                    ->key('primary_branch_id')
                    ->validationMessages([
                        'required' => 'Primarna poslovnica nije odabrana'
                    ])
                    ->extraInputAttributes(['wire:key' => Str::random(10)])
                    ->disabled(function (Get $get) {
                        return collect($get('branches'))->isEmpty();
                    })
                    ->options(function (Get $get, $operation) {
                        if ($get('branches')) {
                            $branchIds = collect($get('branches'))->map(function ($branch) {
                                return intval($branch);
                            });

                            return Branch::whereIn('id', $branchIds->toArray())->get()->pluck('name', 'id');
                        }

                        return Branch::get()->pluck('name', 'id');
                    })
                    ->native(false)
                    ->required(),

                ColorPicker::make('color')
                    ->label('Boja')
                    ->required(function (Get $get) {
                        return $get('service_provider');
                    }),

                Grid::make(3)
                    ->columnSpan(1)
                    ->schema([
                        Toggle::make('active')
                            ->default(true)
                            ->inline(false)
                            ->label('Aktivni djelatnik'),

                        Toggle::make('service_provider')
                            ->default(false)
                            ->live()
                            ->onColor('success')
                            ->inline(false)
                            ->label('Veterinar'),

                        Toggle::make('administrator')
                            ->default(false)
                            ->onColor('success')
                            ->inline(false)
                            ->disabled(function () {
                                return !auth()->user()->administrator;
                            })
                            ->label('Administrator'),

                        Toggle::make('fiscalization_enabled')
                            ->default(false)
                            ->inline(false)
                            ->label('Omogućena fiskalizacija')
                            ->disabled(function ($get) {
                                return !$get('oib');
                            }),
                    ]),

                FileUpload::make('signature_path')
                    ->columnSpanFull()
                    ->hint('Učitajte potpis za prikaz na nalazu.')
                    ->label('Potpis')
            ]);
    }
}
