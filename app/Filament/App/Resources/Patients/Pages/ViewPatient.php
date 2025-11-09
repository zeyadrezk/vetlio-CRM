<?php

namespace App\Filament\App\Resources\Patients\Pages;

use App\Enums\Icons\PhosphorIcons;
use App\Filament\App\Actions\ClientCardAction;
use App\Filament\App\Resources\Patients\PatientResource;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;

class ViewPatient extends ViewRecord
{
    protected static string $resource = PatientResource::class;

    protected static ?string $title = 'Pregled';

    protected static ?string $navigationLabel = 'Pregled';

    public function getSubheading(): string|Htmlable|null
    {
        return $this->getRecord()->description;
    }

    protected function getHeaderActions(): array
    {
        return [
            ClientCardAction::make()
                ->record($this->getRecord()->client),
            Action::make('unarchive')
                ->visible(fn() => $this->getRecord()->archived_at != null)
                ->label('Dearhiviraj')
                ->icon(PhosphorIcons::FilePlus)
                ->color('success')
                ->modalIcon(PhosphorIcons::FilePlus)
                ->modalHeading('Dearhiviranje pacijenta')
                ->modalSubmitActionLabel('Dearhiviraj')
                ->successNotificationTitle('Pacijent je dearhiviran')
                ->requiresConfirmation()
                ->action(function ($data) {
                    $this->getRecord()->update([
                        'archived_at' => null,
                        'archived_note' => null,
                        'archived_by' => null,
                    ]);
                }),
            Action::make('archive')
                ->visible(fn() => $this->getRecord()->archived_at === null)
                ->label('Arhiviraj')
                ->icon(PhosphorIcons::FileMinus)
                ->color('danger')
                ->modalIcon(PhosphorIcons::FileMinus)
                ->modalHeading('Arhiviranje pacijenta')
                ->modalSubmitActionLabel('Arhiviraj')
                ->schema([
                    Textarea::make('archived_note')
                        ->rows(3)
                        ->label('Razlog arhiviranja')
                ])
                ->successNotificationTitle('Pacijent je arhiviran')
                ->requiresConfirmation()
                ->action(function ($data) {
                    $this->getRecord()->update([
                        'archived_at' => now(),
                        'archived_note' => (string)$data['archived_note'],
                        'archived_by' => auth()->user()->id,
                    ]);
                }),
            EditAction::make(),
        ];
    }
}
