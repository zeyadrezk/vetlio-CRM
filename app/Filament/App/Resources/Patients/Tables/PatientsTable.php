<?php

namespace App\Filament\App\Resources\Patients\Tables;

use App\Enums\PatientGender;
use App\Filament\App\Actions\ClientCardAction;
use App\Models\Patient;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;

class PatientsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function ($query) {
                return $query->with(['client', 'breed', 'species']);
            })
            ->columns([
                ImageColumn::make('photo')
                    ->label('')
                    ->width('40px')
                    ->grow(false)
                    ->circular(),

                TextColumn::make('name')
                    ->sortable()
                    ->description(function (Patient $record) {
                        return $record->breed->name . ', ' . $record->species->name;
                    })
                    ->searchable()
                    ->label('Ime'),

                TextColumn::make('gender_id')
                    ->sortable()
                    ->formatStateUsing(function ($state) {
                        return PatientGender::from($state)->getLabel();
                    })
                    ->searchable()
                    ->label('Spol'),

                TextColumn::make('date_of_birth')
                    ->label('Datum rođenja')
                    ->date()
                    ->sortable()
                    ->description(function ($state) {
                        if ($state != null) {
                            return Carbon::parse($state)->diffInYears(now()) . " godina";
                        }

                        return null;
                    }),

                TextColumn::make('client.full_name')
                    ->sortable()
                    ->searchable(true, function ($query, $search) {
                        return $query->whereHas('client', function ($query) use ($search) {
                            return $query->where('first_name', 'like', "%{$search}%")
                                ->orWhere('last_name', 'like', "%{$search}%");
                        });
                    })
                    ->label('Vlasnik'),

                TextColumn::make('remarks')
                    ->searchable()
                    ->label('Opaska'),
            ])
            ->recordActions([
                ViewAction::make(),
                ClientCardAction::make()
                    ->hiddenLabel()
                    ->record(function ($record) {
                        return $record->client;
                    }),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
