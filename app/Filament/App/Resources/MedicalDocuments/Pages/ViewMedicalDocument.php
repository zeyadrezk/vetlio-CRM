<?php

namespace App\Filament\App\Resources\MedicalDocuments\Pages;

use App\Enums\Icons\PhosphorIcons;
use App\Filament\App\Actions\ClientCardAction;
use App\Filament\App\Actions\PatientCardAction;
use App\Filament\App\Resources\Invoices\InvoiceResource;
use App\Filament\App\Resources\MedicalDocuments\MedicalDocumentResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Icons\Heroicon;

class ViewMedicalDocument extends ViewRecord
{
    protected static string $resource = MedicalDocumentResource::class;

    protected static ?string $navigationLabel = 'Pregled';

    public bool $showItemsToPay = true;

    protected function getHeaderActions(): array
    {
        return [
            Action::make(('create-invoice'))
                ->visible(function ($record) {
                    return !$record->isPaid();
                })
                ->color('success')
                ->icon(Heroicon::Eye)
                ->label('Izradi račun')
                ->action(function ($record, $action) {
                    $recordIds = $record->items->pluck('id')->toArray();

                    $action->redirect(InvoiceResource::getUrl('create', [
                        'medicalDocumentItems' => implode(',', $recordIds),
                        'client' => $this->getRecord()->client_id,
                    ]));
                }),

            Action::make(('toggle-items'))
                ->outlined(function () {
                    return !$this->showItemsToPay;
                })
                ->hiddenLabel()
                ->icon(PhosphorIcons::Eye)
                ->tooltip('Prikaži stavke za naplatu')
                ->action(function () {
                    $this->showItemsToPay = !$this->showItemsToPay;
                }),

            Action::make('lock')
                ->icon(Heroicon::LockClosed)
                ->outlined()
                ->color('danger')
                ->tooltip('Zaključaj nalaz')
                ->modalHeading('Zaključavanje nalaza')
                ->modalIcon(Heroicon::LockClosed)
                ->modalDescription('Nalaz će biti zaključan, naknadne izmjene se više neće moći napraviti. Jeste li sigurni?')
                ->visible(function ($record) {
                    return !$record->locked_at;
                })
                ->hiddenLabel()
                ->requiresConfirmation()
                ->successNotificationTitle('Nalaz je uspješno zaključan')
                ->action(function ($record) {
                    $record->update([
                        'locked_at' => now(),
                        'locked_user_id' => auth()->user()->id,
                    ]);
                }),

            ClientCardAction::make(),
            PatientCardAction::make(),

            Action::make('print')
                ->outlined()
                ->hiddenLabel()
                ->icon(Heroicon::Printer),
            EditAction::make()
                ->outlined(),
            DeleteAction::make()
                ->visible(auth()->user()->administrator)
        ];
    }
}
