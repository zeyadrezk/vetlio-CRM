<?php

namespace App\Filament\Public\Pages;

use App\Enums\Icons\PhosphorIcons;
use App\Filament\App\Entries\PlaceholderEntry;
use App\Models\Reservation;
use Filament\Actions\Action;
use Filament\Forms\Components\ToggleButtons;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Filament\Support\Enums\Width;
use Illuminate\Contracts\Support\Htmlable;
use Livewire\Attributes\Locked;

class ConfirmAppointmentArrival extends Page implements HasSchemas
{
    use InteractsWithSchemas;

    protected string $view = 'filament.public.pages.confirm-appointment-arrival';

    protected static ?string $slug = 'appointment/confirm/{uuid}';

    protected ?string $subheading = 'Please confirm that you plan to arrive at the appointment.';

    #[Locked]
    public ?Reservation $appointment;

    public ?array $data = [];

    protected Width|string|null $maxContentWidth = Width::ThreeExtraLarge;

    public function getTitle(): string|Htmlable
    {
        return $this->appointment->organisation->name . ' - Confirm Appointment Arrival';
    }

    public function mount(): void
    {
        $this->appointment = $this->resolveRecord();

        $this->form->fill($this->appointment?->attributesToArray());
    }

    public function appointmentInformation(Schema $schema): Schema
    {
        return $schema
            ->record($this->appointment)
            ->schema([
                ImageEntry::make('organisation.logo')
                    ->hiddenLabel()
                    ->circular()
                    ->alignCenter(),

                Grid::make(1)
                    ->gap(false)
                    ->schema([
                        TextEntry::make('organisation.name')
                            ->hiddenLabel()
                            ->weight(FontWeight::Bold)
                            ->size(TextSize::Large)
                            ->alignCenter(),

                        TextEntry::make('branch.name')
                            ->size(TextSize::Large)
                            ->hiddenLabel()
                            ->alignCenter(),

                        TextEntry::make('branch.full_address')
                            ->weight(FontWeight::SemiBold)
                            ->alignCenter()
                            ->icon(PhosphorIcons::MapPin)
                            ->hiddenLabel()
                            ->size(TextSize::Small),

                        PlaceholderEntry::make('divider')
                            ->extraAttributes([
                                'class' => 'm-8 border-gray-200'
                            ]),

                        ToggleButtons::make('status')
                            ->boolean('Arriving', 'Not ariving')
                            ->label('Manage your arrival')
                            ->grouped()
                            ->extraAttributes([
                                'style' => 'width: 100%',
                            ])
                            ->icons([
                                true => PhosphorIcons::CheckCircleBold,
                                false => PhosphorIcons::XCircleBold,
                            ])
                            ->colors([
                                true => 'success',
                                false => 'danger',
                            ]),


                    ]),

                Grid::make(2)
                    ->schema([
                        TextEntry::make('from')
                            ->alignBetween()
                            ->weight(FontWeight::Bold)
                            ->size(TextSize::Large)
                            ->label('Appointment at')
                            ->icon(PhosphorIcons::Clock)
                            ->dateTime('d.m.Y H:i'),

                        TextEntry::make('service.name')
                            ->alignBetween()
                            ->weight(FontWeight::Bold)
                            ->size(TextSize::Large)
                            ->label('Service')
                            ->icon(PhosphorIcons::Hand),

                        TextEntry::make('patient.name')
                            ->alignBetween()
                            ->weight(FontWeight::Bold)
                            ->size(TextSize::Large)
                            ->label('Pet')
                            ->icon(PhosphorIcons::Dog),

                        TextEntry::make('serviceProvider.full_name')
                            ->alignBetween()
                            ->weight(FontWeight::Bold)
                            ->size(TextSize::Large)
                            ->label('Vet')
                            ->icon(PhosphorIcons::User)
                    ])


            ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Form::make([

                ])->livewireSubmitHandler('save')
                    ->footer([
                        Actions::make([
                            Action::make('save')
                                ->submit('save')
                                ->keyBindings(['mod+s']),
                        ]),
                    ]),
            ])
            ->record($this->appointment)
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $record = $this->appointment;

        $record->fill($data);
        $record->save();

        Notification::make()
            ->success()
            ->title('Saved')
            ->send();
    }

    public function resolveRecord(): ?Reservation
    {
        return Reservation::whereUuid(request('uuid'))
            ->canceled(false)
            ->ordered()
            ->confirmed(false)
            ->first();
    }
}
