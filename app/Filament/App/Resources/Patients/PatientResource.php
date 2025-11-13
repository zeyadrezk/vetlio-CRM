<?php

namespace App\Filament\App\Resources\Patients;

use App\Enums\Icons\HealthIcons;
use App\Filament\App\Resources\Patients\Pages\ListPatients;
use App\Filament\App\Resources\Patients\Pages\PatientDocuments;
use App\Filament\App\Resources\Patients\Pages\PatientMedicalDocuments;
use App\Filament\App\Resources\Patients\Pages\PatientReminders;
use App\Filament\App\Resources\Patients\Pages\PatientAppointments;
use App\Filament\App\Resources\Patients\Pages\ViewPatient;
use App\Filament\App\Resources\Patients\Schemas\PatientForm;
use App\Filament\App\Resources\Patients\Schemas\PatientInfolist;
use App\Filament\App\Resources\Patients\Tables\PatientsTable;
use App\Models\Patient;
use BackedEnum;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PatientResource extends Resource
{
    protected static ?string $model = Patient::class;

    protected static string|BackedEnum|null $navigationIcon = HealthIcons::FGuideDog;

    protected static ?string $recordTitleAttribute = 'name';

    protected static bool $isScopedToTenant = false;

    protected static ?string $navigationLabel = 'Patients';

    protected static ?string $label = 'patient';

    protected static ?string $pluralLabel = 'patients';

    protected static ?int $navigationSort = 20;

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'breed.name', 'species.name'];
    }

    public static function getGlobalSearchResultUrl(Model $record): string
    {
        return self::getUrl('view', ['record' => $record]);
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['breed', 'species']);
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Species' => $record->species->name,
            'Breed' => $record->breed->name ?? '-',
            'Age' => $record->date_of_birth ? $record->date_of_birth->age . ' yrs' : '-',
        ];
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            ViewPatient::class,
            PatientAppointments::class,
            PatientMedicalDocuments::class,
            PatientDocuments::class,
            PatientReminders::class,
        ]);
    }

    public static function form(Schema $schema): Schema
    {
        return PatientForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PatientInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PatientsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPatients::route('/'),
            //'create' => CreatePatient::route('/create'),
            'view' => ViewPatient::route('/{record}'),
            //'edit' => EditPatient::route('/{record}/edit'),
            'reservations' => PatientAppointments::route('/{record}/reservations'),
            'medical-documents' => PatientMedicalDocuments::route('/{record}/medical-documents'),
            'documents' => PatientDocuments::route('/{record}/documents'),
            'reminders' => PatientReminders::route('/{record}/reminders'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withCount(['reminders', 'reservations']);
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
