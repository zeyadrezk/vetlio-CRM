<?php

namespace App\Filament\App\Resources\MedicalDocuments\Schemas;

use App\Filament\App\Actions\ViewInvoiceAction;
use CodeWithDennis\SimpleAlert\Components\SimpleAlert;
use Filament\Actions\Action;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Forms\Components\RichEditor\RichContentRenderer;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\HtmlString;

class MedicalDocumentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                SimpleAlert::make('locked')
                    ->danger()
                    ->visible(function ($record) {
                        return $record->locked_at;
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

                Section::make()
                    ->columnSpanFull()
                    ->schema([
                        RichContentRenderer::make('content'),
                    ]),

                RepeatableEntry::make('items')
                    ->visible(function ($livewire) {
                        return $livewire->showItemsToPay;
                    })
                    ->columnSpanFull()
                    ->label(function ($record) {
                        return 'Stavke (' . count($record->items ?? []) . ')';
                    })
                    ->table([
                        TableColumn::make('Stavka')->width('300px')->markAsRequired(),
                        TableColumn::make('Količina')->markAsRequired()->alignEnd(),
                        TableColumn::make('Cijena')->markAsRequired()->alignEnd(),
                        TableColumn::make('PDV')->alignEnd(),
                        TableColumn::make('Popust')->alignEnd(),
                        TableColumn::make('Ukupno')->alignEnd(),
                        TableColumn::make('Naplaćeno')->alignEnd()->width('100px'),
                    ])
                    ->schema([
                        TextEntry::make('priceable.name'),
                        TextEntry::make('quantity')
                            ->alignEnd(),
                        TextEntry::make('price')
                            ->money('EUR')
                            ->alignEnd(),
                        TextEntry::make('tax')
                            ->money('EUR')
                            ->alignEnd(),
                        TextEntry::make('discount')
                            ->money('EUR')
                            ->alignEnd(),
                        TextEntry::make('total')
                            ->alignEnd()
                            ->weight(FontWeight::Bold)
                            ->money('EUR'),
                        IconEntry::make('invoice.payed')
                            ->afterContent(function ($record) {
                                return ViewInvoiceAction::make()
                                    ->record($record->invoice)
                                    ->hiddenLabel()
                                    ->visible(function() use ($record) {
                                        return $record->invoice;
                                    })
                                    ->tooltip(function () use ($record) {
                                        return $record->invoice->code;
                                    })
                                    ->extraAttributes(['class' => 'mt-1']);
                            })
                            ->alignEnd()
                            ->boolean()
                    ]),

                Grid::make(4)
                    ->visible(function ($livewire) {
                        return $livewire->showItemsToPay;
                    })
                    ->columnSpanFull()
                    ->schema([
                        TextEntry::make('items_sum_total')
                            ->columnStart(4)
                            ->label('Sveukupno:')
                            ->alignRight()
                            ->inlineLabel()
                            ->size(TextSize::Large)
                            ->weight(FontWeight::Bold)
                            ->sum('items', 'total')
                            ->money('EUR', 100)
                    ])
            ]);
    }
}
