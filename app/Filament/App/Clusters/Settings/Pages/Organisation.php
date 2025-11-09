<?php

namespace App\Filament\App\Clusters\Settings\Pages;

use App\Filament\App\Clusters\Settings\SettingsCluster;
use Closure;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Support\Exceptions\Halt;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Model;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class Organisation extends Page implements HasSchemas
{
    use InteractsWithSchemas;

    protected string $view = 'filament.app.clusters.settings.pages.organisation';

    protected static ?string $cluster = SettingsCluster::class;

    protected static string|null|\UnitEnum $navigationGroup = 'Tvrtka';

    public ?array $data = [];

    protected static ?int $navigationSort = 1;

    protected static ?string $title = 'Informacije o tvrtki';

    protected static ?string $navigationLabel = 'Informacije ';

    protected static string|null|\BackedEnum $navigationIcon = Heroicon::BuildingOffice;

    protected Model|string|array|Closure|null $record;

    public static function canAccess(): bool
    {
        return auth()->user()->administrator;
    }

    public function mount(): void
    {
        $this->record = auth()->user()->organisation;

        $this->form->fill(auth()->user()->organisation->attributesToArray());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->columns(2)
            ->model(\App\Models\Organisation::class)
            ->schema([
                Section::make()
                    ->columnSpanFull()
                    ->schema([
                        FileUpload::make('logo')
                            ->directory('logos')
                            ->avatar()
                            ->alignCenter()
                            ->getUploadedFileNameForStorageUsing(function (TemporaryUploadedFile $file): string {
                                return auth()->user()->organisation->subdomain . '.' . $file->getClientOriginalExtension();
                            })
                            ->label('Logo tvrtke'),

                        TextInput::make('name')
                            ->label('Naziv tvrtke')
                            ->required(),

                        TextInput::make('oib')
                            ->label('OIB')
                            ->required()
                            ->minLength(11)
                            ->maxLength(11),

                        Toggle::make('in_vat_system')
                            ->label('U sustavu PDV-a')
                            ->inline(true)
                            ->default(false),

                        TextInput::make('address')
                            ->label('Adresa')
                            ->required(),

                        TextInput::make('city')
                            ->label('Grad')
                            ->required(),

                        TextInput::make('zip_code')
                            ->label('Poštanski broj')
                            ->required(),

                        TextInput::make('phone')
                            ->tel()
                            ->label('Telefon')
                            ->prefixIcon(Heroicon::Phone),

                        Select::make('country_id')
                            ->required()
                            ->relationship('country', 'name_native')
                            ->label('Država'),

                        Select::make('language_id')
                            ->required()
                            ->relationship('language', 'name_native')
                            ->label('Jezik'),


                    ]),
                Action::make('save')
                    ->action('save')
                    ->label('Spremi')
            ]);
    }

    public function save(): void
    {
        try {
            $data = $this->form->getState();

            auth()->user()->organisation->update($data);

            Notification::make()
                ->success()
                ->title("Spremljeno.")
                ->send();

        } catch (Halt $exception) {
            return;
        }
    }
}
