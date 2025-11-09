<?php

namespace App\Filament\App\Schemas;

use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class NoteForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                TextInput::make('title')
                    ->label('Naslov')
                    ->required(),

                RichEditor::make('note')
                    ->label('Napomena')
                    ->required()
                    ->extraAttributes([
                        'style' => 'min-height: 200px',
                    ]),

                SpatieMediaLibraryFileUpload::make('attachments')
                    ->multiple()
                    ->label('Privitci')
            ]);
    }
}
