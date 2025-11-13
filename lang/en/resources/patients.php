<?php

return [
    'navigation' => 'Patients',
    'singular' => 'patient',
    'plural' => 'patients',

    'fields' => [
        'photo' => 'Photo',
        'name' => 'Name',
        'owner' => 'Owner',
        'gender' => 'Gender',
        'date_of_birth' => 'Date of Birth',
        'color' => 'Color',
        'species' => 'Species',
        'breed' => 'Breed',
        'is_dangerous' => 'Is the patient dangerous?',
        'dangerous_note' => 'Dangerous (Note)',
        'remarks' => 'Remarks',
        'allergies' => 'Allergies',
        'age' => 'Age',
    ],

    'placeholders' => [
        'remarks' => 'Enter remarks or notes about the patient...',
        'allergies' => 'Enter any allergies...',
    ],

    'options' => [
        'dangerous_yes' => 'Yes',
        'dangerous_no' => 'No',
    ],
];
