<?php

namespace App\Filament\App\Clusters\Setup\Resources\Users\Pages;

use App\Filament\App\Clusters\Setup\Resources\Users\UserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;
}
