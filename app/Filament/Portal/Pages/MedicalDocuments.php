<?php

namespace App\Filament\Portal\Pages;

use App\Filament\Shared\Columns\CreatedAtColumn;
use App\Filament\Shared\Columns\UpdatedAtColumn;
use App\Models\MedicalDocument;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;

class MedicalDocuments extends Page implements HasTable
{
    use InteractsWithTable;

    protected string $view = 'filament.portal.pages.medical-documents';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::DocumentText;

    protected static ?string $title = 'Medical documents';

    protected static ?int $navigationSort = 4;

    public function getSubheading(): string|Htmlable|null
    {
        return 'View all medical documents from your pets and their details.';
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(MedicalDocument::query()->where('client_id', auth()->id()))
            ->columns([
                TextColumn::make('code')
                    ->label('Code')
                    ->tooltip(function ($record) {
                        return $record->locked_at ? 'The report is locked' : null;
                    })
                    ->icon(function ($record) {
                        return $record->locked_at ? Heroicon::LockClosed : null;
                    })
                    ->iconColor('danger')
                    ->searchable(),

                TextColumn::make('reservation.from')
                    ->sortable()
                    ->dateTime('d.m.Y H:i')
                    ->label('Reservation'),

                TextColumn::make('patient.name')
                    ->label('Patient')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('reason_for_coming')
                    ->label('Reason for visit')
                    ->searchable(),

                TextColumn::make('serviceProvider.name')
                    ->label('Doctor')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('items_sum_total')
                    ->money('EUR', 100)
                    ->label('Total')
                    ->sortable()
                    ->weight(FontWeight::Bold)
                    ->sum('items', 'total'),

                CreatedAtColumn::make('created_at'),
                UpdatedAtColumn::make('updated_at'),
            ])
            ->emptyStateActions([])
            ->emptyStateHeading('No medical records found')
            ->emptyStateDescription('You dont have and medical records yet.');
    }
}
