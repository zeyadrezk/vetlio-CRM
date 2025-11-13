<?php

return [

    'direction' => 'rtl',

    'actions' => [

        'billing' => [
            'subscription' => [
                'actions' => [
                    'select_plan' => [
                        'label' => 'اختيار خطة',
                    ],
                ],
            ],
        ],

        'delete' => [
            'label' => 'حذف',
        ],

        'edit' => [
            'label' => 'تعديل',
        ],

        'view' => [
            'label' => 'عرض',
        ],

        'create' => [
            'label' => 'إنشاء',
        ],

        'attach' => [
            'label' => 'إرفاق',
        ],

        'detach' => [
            'label' => 'فصل',
        ],

        'associate' => [
            'label' => 'ربط',
        ],

        'dissociate' => [
            'label' => 'فك الارتباط',
        ],

        'add' => [
            'label' => 'إضافة',
            'modal' => [
                'heading' => 'إضافة عنصر جديد',
            ],
        ],

        'cancel' => [
            'label' => 'إلغاء',
        ],

        'close' => [
            'label' => 'إغلاق',
        ],

        'collapse' => [
            'label' => 'طي',
        ],

        'expand' => [
            'label' => 'توسيع',
        ],

        'export' => [
            'label' => 'تصدير',
        ],

        'filter' => [
            'label' => 'تصفية',
        ],

        'group' => [
            'label' => 'تجميع',
        ],

        'import' => [
            'fields' => [
                'file' => [
                    'label' => 'الملف',
                ],
            ],
            'label' => 'استيراد',
        ],

        'open' => [
            'label' => 'فتح',
        ],

        'refresh' => [
            'label' => 'تحديث',
        ],

        'replicate' => [
            'label' => 'تكرار',
        ],

        'restore' => [
            'label' => 'استعادة',
        ],

        'save' => [
            'label' => 'حفظ',
        ],

        'search' => [
            'label' => 'بحث',
            'placeholder' => 'بحث...',
        ],

        'submit' => [
            'label' => 'إرسال',
        ],

    ],

    'components' => [

        'breadcrumbs' => [

            'back' => [
                'label' => 'رجوع',
            ],

        ],

    ],

    'notifications' => [

        'database' => [

            'modal' => [

                'buttons' => [

                    'clear' => [
                        'label' => 'مسح',
                    ],

                    'mark_all_as_read' => [
                        'label' => 'وضع علامة على الكل كمقروء',
                    ],

                ],

                'heading' => 'الإشعارات',

                'empty' => [
                    'description' => 'لا توجد إشعارات',
                    'heading' => 'لا توجد إشعارات',
                ],

            ],

        ],

        'title' => 'الإشعارات',

    ],

    'pagination' => [

        'label' => 'التنقل بين الصفحات',

        'overview' => 'عرض :first إلى :last من أصل :total نتيجة|عرض :first إلى :last من أصل :total نتائج',

        'fields' => [

            'records_per_page' => [

                'label' => 'لكل صفحة',

                'options' => [
                    'all' => 'الكل',
                ],

            ],

        ],

        'actions' => [

            'go_to_page' => [
                'label' => 'الذهاب إلى صفحة :page',
            ],

            'next' => [
                'label' => 'التالي',
            ],

            'previous' => [
                'label' => 'السابق',
            ],

        ],

    ],

    'table' => [

        'actions' => [

            'attach' => [
                'label' => 'إرفاق',
            ],

            'bulk_actions' => [
                'label' => 'الإجراءات الجماعية',
            ],

            'create' => [
                'label' => 'إنشاء',
            ],

            'delete' => [
                'label' => 'حذف',
            ],

            'detach' => [
                'label' => 'فصل',
            ],

            'dissociate' => [
                'label' => 'فك الارتباط',
            ],

            'edit' => [
                'label' => 'تعديل',
            ],

            'export' => [
                'label' => 'تصدير',
            ],

            'filter' => [
                'label' => 'تصفية',
            ],

            'open' => [
                'label' => 'فتح',
            ],

            'replicate' => [
                'label' => 'تكرار',
            ],

            'restore' => [
                'label' => 'استعادة',
            ],

            'toggle_columns' => [
                'label' => 'إظهار/إخفاء الأعمدة',
            ],

            'view' => [
                'label' => 'عرض',
            ],

        ],

        'bulk_actions' => [

            'delete' => [
                'label' => 'حذف المحدد',
            ],

            'restore' => [
                'label' => 'استعادة المحدد',
            ],

        ],

        'columns' => [

            'text' => [
                'more_list_items' => 'و :count أخرى',
            ],

        ],

        'empty' => [
            'heading' => 'لا توجد بيانات',
            'description' => 'لا توجد بيانات لعرضها حالياً.',
        ],

        'filters' => [

            'actions' => [

                'remove' => [
                    'label' => 'إزالة التصفية',
                ],

                'remove_all' => [
                    'label' => 'إزالة جميع التصفيات',
                    'tooltip' => 'إزالة جميع التصفيات',
                ],

                'reset' => [
                    'label' => 'إعادة تعيين',
                ],

            ],

            'heading' => 'التصفيات',

            'indicator' => 'التصفيات النشطة',

            'multi_select' => [
                'placeholder' => 'الكل',
            ],

            'select' => [
                'placeholder' => 'الكل',
            ],

            'trashed' => [

                'label' => 'السجلات المحذوفة',

                'only_trashed' => 'المحذوفة فقط',

                'with_trashed' => 'مع المحذوفة',

                'without_trashed' => 'بدون المحذوفة',

            ],

        ],

        'grouping' => [

            'fields' => [

                'group' => [
                    'label' => 'تجميع حسب',
                    'placeholder' => 'تجميع حسب',
                ],

                'direction' => [

                    'label' => 'اتجاه التجميع',

                    'options' => [
                        'asc' => 'تصاعدي',
                        'desc' => 'تنازلي',
                    ],

                ],

            ],

        ],

        'reorder_indicator' => 'قم بسحب وإفلات السجلات لإعادة ترتيبها.',

        'search' => [

            'label' => 'بحث',

            'placeholder' => 'بحث',

            'indicator' => 'بحث',

        ],

        'selection_indicator' => [

            'selected_count' => 'تم تحديد سجل واحد|تم تحديد :count سجلات',

            'actions' => [

                'select_all' => [
                    'label' => 'تحديد جميع :count',
                ],

                'deselect_all' => [
                    'label' => 'إلغاء تحديد الكل',
                ],

            ],

        ],

        'sorting' => [

            'fields' => [

                'column' => [
                    'label' => 'ترتيب حسب',
                ],

                'direction' => [

                    'label' => 'اتجاه الترتيب',

                    'options' => [
                        'asc' => 'تصاعدي',
                        'desc' => 'تنازلي',
                    ],

                ],

            ],

        ],

    ],

    'widgets' => [

        'account' => [

            'heading' => 'مرحباً، :name',

            'description' => 'مرحباً بك في لوحة التحكم',

        ],

        'filament_info' => [

            'heading' => 'معلومات Filament',

        ],

    ],

    'modals' => [

        'button_group_separator' => 'أو',

        'are_you_sure' => 'هل أنت متأكد؟',

    ],

    'pages' => [

        'dashboard' => [
            'title' => 'لوحة التحكم',
        ],

    ],

    'resources' => [

        'label' => ':label',

        'plural_label' => ':label',

        'navigation_label' => ':label',

        'navigation_group' => 'إدارة',

    ],

];
