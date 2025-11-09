<?php

namespace App\Filament\App\Resources\Invoices\Schemas;

use App\Enums\Icons\PhosphorIcons;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use chillerlan\QRCode\QROptionsTrait;
use CodeWithDennis\SimpleAlert\Components\SimpleAlert;
use Endroid\QrCode\ErrorCorrectionLevel;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Illuminate\Support\HtmlString;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\Writer\PngWriter;
class InvoiceInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                static::canceledInvoiceAlert(),

                Section::make()->contained()
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                static::getOrganisationInformationBlock(),
                                static::getClientInformationBlock()
                                    ->columnStart(3),
                                static::invoiceInformation()
                            ]),


                        RepeatableEntry::make('invoiceItems')
                            ->label('Stavke računa')
                            ->table([
                                TableColumn::make('#')
                                    ->width('50px')
                                    ->alignCenter(),
                                TableColumn::make('Stavka'),
                                TableColumn::make('Količina')
                                    ->alignEnd(),
                                TableColumn::make('Cijena')
                                    ->alignEnd(),
                                TableColumn::make('Ukupno')
                                    ->alignEnd()
                            ])
                            ->schema([
                                TextEntry::make('id')
                                    ->alignCenter()
                                    ->html(),
                                TextEntry::make('name')
                                    ->html()
                                    ->formatStateUsing(function (InvoiceItem $record) {
                                        return str(new HtmlString("{$record->name}"))->append('</br>' . $record->description);
                                    }),
                                TextEntry::make('quantity')
                                    ->alignEnd(),
                                TextEntry::make('price')
                                    ->money('EUR')
                                    ->alignEnd(),
                                TextEntry::make('total')
                                    ->money('EUR')
                                    ->weight(FontWeight::Bold)
                                    ->alignEnd(),
                            ]),

                        Grid::make(3)
                            ->columnSpanFull()
                            ->schema([
                                ImageEntry::make('qrcode')
                                    ->visible(function (Invoice $record) {
                                        return $record->fiscalization_at;
                                    })
                                    ->hiddenLabel()
                                    ->columnSpan(1)
                                    ->label('QR kod'),
                                static::invoiceTotals()
                            ]),

                        TextEntry::make('terms_and_conditions')
                            ->label('Uvjeti i odredbe')
                            ->visible(function (Invoice $record) {
                                return $record->terms_and_conditions;
                            })
                            ->columnSpanFull(),
                    ])
            ]);
    }

    private static function getOrganisationInformationBlock()
    {
        return Grid::make(1)
            ->gap(false)
            ->schema([
                TextEntry::make('code')
                    ->extraAttributes([
                        'class' => 'mb-2'
                    ])
                    ->hiddenLabel()
                    ->weight(FontWeight::Bold)
                    ->color('info')
                    ->size(TextSize::Large),
                TextEntry::make('organisation.name')
                    ->hiddenLabel()
                    ->weight(FontWeight::Bold)
                    ->size(TextSize::Small),
                TextEntry::make('organisation.address')
                    ->hiddenLabel()
                    ->size(TextSize::Small),
                TextEntry::make('organisation.city')
                    ->hiddenLabel()
                    ->size(TextSize::Small)
                    ->state(function (Invoice $record) {
                        return $record->organisation->city . ', ' . $record->organisation->zip_code;
                    })
            ]);
    }

    private static function getClientInformationBlock()
    {
        return Grid::make(1)
            ->gap(false)
            ->schema([
                TextEntry::make('code')
                    ->state('Za klijenta')
                    ->hiddenLabel()
                    ->alignEnd()
                    ->weight(FontWeight::Bold),

                TextEntry::make('client.full_name')
                    ->color('info')
                    ->hiddenLabel()
                    ->alignEnd()
                    ->weight(FontWeight::Bold),

                TextEntry::make('client.address')
                    ->hiddenLabel()
                    ->extraAttributes([
                        'class' => 'text-right'
                    ])
                    ->size(TextSize::Small),

                TextEntry::make('client.city')
                    ->extraAttributes([
                        'class' => 'text-right'
                    ])
                    ->hiddenLabel()
                    ->size(TextSize::Small)
                    ->state(function (Invoice $record) {
                        return $record->client->city . ', ' . $record->client->zip_code;
                    })
            ]);
    }

    private static function invoiceInformation()
    {
        return Grid::make(1)
            ->extraAttributes([
                'class' => 'text-right'
            ])
            ->columnStart(3)
            ->columnSpan(2)
            ->gap(false)
            ->schema([
                TextEntry::make('invoice_date')
                    ->label('Datum računa:')
                    ->inlineLabel()
                    ->dateTime('d.m.Y H:i')
                    ->weight(FontWeight::SemiBold),

                TextEntry::make('payment_method_id')
                    ->label('Način plaćanja')
                    ->inlineLabel()
                    ->weight(FontWeight::SemiBold),

                TextEntry::make('user.full_name')
                    ->label('Izradio:')
                    ->inlineLabel()
                    ->weight(FontWeight::SemiBold),

                TextEntry::make('zki')
                    ->label('ZKI:')
                    ->visible(function (Invoice $record) {
                        return $record->fiscalization_at;
                    })
                    ->inlineLabel()
                    ->weight(FontWeight::SemiBold),

                TextEntry::make('jir')
                    ->label('JIR:')
                    ->visible(function (Invoice $record) {
                        return $record->fiscalization_at;
                    })
                    ->inlineLabel()
                    ->weight(FontWeight::SemiBold),
            ]);
    }

    private static function canceledInvoiceAlert()
    {
        return SimpleAlert::make('canceled-invoice')
            ->visible(function ($record) {
                return $record->storno_of_id != null;
            })
            ->icon(PhosphorIcons::Invoice)
            ->danger()
            ->border()
            ->title('Ovo je stornirani račun')
            ->columnSpanFull();

    }

    private static function invoiceTotals()
    {
        return Grid::make(1)
            ->columnSpan(2)
            ->columns(1)
            ->gap(false)
            ->schema([
                TextEntry::make('total_base_price')
                    ->money('EUR')
                    ->label('Osnovica')
                    ->alignRight()
                    ->columnStart(3)
                    ->inlineLabel()
                    ->weight(FontWeight::Bold)
                    ->alignEnd(),
                TextEntry::make('total_tax')
                    ->money('EUR')
                    ->label('Ukupno PDV')
                    ->alignRight()
                    ->columnStart(3)
                    ->inlineLabel()
                    ->weight(FontWeight::Bold)
                    ->alignEnd(),
                TextEntry::make('total_discount')
                    ->money('EUR')
                    ->label('Ukupan popust')
                    ->alignRight()
                    ->columnStart(3)
                    ->inlineLabel()
                    ->weight(FontWeight::Bold)
                    ->alignEnd(),
                TextEntry::make('total')
                    ->alignRight()
                    ->inlineLabel()
                    ->columnStart(3)
                    ->size(TextSize::Large)
                    ->money('EUR')
                    ->label(new HtmlString('<span class="text-lg">Sveukupno:</span>'))
                    ->extraAttributes([
                        'class' => 'text-lg'
                    ])
                    ->weight(FontWeight::Bold),
            ]);
    }

    private function generate2dBarcode() {
        $payload = implode("\n", [
            'HRVHUB30',          // identifikator formata
            'EUR',
            str_pad(number_format(200, 2, '', ''), /*npr.*/15, '0', STR_PAD_LEFT),
            'test',
        ]);

        $result = Builder::create()
            ->writer(new PngWriter())
            ->data($payload)
            ->encoding(new Encoding('UTF-8'))
            ->errorCorrectionLevel(ErrorCorrectionLevel::Medium)
            ->size(300)
            ->margin(10)
            ->build();
    }
}
