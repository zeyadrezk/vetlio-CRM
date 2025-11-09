<?php

namespace App\Filament\App\Resources\Invoices\Pages;

use App\Enums\Icons\PhosphorIcons;
use App\Enums\PaymentMethod;
use App\Filament\App\Actions\ClientCardAction;
use App\Filament\App\Actions\SendEmailAction;
use App\Filament\App\Resources\Invoices\InvoiceResource;
use App\Filament\App\Resources\Payments\Schemas\PaymentForm;
use App\Models\Invoice;
use App\Models\Payment;
use App\Services\InvoiceService;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Number;

class ViewInvoice extends ViewRecord
{
    protected static string $resource = InvoiceResource::class;

    protected static ?SubNavigationPosition $subNavigationPosition = SubNavigationPosition::End;

    protected static ?string $navigationLabel = 'Račun';

    public function getTitle(): string
    {
        return 'Račun: ' . $this->getRecord()->code;
    }

    public function getSubheading(): string|Htmlable|null
    {
        return 'Klijent: ' . $this->getRecord()->client->full_name;
    }

    public function sendInvoiceByEmailAction(): SendEmailAction
    {
        return SendEmailAction::make()
            ->fillForm(function ($data) {
                $data['receivers'] = [$this->getRecord()->client->email];
                $data['subject'] = 'Vaša faktura: ' . $this->getRecord()->code;
                $data['body'] = 'U privitku Vam dostavljamo fakturu. Ukupan iznos je: ' . Number::currency($this->getRecord()->total) . '';
                return $data;
            })
            ->outlined()
            ->hiddenLabel()
            ->subject('Vaša faktura')
            ->label('Pošalji email')
            ->icon(PhosphorIcons::Envelope);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('cancel')
                ->hidden(function ($record) {
                    return $record->storno_of_id != null;
                })
                ->label('Storniraj')
                ->requiresConfirmation()
                ->icon(PhosphorIcons::Invoice)
                ->color('danger')
                ->successNotificationTitle('Račun je uspješno storniran')
                ->successRedirectUrl(function ($record) {
                    return InvoiceResource::getUrl('view', ['record' => $record->canceledInvoice]);
                })
                ->action(function ($record, Action $action) {
                    app(InvoiceService::class)->cancelInvoice($record);
                }),
            ActionGroup::make([
                Action::make('print')
                    ->label('Ispis')
                    ->outlined()
                    ->url(function (Invoice $record) {
                        return route('print.invoices.inline', ['record' => $record]);
                    })
                    ->icon(PhosphorIcons::Printer)
                    ->openUrlInNewTab(),

                Action::make('pdf')
                    ->label('Otvori PDF')
                    ->outlined()
                    ->url(function (Invoice $record) {
                        return route('print.invoices.download', ['record' => $record]);
                    })
                    ->icon(PhosphorIcons::Printer)
                    ->openUrlInNewTab()
                    ->icon(PhosphorIcons::FilePdfFill),

                Action::make('download-pdf')
                    ->label('Preuzmi PDF')
                    ->outlined()
                    ->schema([
                        TextInput::make('code')
                    ])
                    ->action(function () {
                        dd("radim print...");
                    })
                    ->icon(PhosphorIcons::Download),
            ])->hiddenLabel()->icon(Heroicon::Printer)->button()->outlined(),

            $this->sendInvoiceByEmailAction(),

            ActionGroup::make([
                ClientCardAction::make()
                    ->record($this->getRecord()->client),
            ])->label('Više')->button()->outlined(),

            Action::make('createPayment')
                ->label('Dodaj uplatu')
                ->color('success')
                ->icon(PhosphorIcons::CreditCard)
                ->modalIcon(PhosphorIcons::CreditCard)
                ->modalHeading('Uplata na račun')
                ->model(Payment::class)
                ->schema(function ($schema) {
                    return PaymentForm::configure($schema)->columns(2);
                })
                ->fillForm(function ($data) {
                    $data['payment_at'] = now();
                    $data['client_id'] = $this->getRecord()->client_id;
                    $data['branch_id'] = $this->getRecord()->branch_id;
                    $data['amount'] = $this->getRecord()->total;
                    $data['payment_method_id'] = PaymentMethod::BANK;
                    $data['note'] = 'Uplata za račun: ' . $this->getRecord()->code;

                    return $data;
                })
                ->successNotificationTitle('Uplata uspješno dodana')
                ->action(function ($record, $data) {
                    $record->payments()->create($data);
                })
                ->visible(function ($record) {
                    return !$record->payed;
                }),
        ];
    }
}
