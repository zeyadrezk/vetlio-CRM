<?php

namespace App\Filament\App\Pages;

use App\Enums\Icons\PhosphorIcons;
use App\Filament\App\Widgets\AppointmentsTodayWidget;
use App\Filament\App\Widgets\RevenueChart;
use App\Filament\App\Widgets\StatsOverview;
use BackedEnum;
use CodeWithDennis\SimpleAlert\Components\SimpleAlert;
use Filament\Actions\Action;
use Filament\Infolists\Components\TextEntry;
use Filament\Pages\Page;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

class Dashboard extends Page
{
    protected string $view = 'filament.app.pages.dashboard';

    protected static ?int $navigationSort = -1;

    protected static ?string $navigationLabel = 'Dashboard';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Home;

    public $defaultAction = 'unreadAnnouncements';

    public bool $hasUnreadAnnouncements = false;

    public function mount(): void
    {
        $this->hasUnreadAnnouncements = auth()->user()->unreadAnnouncements()->exists();
    }

    protected function getHeaderActions(): array
    {
        return [
            $this->announcementsAction(),
        ];
    }

    public function announcementsAction(): Action
    {
        return Action::make('unreadAnnouncements')
            ->modalWidth(Width::ExtraLarge)
            ->color('warning')
            ->extraAttributes([
                'style' => 'display:none;'
            ])
            ->label('New announcements')
            ->visible(fn() => $this->hasUnreadAnnouncements)
            ->icon(PhosphorIcons::Bell)
            ->modalHeading(fn($record) => $record?->title ?? 'No new announcements')
            ->modalDescription(fn($record) => $record ? 'Announcement from: ' . $record->user->full_name : null)
            ->modalIcon(PhosphorIcons::Bell)
            ->formWrapper(false)
            ->closeModalByClickingAway(false)
            ->closeModalByEscaping(false)
            ->record(function () {
                return auth()->user()->nextUnreadAnnouncement();
            })
            ->modalCloseButton(fn($record) => !filled($record))
            ->modalSubmitAction(false)
            ->modalCancelAction(false)
            ->extraModalFooterActions(function ($action) {
                $record = $action->getRecord();

                if (!$record) {
                    return [
                        Action::make('close')
                            ->label('Zatvori ')
                            ->color('gray')
                            ->close()
                            ->icon(PhosphorIcons::X),
                    ];
                }
                return [
                    Action::make('next')
                        ->label('Ok, I read it!')
                        ->link()
                        ->visible(fn($record) => filled($record))
                        ->icon(PhosphorIcons::Check)
                        ->color('success')
                        ->action(function ($record, Action $action) {
                            $user = auth()->user();

                            $user->markAnnouncementAsRead($record);

                            $next = $user->nextUnreadAnnouncement();

                            if ($next) {
                                $action->record($next);
                                $action->getRecord()->refresh();
                            } else {
                                $action->record(null);

                            }
                        }),
                ];
            })
            ->schema([
                TextEntry::make('content')
                    ->hiddenLabel()
                    ->html()
                    ->visible(fn($record) => filled($record)),

                SimpleAlert::make('announcement-banner')
                    ->warning()
                    ->visible(fn($record) => blank($record))
                    ->border()
                    ->columnSpanFull()
                    ->icon(PhosphorIcons::CheckCircleBold)
                    ->title('You have no unread announcements')
            ]);
    }

    public function getTitle(): string|Htmlable
    {
        return 'Hi, ' . auth()->user()->first_name;
    }

    public function getHeaderWidgets(): array
    {
        return [
            StatsOverview::class,
            AppointmentsTodayWidget::class,
            RevenueChart::class,
        ];
    }

    public function getColumns(): int|array
    {
        return [
            'sm' => 1,
            'xl' => 2,
            '2xl' => 3,
        ];
    }
}
