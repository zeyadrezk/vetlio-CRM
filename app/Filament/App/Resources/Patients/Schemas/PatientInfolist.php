<?php

namespace App\Filament\App\Resources\Patients\Schemas;

use App\Enums\Icons\PhosphorIcons;
use CodeWithDennis\SimpleAlert\Components\SimpleAlert;
use Filament\Actions\Action;
use Filament\Schemas\Schema;

class PatientInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                SimpleAlert::make('archived-info')
                    ->warning()
                    ->border()
                    ->actions([
                        Action::make('unarchive-patient')
                            ->label('Dearhiviraj')
                            ->icon(PhosphorIcons::FilePlus)
                            ->link()
                            ->modalIcon(PhosphorIcons::FilePlus)
                            ->modalHeading('Dearhiviranje pacijenta')
                            ->modalSubmitActionLabel('Dearhiviraj')
                            ->successNotificationTitle('Pacijent je dearhiviran')
                            ->requiresConfirmation()
                            ->action(function ($record) {
                                $record->update([
                                    'archived_at' => null,
                                    'archived_note' => null,
                                    'archived_by' => null,
                                ]);
                            })
                    ])
                    ->columnSpanFull()
                    ->visible(fn($record) => $record->archived_at)
                    ->title('Pacijent je arhiviran')
                    ->description(function ($record) {
                        $archivedNote = $record->archived_note ? 'Razlog arhiviranja: ' . $record->archived_note : '-';
                        return $archivedNote ? $archivedNote : 'Nema razloga';
                    }),

                SimpleAlert::make('dangerous-info')
                    ->danger()
                    ->border()
                    ->columnSpanFull()
                    ->visible(fn($record) => $record->dangerous && $record->archived_at == null)
                    ->title('Pacijent je opasan')
                    ->description(function ($record) {
                        return $record->dangerous_note ? 'Napomena: ' . $record->dangerous_note : null;
                    })
            ]);
    }
}
