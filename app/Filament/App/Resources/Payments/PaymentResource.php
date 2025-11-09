<?php

namespace App\Filament\App\Resources\Payments;

use App\Enums\Icons\PhosphorIcons;
use App\Filament\App\Resources\Payments\Pages\CreatePayment;
use App\Filament\App\Resources\Payments\Pages\EditPayment;
use App\Filament\App\Resources\Payments\Pages\ListPayments;
use App\Filament\App\Resources\Payments\Pages\ViewPayment;
use App\Filament\App\Resources\Payments\Schemas\PaymentForm;
use App\Filament\App\Resources\Payments\Schemas\PaymentInfolist;
use App\Filament\App\Resources\Payments\Tables\PaymentsTable;
use App\Models\Payment;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static string|BackedEnum|null $navigationIcon = PhosphorIcons::CurrencyEurLight;

    protected static ?string $recordTitleAttribute = 'code';

    protected static ?string $navigationLabel = 'Uplate';

    protected static ?string $label = 'uplata';

    protected static ?string $pluralLabel = 'uplate';

    protected static string | UnitEnum | null $navigationGroup = 'Financije';

    public static function form(Schema $schema): Schema
    {
        return PaymentForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PaymentInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PaymentsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPayments::route('/'),
            //'create' => CreatePayment::route('/create'),
            //'view' => ViewPayment::route('/{record}'),
            //'edit' => EditPayment::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
