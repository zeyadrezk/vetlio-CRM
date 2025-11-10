<?php

namespace App\Filament\Portal\Pages;

use App\Enums\Icons\PhosphorIcons;
use App\Filament\Portal\Widgets\ClientStats;
use App\Models\AppointmentRequest;
use App\Models\Branch;
use App\Models\Patient;
use App\Models\Service;
use BackedEnum;
use Carbon\Carbon;
use CodeWithDennis\SimpleAlert\Components\SimpleAlert;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Pages\Page;
use Filament\Schemas\Components\Flex;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Collection;

class Dashboard extends Page implements HasSchemas
{
    use InteractsWithSchemas;

    protected string $view = 'filament.portal.pages.dashboard';

    protected static ?string $title = 'Dashboard';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Home;

    public $defaultAction = 'unreadAnnouncements';

    public bool $hasUnreadAnnouncements = false;

    public function mount(): void
    {
        $this->hasUnreadAnnouncements = auth()->user()->unreadAnnouncements()->exists();
    }

    public function getSubheading(): string|Htmlable|null
    {
        return 'Welcome back, ' . auth()->user()->full_name . '.';
    }

    private function calculateAvailableSlots($get): Collection
    {
        $date = Carbon::parse($get('date'))->format('Y-m-d');

        $service = Service::with('users')->find($get('service_id'));

        $slots = $service->users->flatMap(function ($user) use ($date, $service) {
            return collect($user->getAvailableSlots(
                date: $date,
                slotDuration: $service->duration->minute
            ))->map(function ($slot) use ($user) {
                $slot['user'] = $user->only('id', 'full_name');
                return $slot;
            });
        });

        return collect($slots)
            ->filter(fn($slot) => $slot['is_available']);
    }

    /**
     * @return Action
     */
    public function createAppointmentRequestAction(): Action
    {
        $branches = Branch::where('organisation_id', auth()->user()->organisation_id)->get();
        $patients = Patient::where('client_id', auth()->id())->get();
        $services = Service::all();

        return Action::make('new-appointment')
            ->requiresConfirmation()
            ->slideOver()
            ->modalWidth(Width::FourExtraLarge)
            ->modalHeading('New appointment request')
            ->modalDescription('Select date and time for your appointment')
            ->modalIcon(PhosphorIcons::CalendarPlus)
            ->steps([
                Step::make('Select pet')
                    ->icon(PhosphorIcons::Dog)
                    ->schema(function () use ($patients, $services, $branches) {
                        return [
                            Radio::make('branch_id')
                                ->label('Select preferred location')
                                ->options(function () use ($patients, $branches) {
                                    return $branches->pluck('name', 'id');
                                })
                                ->descriptions(function () use ($branches) {
                                    return $branches
                                        ->mapWithKeys(fn($branch) => [
                                            $branch->id => $branch->name
                                        ])
                                        ->toArray();
                                }),

                            Radio::make('patient_id')
                                ->label('Select your pet')
                                ->options(function () use ($patients) {
                                    return $patients->pluck('name', 'id');
                                })
                                ->descriptions(function () use ($patients) {
                                    return $patients
                                        ->mapWithKeys(fn($patient) => [
                                            $patient->id => "{$patient->species->name} ({$patient->breed->name})"
                                        ])
                                        ->toArray();
                                }),

                            Flex::make([
                                TextInput::make('reason_for_comming')
                                    ->label('Reason for coming'),

                                Select::make('service_id')
                                    ->live(true)
                                    ->label('Select service')
                                    ->options($services->pluck('name', 'id'))
                                    ->required()
                            ])
                        ];
                    }),
                Step::make('Select time')
                    ->icon(Heroicon::Clock)
                    ->schema([
                        DatePicker::make('date')
                            ->label('Select date')
                            ->native(false)
                            ->live(true)
                            ->default(now())
                            ->required(),

                        Radio::make('time')
                            ->hiddenLabel()
                            ->columns(3)
                            ->disabled(function ($get) {
                                return !$get('date');
                            })
                            ->descriptions(function ($get) {
                                if (!$get('date') || !$get('service_id')) return [];

                                return $this->calculateAvailableSlots($get)
                                    ->mapWithKeys(fn($slot) => [$slot['start_time'] => $slot['user']['full_name']])
                                    ->toArray();
                            })
                            ->options(function ($get) {
                                if (!$get('date') || !$get('service_id')) return [];

                                return $this->calculateAvailableSlots($get)
                                    ->mapWithKeys(fn($slot) => [$slot['start_time'] => $slot['start_time']])
                                    ->toArray();
                            })
                    ]),
                Step::make('Additional information')
                    ->icon(PhosphorIcons::Note)
                    ->schema([
                        Textarea::make('note')
                            ->hint('Enter any additional information about the appointment, such as special instructions or any other details.')
                            ->label('Note'),

                        FileUpload::make('attachments')
                            ->label('Attachments')
                    ]),
                Step::make('Summary')
                    ->icon(Heroicon::CheckCircle)
                    ->schema([
                        Text::make('Summary of your request')
                            ->size(TextSize::Large)
                            ->icon(PhosphorIcons::CheckCircleBold)
                            ->weight(FontWeight::Bold),

                        TextEntry::make('patient.name')
                            ->label('Pet')
                            ->icon(PhosphorIcons::Dog)
                            ->getStateUsing(function ($get, $action) use ($patients) {
                                if (!$get('patient_id')) return null;

                                return $patients->find($get('patient_id'))->name;
                            })
                    ])
            ])
            ->action(function (array $data) {
                $serviceDuration = Service::find($data['service_id'])->duration->minute;

                $date = Carbon::parse($data['date']);
                $from = $date->copy()->setTimeFromTimeString($data['time']);
                $to = $from->copy()->addMinutes($serviceDuration);

                $data['from'] = $from;
                $data['to'] = $to;
                $data['client_id'] = auth()->id();
                $data['service_provider_id'] = 1;
                $data['approval_status_id'] = 1;
                $data['organisation_id'] = auth()->user()->organisation_id;

                AppointmentRequest::create($data);
            })
            ->successNotificationTitle('Appointment request created successfully')
            ->icon(PhosphorIcons::CalendarPlus)
            ->color('success')
            ->label('New appointment');
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

    protected function getHeaderActions(): array
    {
        return [
            $this->announcementsAction(),
            $this->createAppointmentRequestAction()
        ];
    }

    public function dashboardContent(Schema $schema)
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
