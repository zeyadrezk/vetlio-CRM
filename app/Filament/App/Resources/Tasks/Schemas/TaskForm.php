<?php

namespace App\Filament\App\Resources\Tasks\Schemas;

use App\Enums\Priority;
use App\Filament\Fields\PriorityField;
use App\Models\Client;
use App\Models\Contract;
use App\Models\Invoice;
use App\Models\Lead;
use App\Models\MedicalDocument;
use App\Models\Offer;
use App\Models\Patient;
use App\Models\Project;
use App\Models\Ticket;
use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\MorphToSelect;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\SpatieTagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;

class TaskForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->columnSpanFull()
                    ->label('Naziv')
                    ->required(),

                Grid::make(2)
                    ->columnSpan(1)
                    ->schema([
                        DatePicker::make('start_at')
                            ->required()
                            ->default(now())
                            ->label('Početak'),

                        DatePicker::make('deadline_at')
                            ->after('start_at')
                            ->label('Rok za završetak'),
                    ]),

                Select::make('priority_id')
                    ->label('Prioritet')
                    ->default(1)
                    ->options(Priority::class)
                    ->required(),

                Select::make('assignedUsers')
                    ->relationship('assignedUsers', 'first_name')
                    ->options(User::get()->pluck('full_name', 'id'))
                    ->label('Dodjeljeno')
                    ->multiple(),

                SpatieTagsInput::make('tags')
                    ->label('Oznake'),

                MorphToSelect::make('related')
                    ->contained(false)
                    ->label('Vezan za')
                    ->required()
                    ->native(false)
                    ->columnSpanFull()
                    ->columns(2)
                    ->extraAttributes([
                        'class' => 'morph-related-select'
                    ])
                    ->types([
                        MorphToSelect\Type::make(Client::class)
                            ->label('Klijent')
                            ->searchColumns(['first_name'])
                            ->titleAttribute('first_name'),
                        MorphToSelect\Type::make(Patient::class)
                            ->label('Pacijent')
                            ->searchColumns(['name'])
                            ->titleAttribute('name'),
                        MorphToSelect\Type::make(Invoice::class)
                            ->label('Račun')
                            ->searchColumns(['code'])
                            ->titleAttribute('code'),
                        MorphToSelect\Type::make(MedicalDocument::class)
                            ->label('Nalaz')
                            ->searchColumns(['code'])
                            ->titleAttribute('code'),
                    ])->modifyKeySelectUsing(function (Select $select) {
                        $select->searchable(true);
                    }),

                RichEditor::make('description')
                    ->extraAttributes([
                        'style' => 'min-height: 200px'
                    ])
                    ->label('Opis')
                    ->columnSpanFull(),

                SpatieMediaLibraryFileUpload::make('attachments')
                    ->label('Prilozi')
                    ->columnSpanFull()
                    ->multiple()
            ]);
    }
}
