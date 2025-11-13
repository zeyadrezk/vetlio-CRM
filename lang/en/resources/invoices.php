<?php

return [
    'navigation' => 'Invoices',
    'singular' => 'invoice',
    'plural' => 'invoices',
    'navigation_group' => 'Finance',

    'fields' => [
        'code' => 'Code',
        'branch' => 'Branch',
        'client' => 'Client',
        'invoice_date' => 'Invoice date',
        'due_date' => 'Due date',
        'payment_method' => 'Payment method',
        'issuer' => 'Issuer',
        'card' => 'Card',
        'bank' => 'Bank',
        'created_by' => 'Created by',
        'base_amount' => 'Base amount',
        'vat_amount' => 'VAT amount',
        'discount' => 'Discount',
        'total' => 'Total',
        'total_discount' => 'Total discount',
        'total_vat' => 'Total VAT',
        'amount_due' => 'Amount due',
        'note_for_client' => 'Note for client',
        'terms_and_conditions' => 'Terms and conditions',
        'invoice_items' => 'Invoice items',
        'cancelled' => 'Cancelled',
    ],

    'items' => [
        'heading' => 'Items',
        'add_item' => 'Add item',
        'item' => 'Item',
        'quantity' => 'Quantity',
        'price' => 'Price',
        'vat' => 'VAT',
        'discount' => 'Discount',
        'total' => 'Total',
        'select_services' => 'Select services',
        'no_items' => 'No items added',
        'no_items_description' => 'There are currently no items, please add some.',
    ],

    'card_types' => [
        'visa' => 'Visa',
        'mastercard' => 'Mastercard',
    ],

    'tooltips' => [
        'fiscalized' => 'Invoice successfully fiscalized',
        'not_fiscalized' => 'Invoice not fiscalized',
    ],

    'filters' => [
        'payment_method' => 'Payment method',
        'created_by' => 'Created by',
        'client' => 'Client',
        'cancelled' => 'Cancelled',
    ],
];
