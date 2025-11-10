<?php

namespace App\Filament\App\Resources\Reservations\Pages;

use App\Enums\Icons\PhosphorIcons;
use App\Enums\ReservationStatus;
use App\Filament\App\Resources\MedicalDocuments\MedicalDocumentResource;
use App\Filament\App\Resources\Reservations\ReservationResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Facades\Filament;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Support\Enums\Width;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class ListReservations extends ListRecords
{
    protected static string $resource = ReservationResource::class;

    public ?Collection $appointmentRequests = null;

    public function mount(): void
    {
        parent::mount();

        $this->appointmentRequests = Filament::getTenant()->appointmentRequests()->where('approval_at', null)->get();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('appointment-requests')
                ->modalIcon(PhosphorIcons::UserPlus)
                ->modalDescription(function () {
                    return 'You have ' . count($this->appointmentRequests) . ' appointment requests';
                })
                ->visible(function () {
                    return count($this->appointmentRequests) > 0;
                })
                ->badge(function () {
                    return count($this->appointmentRequests);
                })
                ->button()
                ->slideOver()
                ->record(Filament::getTenant())
                ->schema([
                    RepeatableEntry::make('appointmentRequests')
                        ->hiddenLabel()
                        ->getStateUsing(function () {
                            return $this->appointmentRequests;
                        })
                        ->schema([
                            TextEntry::make('client.full_name')->hintActions([
                                Action::make('approve')
                                    ->label('Approve')
                                    ->icon(PhosphorIcons::CheckCircleBold)
                                    ->successNotificationTitle('The appointment request has been approved')
                                    ->action(function ($record, $component, Component $livewire, $get) {
                                        $record->update([
                                            'approval_status_id' => 2,
                                            'approval_at' => now(),
                                        ]);
                                        $livewire->appointmentRequests = $livewire->appointmentRequests->reject(fn($r) => $r->id === $record->id);
                                    }),
                                Action::make('deny')
                                    ->label('Deny')
                                    ->color('danger')
                                    ->icon(PhosphorIcons::XCircleBold)
                                    ->successNotificationTitle('The appointment request has been denied')
                                    ->action(function ($record, $livewire) {
                                        $record->update([
                                            'approval_status_id' => 3,
                                            'approval_at' => now(),
                                        ]);

                                        $livewire->appointmentRequests = $livewire->appointmentRequests->reject(fn($r) => $r->id === $record->id);
                                    })
                            ])

                        ])
                ])
                ->badgeColor('success')
                ->icon(PhosphorIcons::UserPlus),

            Action::make('new-medical-document')
                ->icon(PhosphorIcons::FilePlus)
                ->label('New Medical Record')
                ->outlined()
                ->url(fn() => MedicalDocumentResource::getUrl('create')),

            CreateAction::make()
                ->color('success')
                ->modalWidth(Width::SixExtraLarge)
                ->label('New Reservation')
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
                        ->when($serviceProvider, fn($q) => $q->where('service_provider_id', $serviceProvider));
                });
        }

        return $tabs;
    }
}
