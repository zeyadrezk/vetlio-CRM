<?php

return [
    'send_email' => [
        'title' => 'Send email',
        'success' => 'Email sent successfully',
        'error' => 'Error sending email',

        'fields' => [
            'recipients' => 'Recipients',
            'cc' => 'CC',
            'bcc' => 'BCC',
            'subject' => 'Subject',
            'message' => 'Message',
            'attachment' => 'Attachment to send',
            'additional_attachments' => 'Additional attachments',
        ],

        'placeholders' => [
            'recipients' => 'Enter email addresses and confirm with Enter…',
        ],

        'actions' => [
            'add_cc' => 'Add CC',
            'add_bcc' => 'Add BCC',
        ],

        'descriptions' => [
            'additional_attachments' => 'Add PDFs or images as extra attachments.',
        ],

        'validation' => [
            'recipients_required' => 'At least one recipient is required.',
            'recipients_global' => 'You must specify at least one recipient (To, CC or BCC).',
        ],
    ],
];
