<?php

namespace App\Filament\Admin\Resources\Admins\Pages;

use App\Filament\Admin\Resources\Admins\AdminResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Contracts\Support\Htmlable;

class ManageAdmins extends ManageRecords
{
    protected static string $resource = AdminResource::class;

    public function getSubheading(): string|Htmlable|null
    {
     return 'Manage system admins.';
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
