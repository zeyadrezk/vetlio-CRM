<?php

return [
    'navigation' => 'الفواتير',
    'singular' => 'فاتورة',
    'plural' => 'فواتير',
    'navigation_group' => 'المالية',

    'fields' => [
        'code' => 'الرمز',
        'branch' => 'الفرع',
        'client' => 'العميل',
        'invoice_date' => 'تاريخ الفاتورة',
        'due_date' => 'تاريخ الاستحقاق',
        'payment_method' => 'طريقة الدفع',
        'issuer' => 'المُصدر',
        'card' => 'البطاقة',
        'bank' => 'البنك',
        'created_by' => 'تم الإنشاء بواسطة',
        'base_amount' => 'المبلغ الأساسي',
        'vat_amount' => 'قيمة الضريبة',
        'discount' => 'الخصم',
        'total' => 'الإجمالي',
        'total_discount' => 'إجمالي الخصم',
        'total_vat' => 'إجمالي الضريبة',
        'amount_due' => 'المبلغ المستحق',
        'note_for_client' => 'ملاحظة للعميل',
        'terms_and_conditions' => 'الشروط والأحكام',
        'invoice_items' => 'عناصر الفاتورة',
        'cancelled' => 'ملغية',
    ],

    'items' => [
        'heading' => 'العناصر',
        'add_item' => 'إضافة عنصر',
        'item' => 'العنصر',
        'quantity' => 'الكمية',
        'price' => 'السعر',
        'vat' => 'الضريبة',
        'discount' => 'الخصم',
        'total' => 'الإجمالي',
        'select_services' => 'اختيار الخدمات',
        'no_items' => 'لم يتم إضافة عناصر',
        'no_items_description' => 'لا توجد عناصر حالياً، يرجى إضافة بعضها.',
    ],

    'card_types' => [
        'visa' => 'فيزا',
        'mastercard' => 'ماستركارد',
    ],

    'tooltips' => [
        'fiscalized' => 'تم إصدار الفاتورة بنجاح',
        'not_fiscalized' => 'الفاتورة غير مُصدرة',
    ],

    'filters' => [
        'payment_method' => 'طريقة الدفع',
        'created_by' => 'تم الإنشاء بواسطة',
        'client' => 'العميل',
        'cancelled' => 'ملغية',
    ],
];
