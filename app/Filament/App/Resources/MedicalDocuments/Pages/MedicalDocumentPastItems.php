<?php

namespace App\Filament\App\Resources\MedicalDocuments\Pages;

use App\Enums\Icons\PhosphorIcons;
use App\Filament\App\Resources\MedicalDocuments\MedicalDocumentResource;
use BackedEnum;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables\Table;
use Livewire\Livewire;

class MedicalDocumentPastItems extends ManageRelatedRecords
{
    protected static string $resource = MedicalDocumentResource::class;

    protected static string $relationship = 'pastMedicalDocuments';

    protected static string|BackedEnum|null $navigationIcon = PhosphorIcons::ClockCountdown;

    protected static ?string $relatedResource = MedicalDocumentResource::class;

    protected static ?string $title = 'Prethodni nalazi';

    protected static ?string $navigationLabel = 'Prethodni nalazi';

    public static function getNavigationBadge(): ?string
    {
        $record = Livewire::current()->getRecord();

        return $record->past_medical_documents_count;
    }

    public function table(Table $table): Table
    {
        return $table
            ->emptyStateActions([])
            ->emptyStateDescription('Trenutno nema prethodnih nalaza');
    }
}
