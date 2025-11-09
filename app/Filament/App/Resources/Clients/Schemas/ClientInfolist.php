<?php

namespace App\Filament\App\Resources\Clients\Schemas;

use App\Filament\App\Resources\Clients\ClientResource;
use App\Models\Client;
use CodeWithDennis\SimpleAlert\Components\SimpleAlert;
use Filament\Actions\Action;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ClientInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                self::getClientInactiveNotification(),
                TextEntry::make('name')
                    ->label('Klijent')
            ]);
    }

    private static function getClientInactiveNotification()
    {
        return SimpleAlert::make('client-inactive')
            ->columnSpanFull()
            ->warning()
            ->visible(function (Client $record) {
                return !$record->active;
            })
            ->border()
            ->actions([
                Action::make('activate-client')
                    ->link()
                    ->icon(ClientResource::getNavigationIcon())
                    ->label('Aktiviraj')
                    ->action(function (Client $record) {
                        $record->update([
                            'active' => true
                        ]);
                    })
            ])
            ->icon('heroicon-o-arrow-path')
            ->description('Klijent nije aktivan, neke funkcionalnosti će biti limitirane.');
    }
}
