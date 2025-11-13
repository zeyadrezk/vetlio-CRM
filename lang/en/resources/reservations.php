<?php

return [
    'navigation' => 'Reservations',
    'navigation_waiting_room' => 'Waiting Room',
    'singular' => 'reservation',
    'plural' => 'reservations',

    'fields' => [
        'client' => 'Client',
        'patient' => 'Patient',
        'reservation_time' => 'Reservation Time',
        'waiting_time' => 'Waiting time',
        'service' => 'Service',
        'doctor' => 'Doctor',
        'room' => 'Room',
        'status' => 'Status',
    ],

    'filters' => [
        'from' => 'From',
        'to' => 'To',
        'doctor' => 'Doctor',
        'room' => 'Room',
    ],

    'actions' => [
        'confirmed_arrival' => 'Confirmed arrival',
        'create_medical_record' => 'Create Medical Record',
    ],
];
