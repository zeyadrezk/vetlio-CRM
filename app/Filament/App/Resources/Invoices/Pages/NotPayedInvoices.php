<?php

namespace App\Filament\App\Resources\Invoices\Pages;

use App\Enums\Icons\PhosphorIcons;
use App\Filament\App\Resources\Invoices\InvoiceResource;
use BackedEnum;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use UnitEnum;

class NotPayedInvoices extends ListInvoices
{
    protected static string $resource = InvoiceResource::class;

    protected string $view = 'filament.app.resources.invoices.pages.not-payed-invoices';

    protected static ?string $navigationLabel = 'Neplaćeni računi';

    protected static string|BackedEnum|null $navigationIcon = PhosphorIcons::MoneyWavyLight;

    protected static string|UnitEnum|null $navigationGroup = 'Financije';

    protected static ?string $navigationParentItem = 'Računi';

    public function getSubheading(): string|Htmlable|null
    {
        return 'Popis svih neplaćenih računa';
    }

    protected function getHeaderActions(): array
    {
        return [];
    }

    public function table(Table $table): Table
    {
        return parent::table($table)
            ->filters([])
            ->emptyStateActions([])
            ->modifyQueryUsing(function ($query) {
                return $query->notPayed();
            });
    }
}
