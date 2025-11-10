<?php

namespace App\Filament\Portal\Pages;

use App\Enums\Icons\PhosphorIcons;
use App\Filament\Portal\Widgets\ClientStats;
use BackedEnum;
use CodeWithDennis\SimpleAlert\Components\SimpleAlert;
use Filament\Actions\Action;
use Filament\Infolists\Components\TextEntry;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

class Dashboard extends Page implements HasSchemas
{
    use InteractsWithSchemas;

    protected string $view = 'filament.portal.pages.dashboard';

    protected static ?string $title = 'Dashboard';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Home;

    public function getSubheading(): string|Htmlable|null
    {
        return 'Welcome back, ' . auth()->user()->full_name . '.';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('new-appointment')
                ->icon(PhosphorIcons::CalendarPlus)
                ->color('success')
                ->label('New appointment')
        ];
    }

    public function beforeConsultationBanner(Schema $schema)
    {
        return $schema
            ->columns(4)
            ->components([
                SimpleAlert::make('vaccination-banner')
                    ->warning()
                    ->icon(PhosphorIcons::Virus)
                    ->info()
                    ->title('Vaccination reminder for Rex')
                    ->actions([
                        Action::make('view-vaccination')
                            ->link()
                            ->color('info')
                            ->label('Schedule vaccination')
                            ->icon(PhosphorIcons::CalendarPlus)
                    ])
                    ->description(function () {
                        $fullName = auth()->user()->first_name;

                        return "Hi, {$fullName}, vaccination for you pet Rex is due on 20.03.2025";
                    })->columnSpanFull(),

                Section::make('How to prepare your pet for an examination?')
                    ->columnSpan(2)
                    ->icon(PhosphorIcons::Dog)
                    ->schema([
                        TextEntry::make('preparation-list')
                            ->hiddenLabel()
                            ->bulleted()
                            ->state([
                                'Bring your pet’s health booklet or any previous medical records – if you have them at home.',
                                'Avoid feeding your pet 2–3 hours before the appointment, unless your veterinarian advises otherwise.',
                                'Take your pet for a short walk before arriving, to help them relax.',
                                'If your pet is anxious, bring their favorite blanket or treat 🧸',
                            ]),
                        TextEntry::make('preparation-list-2')
                            ->state('View more tips')
                            ->hiddenLabel()
                            ->url('/portal/tips')
                            ->icon(PhosphorIcons::ArrowRight)
                            ->weight(FontWeight::SemiBold)
                    ])
            ]);
    }

    protected function getHeaderWidgets(): array
    {
        return [
            ClientStats::make()
        ];
    }
}
