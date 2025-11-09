<?php

namespace App\Filament\App\Resources\Reservations\Pages;

use App\Enums\Icons\PhosphorIcons;
use App\Filament\App\Resources\MedicalDocuments\MedicalDocumentResource;
use App\Filament\App\Resources\Reservations\ReservationResource;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Support\Enums\Width;
use Filament\Tables\Table;

class DoctorReservations extends ListReservations
{
    protected static string $resource = ReservationResource::class;

    protected static ?int $navigationSort = 4;

    protected static ?string $slug = 'doctor';

    protected static ?string $title = 'Operater';

    protected static string|BackedEnum|null $navigationIcon = PhosphorIcons::UserCheck;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('new-medical-document')
                ->icon(PhosphorIcons::FilePlus)
                ->fillForm(function($data) {
                    $data['service_provider_id'] = auth()->id();
                })
                ->label('Novi nalaz')
                ->outlined()
                ->url(function () {
                    return MedicalDocumentResource::getUrl('create');
                }),

            CreateAction::make()
                ->fillForm(function($data) {
                    $data['service_provider_id'] = auth()->id();

                    return $data;
                })
                ->color('success')
                ->modalWidth(Width::SixExtraLarge)
                ->label('Nova rezervacija')
                ->icon(PhosphorIcons::CalendarPlus),
        ];
    }
    public function table(Table $table): Table
    {
        return parent::table($table)->modifyQueryUsing(function ($query) {
            return $query->where('service_provider_id', auth()->id());
        });
    }
}
