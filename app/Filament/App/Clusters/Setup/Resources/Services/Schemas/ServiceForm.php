<?php

namespace App\Filament\App\Clusters\Setup\Resources\Services\Schemas;

use App\Models\ServiceGroup;
use Awcodes\Palette\Forms\Components\ColorPicker;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;

class ServiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Naziv')
                            ->required(),

                        TextInput::make('code')
                            ->label('Šifra')
                            ->required(),

                        Select::make('service_group_id')
                            ->relationship('serviceGroup', 'name')
                            ->label('Grupa')
                            ->createOptionForm([
                                TextInput::make('name')
                                    ->label('Naziv')
                                    ->required(),
                            ])
                            ->createOptionUsing(function (array $data) {
                                return ServiceGroup::create($data)->id;
                            })
                            ->required(),

                        Toggle::make('active')
                            ->inline(false)
                            ->label('Aktivno')
                            ->default(true)
                            ->required(),

                        Grid::make(2)
                            ->schema([
                                TimePicker::make('duration')
                                    ->default("00:15")
                                    ->label('Trajanje')
                                    ->seconds(false)
                                    ->native(false)
                                    ->minutesStep(5)
                                    ->required(),

                                ColorPicker::make('color')
                                    ->label('Boja'),

                            ]),

                        TextEntry::make('placeholder')
                            ->html()
                            ->columnSpanFull()
                            ->hiddenLabel()
                            ->state(new HtmlString('<hr class="border-gray-200"/>')),

                        CheckboxList::make('users')
                            ->label('Djelatnici')
                            ->relationship('users', 'first_name'),

                        CheckboxList::make('rooms')
                            ->label('Prostorije')
                            ->relationship('rooms', 'name')
                    ])
            ]);
    }
}
