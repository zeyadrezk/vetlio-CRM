<?php

namespace App\Filament\Admin\Resources\Admins;

use App\Filament\Admin\Resources\Admins\Pages\ManageAdmins;
use App\Filament\Shared\Columns\CreatedAtColumn;
use App\Filament\Shared\Columns\UpdatedAtColumn;
use App\Filament\Shared\Fields\EmailField;
use App\Models\Admin;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class AdminResource extends Resource
{
    protected static ?string $model = Admin::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Users;

    protected static ?string $recordTitleAttribute = 'first_name';

    protected static ?string $navigationLabel = 'Admins';

    protected static ?string $label = 'admin';

    protected static ?string $pluralLabel = 'admins';

    public static function getNavigationBadge(): ?string
    {
        return self::getModel()::count();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('first_name')
                    ->label('First Name')
                    ->required(),

                TextInput::make('last_name')
                    ->label('Last Name')
                    ->required(),

                EmailField::make('email')
                    ->unique('admins', 'email', ignoreRecord: true),

                TextInput::make('password')
                    ->password()
                    ->revealable()
                    ->label('Password')
                    ->required(function (?Admin $record) {
                        return $record == null;
                    })
                    ->disabled(function (?Admin $record) {
                        return $record != null;
                    }),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('first_name')
            ->columns([
                TextColumn::make('full_name')
                    ->label('Full Name')
                    ->copyable()
                    ->searchable(),

                TextColumn::make('email')
                    ->label('Email address')
                    ->copyable()
                    ->searchable(),

                ToggleColumn::make('active')
                    ->disabled(function (Admin $record) {
                        return $record->id = auth()->id();
                    })
                    ->label('Active'),

                CreatedAtColumn::make(),
                UpdatedAtColumn::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageAdmins::route('/'),
        ];
    }

}
