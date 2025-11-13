<?php

return [
    'navigation' => 'Clients',
    'singular' => 'client',
    'plural' => 'clients',

    'fields' => [
        'profile_picture' => 'Profile picture',
        'first_name' => 'First name',
        'last_name' => 'Last name',
        'full_name' => 'Full name',
        'gender' => 'Gender',
        'date_of_birth' => 'Date of birth',
        'oib' => 'OIB',
        'language' => 'Language',
        'how_did_you_hear' => 'How did you hear about us?',
        'email' => 'Email',
        'phone' => 'Phone',
        'address' => 'Address',
        'city' => 'City',
        'postal_code' => 'Postal code',
        'country' => 'Country',
        'tags' => 'Tags',
        'total_due' => 'Total due',
        'total_paid' => 'Total paid',
    ],

    'tabs' => [
        'basic_information' => 'Basic information',
        'address' => 'Address',
        'contact' => 'Contact',
    ],

    'pages' => [
        'view' => 'View client',
        'notes' => 'Notes',
        'documents' => 'Documents',
        'invoices' => 'Invoices',
    ],

    'stats' => [
        'previous_visit' => 'Previous visit',
        'no_previous_visits' => 'No previous visits',
        'next_visit' => 'Next visit',
        'no_upcoming_visits' => 'No upcoming visits',
        'unpaid_amount' => 'Unpaid amount',
        'total_balance_due' => 'Total client balance due',
    ],

    'validation' => [
        'oib_unique' => 'OIB is already in use.',
        'email_unique' => 'Email address is already in use.',
        'phone_unique' => 'Phone number is already in use.',
    ],

    'alerts' => [
        'inactive_title' => 'Client',
        'inactive_description' => 'Client is not active, some functionalities will be limited.',
        'activate' => 'Activate',
    ],
];
