<?php

namespace App\Filament\App\Schemas;

use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class DocumentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                TextInput::make('title')
                    ->placeholder('Naziv datoteke')
                    ->label('Naziv')
                    ->required()
                    ->maxLength(255),
                Textarea::make('description')
                    ->placeholder('Opis datoteka')
                    ->label('Opis')
                    ->maxLength(255),

                Toggle::make('visible_in_portal')
                    ->label('Prikaz u portalu'),

                SpatieMediaLibraryFileUpload::make('media')
                    ->label('Datoteke')
                    ->downloadable()
                    ->collection('documents')
                    ->multiple()
            ]);
    }
}
