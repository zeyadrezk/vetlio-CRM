<?php

namespace App\Filament\App\Resources\Patients\Pages;

use App\Filament\App\Resources\Patients\PatientResource;
use App\Filament\App\Schemas\ReminderForm;
use App\Filament\App\Tables\RemindersTable;
use BackedEnum;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Livewire;

class PatientReminders extends ManageRelatedRecords
{
    protected static string $resource = PatientResource::class;

    protected static string $relationship = 'reminders';

    protected static string|null|\BackedEnum $navigationIcon = Heroicon::Clock;

    protected static ?string $navigationLabel = 'Podsjetnici';

    protected static ?string $title = 'Podsjetnici';

    public static function getNavigationBadge(): ?string
    {
        $record = Livewire::current()->getRecord();

        return $record->reminders_count;
    }

    public function getSubheading(): string|null|\Illuminate\Contracts\Support\Htmlable
    {
        return $this->getRecord()->description;
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->fillForm(function ($data) {
                $data['user_to_remind_id'] = auth()->id();
                $data['remind_at'] = now()->addDays(1);

                return $data;
            })
        ];
    }

    public function form(Schema $schema): Schema
    {
        return ReminderForm::configure($schema);
    }

    public function table(Table $table): Table
    {
        return RemindersTable::configure($table);
    }
}
