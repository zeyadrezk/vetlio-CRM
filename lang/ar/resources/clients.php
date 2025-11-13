<?php

return [
    'navigation' => 'العملاء',
    'singular' => 'عميل',
    'plural' => 'عملاء',

    'fields' => [
        'profile_picture' => 'الصورة الشخصية',
        'first_name' => 'الاسم الأول',
        'last_name' => 'اسم العائلة',
        'full_name' => 'الاسم الكامل',
        'gender' => 'الجنس',
        'date_of_birth' => 'تاريخ الميلاد',
        'oib' => 'رقم الهوية',
        'language' => 'اللغة',
        'how_did_you_hear' => 'كيف سمعت عنا؟',
        'email' => 'البريد الإلكتروني',
        'phone' => 'الهاتف',
        'address' => 'العنوان',
        'city' => 'المدينة',
        'postal_code' => 'الرمز البريدي',
        'country' => 'الدولة',
        'tags' => 'الوسوم',
        'total_due' => 'المبلغ المستحق',
        'total_paid' => 'المبلغ المدفوع',
    ],

    'tabs' => [
        'basic_information' => 'المعلومات الأساسية',
        'address' => 'العنوان',
        'contact' => 'معلومات الاتصال',
    ],

    'pages' => [
        'view' => 'عرض العميل',
        'notes' => 'الملاحظات',
        'documents' => 'المستندات',
        'invoices' => 'الفواتير',
    ],

    'stats' => [
        'previous_visit' => 'الزيارة السابقة',
        'no_previous_visits' => 'لا توجد زيارات سابقة',
        'next_visit' => 'الزيارة القادمة',
        'no_upcoming_visits' => 'لا توجد زيارات قادمة',
        'unpaid_amount' => 'المبلغ غير المدفوع',
        'total_balance_due' => 'إجمالي رصيد العميل المستحق',
    ],

    'validation' => [
        'oib_unique' => 'رقم الهوية مستخدم بالفعل.',
        'email_unique' => 'عنوان البريد الإلكتروني مستخدم بالفعل.',
        'phone_unique' => 'رقم الهاتف مستخدم بالفعل.',
    ],

    'alerts' => [
        'inactive_title' => 'عميل',
        'inactive_description' => 'العميل غير نشط، بعض الوظائف ستكون محدودة.',
        'activate' => 'تفعيل',
    ],
];
