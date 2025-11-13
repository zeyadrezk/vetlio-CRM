<?php

return [
    'columns' => [
        'created_at' => 'تاريخ الإنشاء',
        'updated_at' => 'تاريخ التحديث',
    ],

    'items_to_select' => [
        'columns' => [
            'code' => 'الرمز',
            'name' => 'الاسم',
            'group' => 'المجموعة',
            'price' => 'السعر',
            'vat' => 'ضريبة القيمة المضافة',
            'price_with_vat' => 'السعر شامل الضريبة',
        ],

        'filters' => [
            'group' => 'المجموعة',
        ],
    ],
];
