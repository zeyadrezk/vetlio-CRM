<?php

namespace App\Filament\App\Actions;

use App\Enums\Icons\PhosphorIcons;
use App\Mail\GenericMail;
use Closure;
use CodeWithDennis\SimpleAlert\Components\SimpleAlert;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Mail;

class SendEmailAction extends Action
{
    protected ?array $receivers = null;
    protected ?array $ccReceivers = null;
    protected ?array $bccReceivers = null;
    protected string|\Closure|null $subject = null;
    protected string|\Closure|null $body = null;
    protected bool $useQueue = false;

    protected string|null $attachment = null;
    protected string|\Closure|null $attachmentName = null;

    public static function getDefaultName(): ?string
    {
        return 'sendEmail';
    }

    public function setUp(): void
    {
        parent::setUp();

        $this->icon(Heroicon::Envelope);
        $this->modalIcon(PhosphorIcons::Envelope);
        $this->successNotificationTitle('Email je uspješno poslan');
        $this->failureNotificationTitle('Greška pri slanju emaila');
        $this->hiddenLabel();
        $this->tooltip('Pošalji email');
        $this->outlined();

        $this->schema(fn() => [
            Hidden::make('showCc')
                ->live()
                ->default(fn() => filled($this->evaluate($this->ccReceivers))),

            Hidden::make('showBcc')
                ->reactive()
                ->default(fn() => filled($this->evaluate($this->bccReceivers))),

            TagsInput::make('receivers')
                ->hintActions([
                    Action::make('addCCReceiver')
                        ->icon(PhosphorIcons::Users)
                        ->label('Dodaj CC')
                        ->action(function (Get $get, Set $set) {
                            $set('showCc', !(bool)$get('showCc'));
                        }),
                    Action::make('addBCCReceiver')
                        ->icon(PhosphorIcons::Users)
                        ->label('Dodaj BCC')
                        ->action(function (Get $get, Set $set) {
                            $set('showBcc', !(bool)$get('showBcc'));
                        }),
                ])
                ->label('Primatelji')
                ->placeholder('Upišite e-mail adrese i potvrdite Enterom…')
                ->required()
                ->separator(',')
                ->default($this->evaluate($this->receivers))
                ->helperText('Najmanje jedan primatelj je obavezan.'),

            TagsInput::make('ccReceivers')
                ->label('CC')
                ->separator(',')
                ->visible(fn(Get $get) => (bool)$get('showCc'))
                ->default($this->evaluate($this->ccReceivers)),

            TagsInput::make('bccReceivers')
                ->label('BCC')
                ->separator(',')
                ->visible(fn(Get $get) => (bool)$get('showBcc'))
                ->default($this->evaluate($this->bccReceivers)),

            TextInput::make('subject')
                ->label('Predmet')
                ->required()
                ->maxLength(255)
                ->default($this->evaluate($this->subject)),

            RichEditor::make('body')
                ->label('Poruka')
                ->extraInputAttributes(['style' => 'min-height: 200px'])
                ->required()
                ->default($this->evaluate($this->body)),

            SimpleAlert::make('existing_attachment')
                ->title('Privitak za slanje')
                ->description(fn() => $this->evaluate($this->attachmentName))
                ->columnSpanFull()
                ->info()
                ->icon(Heroicon::PaperClip)
                ->visible(fn() => $this->attachment !== null),

            FileUpload::make('extra_attachments')
                ->label('Dodatni privitci')
                ->multiple()
                ->disk('local')                 // sve radimo na 'local'
                ->directory('emails/attachments')
                ->visibility('private')
                ->downloadable()
                ->maxSize(10240)
                ->hint('Dodajte PDF-ove ili slike kao dodatne priloge.'),
        ]);

        $this->action(function (array $data) {
            $to = $data['receivers'] ?? [];
            $cc = $data['ccReceivers'] ?? [];
            $bcc = $data['bccReceivers'] ?? [];

            if (empty($to) && empty($cc) && empty($bcc)) {
                $this->failureNotificationTitle('Potrebno je unijeti barem jednog primatelja (To, CC ili BCC).');
                $this->failure();
                return;
            }

            $primaryAttachmentPath = $this->attachment;

            $extraAttachments = array_values($data['extra_attachments'] ?? []);

            $mailable = new GenericMail(
                title: $data['subject'],
                body: $data['body'],
                attachmentPath: $primaryAttachmentPath,
                extraAttachments: $extraAttachments,
                disk: 'local',
            );

            $this->useQueue
                ? Mail::to($to)->cc($cc)->bcc($bcc)->queue($mailable)
                : Mail::to($to)->cc($cc)->bcc($bcc)->send($mailable);

            $this->success();
        });
    }

    public function receivers(array|callable|null $receivers): static
    {
        $this->receivers = $receivers;
        return $this;
    }

    public function ccReceivers(array|callable|null $cc): static
    {
        $this->ccReceivers = $cc;
        return $this;
    }

    public function bccReceivers(array|callable|null $bcc): static
    {
        $this->bccReceivers = $bcc;
        return $this;
    }

    public function subject(string|\Closure|null $subject): static
    {
        $this->subject = $subject;
        return $this;
    }

    public function body(string|\Closure|null $body): static
    {
        $this->body = $body;
        return $this;
    }

    public function queue(bool $state = true): static
    {
        $this->useQueue = $state;
        return $this;
    }

    public function attachment(string $relativePath, string|\Closure|null $displayName = null): static
    {
        $this->attachment = $relativePath;       // npr. 'emails/attachments/invoice-123.pdf'
        $this->attachmentName = $displayName;    // npr. 'invoice-123.pdf'
        return $this;
    }
}
