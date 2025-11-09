<?php

namespace App\Filament\App\Resources\MedicalDocuments\Schemas;

use App\Enums\Icons\PhosphorIcons;
use App\Models\MedicalDocument;
use App\Models\Service;
use App\Models\User;
use CodeWithDennis\SimpleAlert\Components\SimpleAlert;
use Filament\Actions\Action;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Flex;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Enums\TextSize;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Number;

class MedicalDocumentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->disabled(function (?MedicalDocument $record) {
                return $record?->locked_at;
            })
            ->columns(1)
            ->components([
                SimpleAlert::make('locked')
                    ->danger()
                    ->hiddenOn('create')
                    ->visible(function (?MedicalDocument $record) {
                        return $record?->locked_at;
                    })
                    ->border()
                    ->columnSpanFull()
                    ->action(Action::make('unlock')
                        ->action(function ($record) {
                            return $record->update([
                                'locked_at' => null,
                                'locked_user_id' => null,
                            ]);
                        })
                        ->color('danger')
                        ->icon(Heroicon::LockOpen)
                        ->link()
                        ->icon(Heroicon::LockClosed)
                        ->requiresConfirmation()
                        ->label('Otključaj'))
                    ->description(function ($record) {
                        return new HtmlString("Dokument je zaključan <b>{$record->locked_at->format('d.m.Y H:i')} (Prije {$record->locked_at->diffForHumans()})</b> od djelatnika: <b>{$record->userLocked->full_name}</b>");
                    }),
                Tabs::make('tabs')
                    ->contained(false)
                    ->tabs([
                        Tabs\Tab::make('Osnovni podaci')
                            ->key('main-info')
                            ->columns(4)
                            ->icon(Heroicon::Document)
                            ->schema([
                                Section::make()
                                    ->compact()
                                    ->columnSpan(3)
                                    ->schema([
                                        RichEditor::make('content')
                                            ->autofocus()
                                            ->hintActions([
                                                self::getLoadFromTemplateAction(),
                                                self::saveAsTemplateAction()
                                            ])
                                            ->hiddenLabel()
                                            ->extraAttributes([
                                                'style' => 'min-height: 600px;',
                                            ])
                                            ->label('Sadržaj')
                                            ->required()
                                    ]),

                                Section::make()
                                    ->compact()
                                    ->columnSpan(1)
                                    ->schema([
                                        Select::make('service_provider_id')
                                            ->label('Liječnik')
                                            ->required()
                                            ->options(User::get()->pluck('full_name', 'id')),

                                        Fieldset::make('Pacijent')
                                            ->visible(function ($livewire) {
                                                return $livewire->patient;
                                            })
                                            ->schema([
                                                Flex::make([
                                                    ImageEntry::make('patient.avatar_url')
                                                        ->defaultImageUrl('https://www.svgrepo.com/show/420337/animal-avatar-bear.svg')
                                                        ->circular()
                                                        ->imageSize(100)
                                                        ->hiddenLabel(),
                                                    Grid::make(1)
                                                        ->gap(false)
                                                        ->schema([
                                                            TextEntry::make('patient.name')
                                                                ->state(function ($livewire) {
                                                                    return $livewire?->patient?->name;
                                                                })
                                                                ->live()
                                                                ->hiddenLabel()
                                                                ->size(TextSize::Large),
                                                            TextEntry::make('patient.breed.name')
                                                                ->state(function ($livewire) {
                                                                    return $livewire?->patient?->breed?->name;
                                                                })
                                                                ->icon(PhosphorIcons::Dog)
                                                                ->size(TextSize::Small)
                                                                ->hiddenLabel(),
                                                            TextEntry::make('patient.breed.name')
                                                                ->state(function ($livewire) {
                                                                    return $livewire?->patient?->breed?->name;
                                                                })
                                                                ->icon(PhosphorIcons::Cow)
                                                                ->size(TextSize::Small)
                                                                ->hiddenLabel(),
                                                        ])
                                                ])->gap(false)->columnSpanFull(),
                                            ])
                                    ]),
                            ]),

                        Tabs\Tab::make('Stavke')
                            ->key('items')
                            ->icon(Heroicon::DocumentText)
                            ->label(function ($get) {
                                return 'Stavke (' . count($get('items') ?? []) . ')';
                            })
                            ->schema([
                                Section::make()
                                    ->contained()
                                    ->columns(4)
                                    ->schema([
                                        Select::make('service_id')
                                            ->columnSpan(1)
                                            ->live()
                                            ->columnSpan(1)
                                            ->hiddenLabel()
                                            ->placeholder('Odaberite uslugu...')
                                            ->columnSpan(2)
                                            ->afterStateUpdated(function (Get $get, Set $set) {
                                                $service = Service::find($get('service_id'));
                                                $item = [
                                                    'priceable_id' => $service->id,
                                                    'priceable_type' => Service::class,
                                                    'name' => $service->name,
                                                    'description' => 'Usluga',
                                                    'quantity' => 1,
                                                    'price' => Number::format($service->currentPrice->price, 2),
                                                    'vat' => 25,
                                                    'discount' => 0,
                                                    'total' => Number::format($service->currentPrice->price_with_vat, 2),
                                                ];

                                                $items = collect($get('items') ?? []);
                                                $items->push($item);
                                                $set('items', $items->toArray());
                                                $set('service_id', null);
                                            })
                                            ->options(Service::whereHas('currentPrice')->get()->pluck('name', 'id')),

                                        SimpleAlert::make('no-items')
                                            ->warning()
                                            ->border()
                                            ->title('Nema dodatnih stavki')
                                            ->visible(function ($get) {
                                                return !$get('items');
                                            })->columnSpanFull(),
                                        ItemsRepeater::make('items')
                                            ->visible(function ($get) {
                                                return $get('items');
                                            }),
                                    ])
                            ])
                    ]),


            ]);
    }

    public static function getLoadFromTemplateAction(): Action
    {
        return Action::make('load-from-template')
            ->link()
            ->label('Učitaj iz predloška')
            ->modalIcon(Heroicon::Document)
            ->modalDescription('Odaberite predložak za učitavanje')
            ->icon(Heroicon::Document)
            ->modalSubmitActionLabel('Dodaj')
            ->schema([
                Select::make('id')
                    ->live(),

                Grid::make(2)
                    ->visible(function ($get) {
                        return $get('id');
                    })
                    ->schema([
                        TextEntry::make('subject')
                            ->label('Naslov'),
                        TextEntry::make('user')
                            ->label('Kreirao'),
                        TextEntry::make('content')
                            ->columnSpanFull()
                            ->html()
                            ->columnSpanFull()
                            ->label('Sadržaj')
                    ]),

            ])->action(function ($data, Set $set) {

            });
    }

    private static function saveAsTemplateAction()
    {
        return Action::make('save-as-template')
            ->link()
            ->label('Spremi kao predložak')
            ->modalIcon(Heroicon::DocumentMagnifyingGlass)
            ->modalDescription('Upišite naziv predloška')
            ->icon(Heroicon::DocumentMagnifyingGlass)
            ->schema([
                TextInput::make('subject')
                    ->label('Naslov')
                    ->required(),

            ])->action(function ($data, Get $get) {

            });
    }
}
