<?php

namespace App\Filament\App\Resources\MedicalDocuments\Schemas;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Support\Number;

class ItemsRepeater extends Repeater
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->itemNumbers();
        $this->cloneable();
        $this->columnSpanFull();
        $this->label('Stavke');
        $this->addActionLabel('Dodaj stavku');
        $this->compact();
        $this->addable(false);
        $this->defaultItems(0);
        $this->minItems(1);
        $this->table([
            TableColumn::make('Stavka')->width('350px')->markAsRequired(),
            TableColumn::make('Količina')->markAsRequired()->alignEnd(),
            TableColumn::make('Cijena')->markAsRequired()->alignEnd(),
            TableColumn::make('PDV')->alignEnd(),
            TableColumn::make('Popust')->alignEnd(),
            TableColumn::make('Ukupno')->alignEnd(),
        ])
            ->schema([
                TextInput::make('name'),
                TextInput::make('quantity')
                    ->required()
                    ->minValue(1)
                    ->default(1)
                    ->live(false, 500)
                    ->numeric()
                    ->extraInputAttributes(['class' => 'text-right'])
                    ->afterStateUpdated(function (Get $get, Set $set) {
                        if ($get('quantity') === null) {
                            $set('quantity', 1);
                        }
                    }),
                TextInput::make('price')
                    ->required()
                    ->default(0)
                    ->live(false, 500)
                    ->formatStateUsing(fn($state) => Number::format($state ?? 0, 2))
                    ->extraInputAttributes(['class' => 'text-right']),
                TextInput::make('vat')
                    ->default(0)
                    ->live(false, 500)
                    ->formatStateUsing(fn($state) => Number::format($state ?? 0, 2))
                    ->extraInputAttributes(['class' => 'text-right']),
                TextInput::make('discount')
                    ->default(0)
                    ->live(false, 500)
                    ->formatStateUsing(fn($state) => Number::format($state ?? 0, 2))
                    ->extraInputAttributes(['class' => 'text-right']),
                TextInput::make('total')
                    ->default(0)
                    ->formatStateUsing(fn($state) => Number::format($state ?? 0, 2))
                    ->extraInputAttributes(['class' => 'text-right font-bold']),

                TextInput::make('priceable_type'),
                TextInput::make('priceable_id'),
            ]);
        $this->afterStateUpdated(function (Get $get, Set $set) {
            $this->calculateTotals($get, $set);
        });
    }


    public function calculateTotals(Get $get, Set $set): void
    {
        $items = $get($this->getName()) ?? [];
//dd($items);
        $total = 0;

        foreach ($items as $item) {
            $quantity = $item['quantity'] ?? 1;
            $price = $item['price'] ?? 0;
            $vat = $item['vat'] ?? 0;
            $discount = $item['discount'] ?? 0;

            $itemTotal = (($price + $vat) - $discount) * $quantity;
            $total += $itemTotal;
        }

        $set('total', $total);
    }
}
