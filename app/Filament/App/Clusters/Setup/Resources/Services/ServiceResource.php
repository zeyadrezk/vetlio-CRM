<?php

namespace App\Filament\App\Clusters\Setup\Resources\Services;

use App\Filament\App\Clusters\Setup\Resources\Services\Pages\CreateService;
use App\Filament\App\Clusters\Setup\Resources\Services\Pages\EditService;
use App\Filament\App\Clusters\Setup\Resources\Services\Pages\ListServices;
use App\Filament\App\Clusters\Setup\Resources\Services\RelationManagers\PricesRelationManager;
use App\Filament\App\Clusters\Setup\Resources\Services\Schemas\ServiceForm;
use App\Filament\App\Clusters\Setup\Resources\Services\Tables\ServicesTable;
use App\Filament\App\Clusters\Setup\SetupCluster;
use App\Models\Service;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Number;
use UnitEnum;

class ServiceResource extends Resource
{
    protected static ?string $model = Service::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::HandRaised;

    protected static ?string $cluster = SetupCluster::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static bool $isScopedToTenant = false;

    protected static ?string $navigationLabel = 'Usluge';

    protected static string | UnitEnum | null $navigationGroup = 'Usluge';

    protected static ?string $label = 'usluga';

    protected static ?string $pluralLabel = 'usluge';

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'serviceGroup.name'];
    }

    public static function getGlobalSearchResultUrl(Model $record): string
    {
        return self::getUrl('edit', ['record' => $record]);
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['currentPrice', 'serviceGroup']);;
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Grupa' => $record->serviceGroup->name,
            'Cijena' => $record->currentPrice ? Number::currency($record->currentPrice->price_with_vat) : 'Nema cijene',
        ];
    }

    public static function form(Schema $schema): Schema
    {
        return ServiceForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ServicesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            PricesRelationManager::make()
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListServices::route('/'),
            'create' => CreateService::route('/create'),
            'edit' => EditService::route('/{record}/edit'),
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
