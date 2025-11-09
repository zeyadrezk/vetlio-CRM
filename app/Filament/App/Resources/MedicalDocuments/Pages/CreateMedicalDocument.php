<?php

namespace App\Filament\App\Resources\MedicalDocuments\Pages;

use App\Filament\App\Resources\MedicalDocuments\MedicalDocumentResource;
use App\Models\Client;
use App\Models\Patient;
use App\Models\Reservation;
use App\Models\Service;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Livewire\Attributes\Locked;

class CreateMedicalDocument extends CreateRecord
{
    protected static string $resource = MedicalDocumentResource::class;

    protected static bool $canCreateAnother = false;

    protected static ?string $title = 'Novi nalaz';

    #[Locked]
    public ?Reservation $reservation = null;

    #[Locked]
    public ?Client $client = null;

    #[Locked]
    public ?Patient $patient = null;

    protected function getFormActions(): array
    {
        return [
            $this->getCreateFormAction()
                ->label('Spremi'),
            Action::make('save-and-lock')
                ->label('Spremi i zaključaj')
                ->successNotificationTitle('Nalaz je uspješno spremljen i zaključan')
                ->action(function (Action $action) {
                    $this->create();

                    $this->getRecord()->update([
                        'locked_at' => now(),
                        'locked_user_id' => auth()->id(),
                    ]);
                }),
            $this->getCancelFormAction(),
        ];
    }

    public function getSubheading(): string|Htmlable|null
    {
        return $this->patient?->description ?? null;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data = parent::mutateFormDataBeforeCreate($data);

        if ($this->reservation) {
            $data['reservation_id'] = $this->reservation->id;
            $data['service_provider_id'] = $this->reservation->service_provider_id;
        }

        $data['patient_id'] = $this->patient?->id;
        $data['client_id'] = $this->client?->id;

        return $data;
    }

    public function beforeCreate()
    {
        if (empty($this->form->getStateOnly(['items']))) {
            Notification::make()
                ->danger()
                ->title('Niste unijeli stavke')
                ->send();
            $this->halt();
        }
    }

    protected function handleRecordCreation(array $data): Model
    {
        $record = parent::handleRecordCreation($data);

        $record->items()->createMany($data['items']);

        return $record;
    }

    public function mount(): void
    {
        parent::mount();

        $data = [];

        if (request()->has('reservationId')) {
            $this->reservation = Reservation::whereUuid(request('reservationId'))->first();

            $data['service_provider_id'] = $this->reservation->service_provider_id;
            $data['reservation_id'] = $this->reservation->id;
            $data['client_id'] = $this->reservation->client_id;
            $data['patient_id'] = $this->reservation->patient_id;

            $this->patient = $this->reservation->patient;
            $this->client = $this->reservation->client;

            $this->form->getComponent('service_provider_id')?->disabled();
            //Find reservation service and add as item.
            $service = $this->reservation->service;
            $price = $service->currentPrice;

            $data['items'] = [
                [
                    'name' => $service->name,
                    'description' => $service->name ?? null,
                    'quantity' => 1,
                    'price' => $price->price ?? 0,
                    'vat' => $price->vat_percentage ?? 0,
                    'discount' => 0,
                    'total' => $price->price_with_vat ?? 0,
                    'priceable_id' => $price->id,
                    'priceable_type' => Service::class,
                ]
            ];
        }

        $this->form->fill($data);
    }

    protected function getHeaderActions(): array
    {
        return [
            /*Action::make('save')
                ->action('create')
                ->label('Spremi'),*/
            Action::make('select-client-patient')
                ->icon(Heroicon::UserCircle)
                ->label('Odaberi klijenta i pacijenta')
                ->hidden(function () {
                    return $this->client && $this->patient;
                })
                ->schema([
                    Select::make('client_id')
                        ->options(Client::all()->pluck('first_name', 'id')),

                    Select::make('patient_id')
                        ->options(Patient::all()->pluck('name', 'id')),
                ])->action(function (array $data) {
                    $this->client = Client::find($data['client_id']);
                    $this->patient = Patient::find($data['patient_id']);

                    // Popuni formu
                    $this->form->fill([
                        'client_id' => $this->client->id,
                        'patient_id' => $this->patient->id,
                    ]);
                }),
            Action::make('history')
                ->icon(Heroicon::DocumentMagnifyingGlass)
                ->slideOver()
                ->visible(function () {
                    return $this->patient;
                })
                ->modalSubmitAction(false)
                ->label('Prošli nalazi')
                ->record($this->patient)
                ->schema([
                    RepeatableEntry::make('medicalDocuments')
                        ->label(function ($state) {
                            return 'Prošli nalazi (' . count($state) . ')';
                        })
                        ->columns(2)
                        ->schema([
                            TextEntry::make('created_at')
                                ->label('Datum kreiranja')
                                ->dateTime(),
                            TextEntry::make('serviceProvider.full_name')
                                ->label('Liječnik'),
                            TextEntry::make('content')
                                ->hintAction(Action::make('open-document')
                                    ->icon(Heroicon::DocumentText)
                                    ->label('Otvori')
                                    ->openUrlInNewTab()
                                    ->url(function ($record) {
                                        return MedicalDocumentResource::getUrl('view', ['record' => $record]);
                                    })
                                )
                                ->columnSpanFull()
                                ->html()
                                ->label('Nalaz')
                        ])
                ])
                ->label('Prijašnji nalazi')
                ->outlined()
        ];
    }
}
