<?php

namespace App\Filament\App\Resources\Clients\Schemas;

use App\Enums\Icons\PhosphorIcons;
use App\Models\Language;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieTagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class ClientForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                FileUpload::make('avatar_url')
                    ->alignCenter()
                    ->avatar()
                    ->columnSpanFull()
                    ->label('Slika profila'),



                Tabs::make()
                    ->columnSpanFull()
                    ->contained(false)
                    ->tabs([
                        Tabs\Tab::make('Osnovne informacije')
                            ->columns(2)
                            ->icon(Heroicon::UserCircle)
                            ->schema([
                                TextInput::make('first_name')
                                    ->label('Ime')
                                    ->required(),

                                TextInput::make('last_name')
                                    ->required()
                                    ->label('Prezime'),

                                ToggleButtons::make('gender_id')
                                    ->label('Spol')
                                    ->inline()
                                    ->default(3)
                                    ->icons([
                                        1 => PhosphorIcons::GenderMale,
                                        2 => PhosphorIcons::GenderFemale,
                                        3 => PhosphorIcons::GenderIntersex
                                    ])
                                    ->grouped()
                                    ->options([
                                        1 => 'Muški',
                                        2 => 'Ženski',
                                        3 => 'Neodabrano'
                                    ]),

                                DatePicker::make('date_of_birth')
                                    ->before(now())
                                    ->label('Datum rođenja'),

                                TextInput::make('oib')
                                    ->label('OIB')
                                    ->unique('clients', 'oib', ignoreRecord: true, modifyRuleUsing: function ($rule) {
                                        return $rule->where('organisation_id', auth()->user()->organisation_id);
                                    })
                                    ->validationMessages([
                                        'unique' => 'OIB se već koristi'
                                    ])
                                    ->numeric()
                                    ->maxLength(11),

                                Select::make('language_id')
                                    ->label('Jezik')
                                    ->default(auth()->user()->organisation->language_id)
                                    ->required()
                                    ->options(Language::get()->pluck('name_native', 'id'))
                                    ->prefixIcon(Heroicon::Flag),

                                Select::make('how_did_you_hear')
                                    ->prefixIcon(PhosphorIcons::FacebookLogo)
                                    ->label('Kako ste čuli za nas?')
                                    ->options([
                                        'facebook' => 'Facebook',
                                        'instagram' => 'Instagram',
                                    ]),

                                SpatieTagsInput::make('tags')
                                    ->label('Opaske'),
                            ]),
                        Tabs\Tab::make('Adresa')
                            ->columns(2)
                            ->icon(Heroicon::Map)
                            ->schema([
                                TextInput::make('address')
                                    ->label('Adresa'),

                                TextInput::make('city')
                                    ->label('Grad'),

                                TextInput::make('zip_code')
                                    ->label('Poštanski broj'),

                                Select::make('country_id')
                                    ->relationship('country', 'name_native')
                                    ->required()
                                    ->default(auth()->user()->organisation->country_id)
                                    ->label('Država'),
                            ]),

                        Tabs\Tab::make('Kontakt')
                            ->columns(2)
                            ->icon(Heroicon::Phone)
                            ->schema([
                                TextInput::make('email')
                                    ->email()
                                    ->unique('clients', 'email', ignoreRecord: true, modifyRuleUsing: function ($rule) {
                                        return $rule->where('organisation_id', auth()->user()->organisation_id);
                                    })
                                    ->validationMessages([
                                        'unique' => 'Email adresa se već koristi'
                                    ])
                                    ->prefixIcon('heroicon-o-at-symbol')
                                    ->label('Email'),

                                TextInput::make('phone')
                                    ->prefixIcon('heroicon-o-phone')
                                    ->unique('clients', 'phone', ignoreRecord: true, modifyRuleUsing: function ($rule) {
                                        return $rule->where('organisation_id', auth()->user()->organisation_id);
                                    })
                                    ->validationMessages([
                                        'unique' => 'Broj telefona se već koristi'
                                    ])
                                    ->label('Telefon'),
                            ])
                    ]),


            ]);
    }
}
