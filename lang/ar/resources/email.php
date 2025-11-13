<?php

return [
    'send_email' => [
        'title' => 'إرسال بريد إلكتروني',
        'success' => 'تم إرسال البريد الإلكتروني بنجاح',
        'error' => 'خطأ في إرسال البريد الإلكتروني',

        'fields' => [
            'recipients' => 'المستلمون',
            'cc' => 'نسخة كربونية',
            'bcc' => 'نسخة مخفية',
            'subject' => 'الموضوع',
            'message' => 'الرسالة',
            'attachment' => 'المرفق المراد إرساله',
            'additional_attachments' => 'مرفقات إضافية',
        ],

        'placeholders' => [
            'recipients' => 'أدخل عناوين البريد الإلكتروني واضغط Enter للتأكيد…',
        ],

        'actions' => [
            'add_cc' => 'إضافة نسخة كربونية',
            'add_bcc' => 'إضافة نسخة مخفية',
        ],

        'descriptions' => [
            'additional_attachments' => 'أضف ملفات PDF أو صور كمرفقات إضافية.',
        ],

        'validation' => [
            'recipients_required' => 'مطلوب مستلم واحد على الأقل.',
            'recipients_global' => 'يجب تحديد مستلم واحد على الأقل (إلى، نسخة كربونية، أو نسخة مخفية).',
        ],
    ],
];
