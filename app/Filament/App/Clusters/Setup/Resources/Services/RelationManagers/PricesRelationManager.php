<?php

namespace App\Filament\App\Clusters\Setup\Resources\Services\RelationManagers;

use App\Helpers\PriceHelper;
use App\Helpers\PriceHelpers;
use App\Models\PriceList;
use App\Rules\ValidPriceDate;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PricesRelationManager extends RelationManager
{
    protected static string $relationship = 'prices';

    protected static ?string $label = 'cijena';

    protected static ?string $pluralLabel = 'cijene';

    protected static ?string $title = 'Cijene';

    public ?int $productId;

    public ?int $serviceId;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Select::make('price_list_id')
                    ->default(Filament::getTenant()->price_list_id)
                    ->relationship('priceList', 'name')
                    ->native(false)
                    ->required()
                    ->label('Cjenik'),

                DatePicker::make('valid_from_at')
                    ->label('Vrijedi od')
                    ->required()
                    ->rules(fn($record) => [
                        new ValidPriceDate($record, $this->getOwnerRecord()),
                    ])
                    ->default(now()),

                TextInput::make('price')
                    ->required()
                    ->live(true)
                    ->afterStateUpdated(function ($state, Get $get, callable $set) {
                        if ($get('vat_percentage') != null) {
                            $set('price_with_vat', PriceHelpers::vatFromNet($state, $get('vat_percentage')));
                        }
                    })
                    ->numeric(2)
                    ->suffix(auth()->user()->organisation->currency->code)
                    ->label('Cijena'),

                TextInput::make('vat_percentage')
                    ->required()
                    ->live(true, 500)
                    ->afterStateUpdated(function ($state, Get $get, callable $set) {
                        $price = $get('price');
                        $priceWithVat = $get('price_with_vat');

                        if (!$state) {
                            $set('vat_percentage', 0);
                        }

                        if ($price != null) {
                            $set('price_with_vat', PriceHelpers::vatFromNet($price, $state));
                            return;
                        }
                        if ($priceWithVat != null) {
                            $set('price', PriceHelpers::netFromVat($priceWithVat, $state));
                        }
                    })
                    ->numeric(2)
                    ->default(25)
                    ->inputMode('decimal')
                    ->suffix('%')
                    ->label('PDV (%)'),

                TextInput::make('price_with_vat')
                    ->required()
                    ->numeric(2)
                    ->suffix(auth()->user()->organisation->currency->code)
                    ->afterStateUpdated(function ($state, Get $get, callable $set) {
                        if ($get('vat_percentage') != null) {
                            $price = $state / (1 + ($get('vat_percentage') / 100));

                            $set('price', $price);
                        }
                    })
                    ->live(true)
                    ->label('Cijena sa PDV'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                if (class_basename($this->getOwnerRecord()) == "Service") {
                    return $query->whereMorphedTo('priceable', $this->getOwnerRecord());
                } else if (class_basename($this->getOwnerRecord()) == "Product") {
                    return $query->whereMorphedTo('priceable', $this->getOwnerRecord());
                } else if (class_basename($this->getOwnerRecord()) == "PriceList") {
                    return $query->where('price_list_id', $this->getOwnerRecord()->id);
                }

                return $query;
            })
            ->recordTitleAttribute('price')
            ->columns([
                TextColumn::make('priceList.name')
                    ->label('Cjenik'),

                TextColumn::make('valid_from_at')
                    ->date()
                    ->label('Vrijedi od'),

                TextColumn::make('price')
                    ->money('EUR')
                    ->label('Cijena'),

                TextColumn::make('vat_percentage')
                    ->numeric()
                    ->suffix('%')
                    ->label('PDV'),

                TextColumn::make('price_with_vat')
                    ->money('EUR')
                    ->weight(FontWeight::Bold)
                    ->label('Cijena')
                    ->label('Cijena sa PDV'),
            ])
            ->filters([
                SelectFilter::make('price_list_id')
                    ->label('Cjenik')
                    ->options(PriceList::pluck('name', 'id'))
                    ->native(false)
                    ->default(Filament::getTenant()->price_list_id)
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Nova cijena')
                    ->modalHeading('Nova cijena'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
