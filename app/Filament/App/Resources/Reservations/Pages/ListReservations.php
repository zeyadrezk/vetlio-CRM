<?php

namespace App\Filament\App\Resources\Reservations\Pages;

use App\Enums\Icons\PhosphorIcons;
use App\Enums\ReservationStatus;
use App\Filament\App\Resources\MedicalDocuments\MedicalDocumentResource;
use App\Filament\App\Resources\Reservations\ReservationResource;
use App\Models\Reservation;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Support\Enums\Width;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class ListReservations extends ListRecords
{
    protected static string $resource = ReservationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('new-medical-document')
                ->icon(PhosphorIcons::FilePlus)
                ->label('Novi nalaz')
                ->outlined()
                ->url(function () {
                    return MedicalDocumentResource::getUrl('create');
                }),

            CreateAction::make()
                ->color('success')
                ->modalWidth(Width::SixExtraLarge)
                ->label('Nova rezervacija')
                ->icon(PhosphorIcons::CalendarPlus),
        ];
    }

    public function getDefaultActiveTab(): string|int|null
    {
        return 1;
    }

    public function getTabs(): array
    {
        $room = $this->getTable()->getFilter('room_id')?->getState()['value'] ?? null;
        $serviceProvider = $this->getTable()->getFilter('service_provider_id')?->getState()['value'] ?? null;
        $from = $this->getTable()->getFilter('date')?->getState()['from'] ?? null;
        $to = $this->getTable()->getFilter('date')?->getState()['to'] ?? null;

        $baseQuery = $this->getModel()::query()
            ->when($from, fn($q) => $q->whereDate('date', '>=', $from))
            ->when($to, fn($q) => $q->whereDate('date', '<=', $to))
            ->when($room, fn($q) => $q->where('room_id', $room))
            ->when($serviceProvider, fn($q) => $q->where('service_provider_id', $serviceProvider));

        $counts = $baseQuery->clone()
            ->select('status_id', DB::raw('COUNT(*) as total'))
            ->groupBy('status_id')
            ->pluck('total', 'status_id');


        $tabs = [];

        foreach (ReservationStatus::cases() as $status) {
            $tabs[$status->value] = Tab::make($status->getLabel())
                ->badge($counts[$status->value] ?? 0)
                ->modifyQueryUsing(function (Builder $query) use ($status, $serviceProvider, $room, $from, $to) {
                    return $query
                        ->where('status_id', $status->value)
                        ->when($from, fn($q) => $q->whereDate('date', '>=', $from))
                        ->when($to, fn($q) => $q->whereDate('date', '<=', $to))
                        ->when($room, fn($q) => $q->where('room_id', $room))
                        ->when($serviceProvider, fn($q) => $q->where('service_provider_id', $serviceProvider)
                        );
                });
        }

        return $tabs;
    }
}
