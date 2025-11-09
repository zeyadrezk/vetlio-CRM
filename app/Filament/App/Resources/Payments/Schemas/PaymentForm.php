<?php

namespace App\Filament\App\Resources\Payments\Schemas;

use App\Enums\Icons\PhosphorIcons;
use App\Enums\PaymentMethod;
use Filament\Facades\Filament;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Schema;
use Illuminate\Support\Number;

class PaymentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('code')
                    ->label('Šifra uplate')
                    ->disabled(),

                Select::make('branch_id')
                    ->relationship('branch', 'name')
                    ->default(Filament::getTenant()->id)
                    ->required()
                    ->label('Poslovnica'),

                DateTimePicker::make('payment_at')
                    ->default(now())
                    ->label('Datum uplate')
                    ->required(),

                TextInput::make('amount')
                    ->required()
                    ->formatStateUsing(function($state) {
                        return Number::format($state, 2);
                    })
                    ->label('Iznos')
                    ->suffixIcon(PhosphorIcons::CurrencyEur),

                ToggleButtons::make('payment_method_id')
                    ->grouped()
                    ->label('Način plaćanja')
                    ->required()
                    ->options(PaymentMethod::class),

                Select::make('client_id')
                    ->relationship('client', 'first_name')
                    ->required()
                    ->label('Klijent'),

                Textarea::make('note')
                    ->label('Napomena')
                    ->columnSpanFull(),
            ]);
    }
}
