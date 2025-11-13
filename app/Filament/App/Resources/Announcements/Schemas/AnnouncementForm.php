<?php

namespace App\Filament\App\Resources\Announcements\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class AnnouncementForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->columnSpanFull()
                    ->label('Title')
                    ->required(),

                RichEditor::make('content')
                    ->required()
                    ->extraInputAttributes([
                        'style' => 'min-height: 200px',
                    ])
                    ->label('Content')
                    ->columnSpanFull(),

                Toggle::make('for_users')
                    ->inline(false)
                    ->hint('Users will be notified about this announcement')
                    ->default(true),

                Toggle::make('for_clients')
                    ->inline(false)
                    ->hint('Clients will be notified about this announcement')
                    ->default(false),

                DatePicker::make('starts_at')
                    ->required()
                    ->label('Start date')
                    ->default(now()),

                DatePicker::make('ends_at')
                    ->after('starts_at')
                    ->required()
                    ->label('End date')
                    ->default(now()->addDays(2)),

            ]);
    }
}
