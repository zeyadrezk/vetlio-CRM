# Hidden Assumptions & Edge Cases

**Vetlio → Coodely Hospital Transformation**
**Version:** 1.0
**Date:** 2025-11-16

---

## Overview

This document catalogs **hidden assumptions** in the Vetlio pet clinic codebase that are not immediately obvious but will cause issues during transformation to a human hospital system. Each assumption is documented with:

- Location in code
- Current behavior
- Why it's problematic for human healthcare
- Migration strategy

---

## 1. Age Calculation and Display

### Location
`app/Models/Patient.php:50`

### Current Implementation
```php
$age = $this->date_of_birth ? $this->date_of_birth->age . ' god.' : null;
```

### Assumption
- Uses Carbon's `.age` property which returns years
- Displays as "X god." (Croatian for "years old")
- Same logic for all animals regardless of age

### Problem for Human Healthcare

**Veterinary Context:**
- Puppies/kittens: Often shown in months (e.g., "6 months old")
- Adult pets: Years are sufficient
- Senior pets: Years are sufficient

**Human Context:**
- **Neonates:** Days/weeks old (0-28 days)
- **Infants:** Months old (0-12 months)
- **Toddlers:** Months old (12-36 months)
- **Children:** Years old (3-17 years)
- **Adults:** Years old (18-64 years)
- **Elderly:** Years old (65+ years)
- **Privacy:** Sometimes DOB should be shown, not age (HIPAA)

### Migration Strategy

**Replace with context-aware formatting:**

```php
public function ageDisplay(): Attribute
{
    return Attribute::make(function () {
        if (!$this->date_of_birth) {
            return null;
        }

        $diff = $this->date_of_birth->diff(now());

        // Neonate (0-28 days)
        if ($diff->y == 0 && $diff->m == 0 && $diff->d <= 28) {
            return $diff->d . ' days old';
        }

        // Infant/Toddler (0-36 months)
        if ($diff->y < 3) {
            $months = ($diff->y * 12) + $diff->m;
            return $months . ' months old';
        }

        // Everyone else
        return $diff->y . ' years old';
    });
}

// For privacy-sensitive contexts:
public function ageOrDOB(bool $showDOB = false): string
{
    if ($showDOB) {
        return $this->date_of_birth->format('m/d/Y');
    }
    return $this->age_display;
}
```

**Impact:** Patient model, all display forms, PDFs, search results

---

## 2. Dangerous Flag - Behavioral Risk

### Location
`app/Models/Patient.php:51`
`app/Filament/App/Resources/Patients/Schemas/PatientForm.php:91-106`

### Current Implementation
```php
// Model
$dangerous = $this->dangerous ? 'opasan' : null;

// Form
ToggleButtons::make('dangerous')
    ->label('Is the patient dangerous?')
    ->boolean('Yes', 'No')
```

### Assumption
- Binary yes/no flag
- Means "aggressive animal" or "bites"
- Simple warning for staff handling

### Problem for Human Healthcare

**Veterinary Context:**
- Dangerous = Might bite, scratch, or attack
- Staff safety concern
- Handling precautions needed

**Human Context - Much More Complex:**
- **Fall Risk:** Patient mobility issues
- **Elopement Risk:** Dementia, confusion
- **Self-Harm Risk:** Psychiatric concern
- **Violence Risk:** Behavioral health
- **Allergy Alerts:** Critical medical info
- **Isolation Precautions:** Infectious disease
- **DNR/AND Status:** Do Not Resuscitate
- **Legal Status:** Involuntary hold, custody

### Migration Strategy

**Replace with multi-dimensional risk assessment:**

```php
// Migration: Add columns
Schema::table('patients', function (Blueprint $table) {
    // Remove boolean dangerous
    $table->dropColumn('dangerous');

    // Add structured risk flags
    $table->json('risk_flags')->nullable();
    $table->text('risk_notes')->nullable();
    $table->boolean('fall_risk')->default(false);
    $table->boolean('elopement_risk')->default(false);
    $table->boolean('behavioral_risk')->default(false);
    $table->string('isolation_precautions')->nullable();
    $table->boolean('dnr_status')->default(false);
});

// Form
Fieldset::make('Patient Risk Assessment')
    ->schema([
        CheckboxList::make('risk_flags')
            ->options([
                'fall_risk' => 'Fall Risk',
                'elopement_risk' => 'Elopement/Wandering Risk',
                'behavioral_risk' => 'Behavioral/Violence Risk',
                'self_harm_risk' => 'Self-Harm Risk',
                'seizure_risk' => 'Seizure Risk',
                'aspiration_risk' => 'Aspiration Risk',
            ])
            ->columns(2),

        Select::make('isolation_precautions')
            ->options([
                'none' => 'None',
                'contact' => 'Contact Precautions',
                'droplet' => 'Droplet Precautions',
                'airborne' => 'Airborne Precautions',
                'protective' => 'Protective Isolation',
            ]),

        Textarea::make('risk_notes')
            ->label('Risk Assessment Notes')
            ->helperText('Specific precautions or details'),

        Toggle::make('dnr_status')
            ->label('DNR/AND Status')
            ->helperText('Do Not Resuscitate / Allow Natural Death'),
    ]),
```

**Impact:** Patient model, forms, display, PDF reports, alerts

---

## 3. Client-Patient Relationship Navigation

### Location
`app/Filament/App/Resources/Reservations/Schemas/ReservationForm.php:39`
`app/Filament/Portal/Pages/Dashboard.php`

### Current Implementation
```php
// When client changes, reset patient selection
Select::make('client_id')
    ->live()
    ->afterStateUpdated(fn($state, $get, $set) => $set('patient_id', null))

// Portal shows all client's patients
$patients = auth()->user()->patients;
```

### Assumption
- One client (owner) has multiple patients (pets)
- Client selects which pet the appointment is for
- Portal shows grid of all client's pets

### Problem for Human Healthcare

**Veterinary Context:**
- John Smith owns: "Max" (Dog), "Whiskers" (Cat), "Tweety" (Bird)
- John logs into portal, sees all three pets
- Clicks appointment for Max

**Human Context:**
- **Option A (1:1):** John Smith IS the patient
  - No selection needed
  - Always scheduling for self
  - This logic breaks completely

- **Option B (Guardian):** Mary Smith (mother) manages children
  - "Johnny" (Child), "Emma" (Child)
  - Still works, but needs different UX
  - Privacy concerns (can parent see all records?)

- **Option C (Hybrid):** Adult patients self-manage
  - John Smith manages himself
  - Mary Smith manages her children
  - Complex navigation

### Migration Strategy

**For Strategy B (Guardian Model) - Recommended:**

```php
// Reservation form - keep logic but update labels
Select::make('guardian_id')
    ->label('Patient or Guardian')
    ->live()
    ->afterStateUpdated(fn($state, $get, $set) => $set('patient_id', null))

Select::make('patient_id')
    ->label('Patient')
    ->disabled(fn($get) => !$get('guardian_id'))
    ->options(function (Get $get) {
        if (!$get('guardian_id')) {
            return [];
        }

        $guardian = Guardian::find($get('guardian_id'));
        $patients = $guardian->patients()->get();

        // If guardian has only one patient, auto-select
        if ($patients->count() === 1) {
            return [$patients->first()->id => $patients->first()->name];
        }

        return $patients->pluck('name', 'id');
    })

// Portal - add privacy controls
public function getPatients()
{
    return auth()->guard('guardian')
        ->user()
        ->patients()
        ->with(['latestVitals', 'upcomingAppointments'])
        ->get()
        ->map(function ($patient) {
            // Add privacy badge if patient is adult
            $patient->is_adult = $patient->date_of_birth->age >= 18;
            $patient->requires_consent = $patient->is_adult;
            return $patient;
        });
}
```

**For Strategy A (1:1 Model):**
```php
// Remove patient selection entirely
// Use authenticated user as patient
$reservation->patient_id = auth()->id();
$reservation->guardian_id = null; // No guardian concept
```

**Impact:** Reservation forms, portal dashboard, appointment booking, all patient-client navigation

---

## 4. Medical Document Content Structure

### Location
`app/Models/MedicalDocument.php`
`database/migrations/*_create_medical_documents_table.php`

### Current Implementation
```php
Schema::create('medical_documents', function (Blueprint $table) {
    // ...
    $table->text('content');  // Free-text medical findings
    $table->text('reason_for_coming');
    // ...
});
```

### Assumption
- Medical findings are free-text
- Veterinary SOAP notes (Subjective, Objective, Assessment, Plan)
- Simple, unstructured documentation

### Problem for Human Healthcare

**Veterinary Context:**
- Simple text: "Dog presented with limping. Examination shows swelling on left front paw. Prescribed anti-inflammatory. Follow up in 2 weeks."
- No coding required
- Insurance rarely involved

**Human Context:**
- **Must Include:**
  - Chief Complaint (structured)
  - History of Present Illness (HPI)
  - Review of Systems (ROS)
  - Physical Examination (structured by body system)
  - Vital Signs (separate table needed - we added this)
  - Assessment (with ICD-10 codes)
  - Plan (with CPT codes)
  - Prescriptions (separate table needed - we added this)
  - Lab Orders (separate table needed - we added this)

- **Billing Requirements:**
  - Must support ICD-10 diagnosis codes
  - Must support CPT procedure codes
  - Must track E&M level (99201-99215)
  - Required for insurance claims

- **Legal Requirements:**
  - Must be immutable (or track all changes)
  - Must support addendums (not edits)
  - Must track who/when for liability

### Migration Strategy

**Already Planned:**
- Added `vital_signs` table
- Added `diagnoses` table
- Added `prescriptions` table
- Added `lab_orders` table

**Additional Changes Needed:**

```php
// Migration: Add structured fields to medical_documents
Schema::table('medical_documents', function (Blueprint $table) {
    // Keep content for narrative
    // Add structured fields
    $table->text('chief_complaint')->nullable();
    $table->text('history_present_illness')->nullable();
    $table->json('review_of_systems')->nullable();
    $table->text('physical_examination')->nullable();
    $table->json('assessment')->nullable();  // Will link to diagnoses table
    $table->text('plan')->nullable();

    // Billing
    $table->string('em_level')->nullable();  // 99201-99215
    $table->decimal('total_charges', 10, 2)->nullable();

    // Legal
    $table->boolean('is_locked')->default(false);
    $table->timestamp('locked_at')->nullable();
    $table->unsignedBigInteger('locked_by')->nullable();
    $table->boolean('is_final')->default(false);
    $table->timestamp('finalized_at')->nullable();
});

// Create addendums table
Schema::create('medical_document_addendums', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('medical_document_id');
    $table->text('content');
    $table->unsignedBigInteger('added_by');
    $table->timestamp('added_at');
    $table->string('reason');
    $table->timestamps();
});
```

**Impact:** Medical documentation workflow, billing, legal compliance, insurance claims

---

## 5. Service Provider (Veterinarian) Availability

### Location
`app/Filament/App/Resources/Reservations/Schemas/ReservationForm.php:250-277`
`app/Models/User.php` (HasSchedules trait)

### Current Implementation
```php
public static function checkAvailability($get, $set)
{
    // ...
    if ($doctor && !$doctor->isAvailableAt($date, $start, $end)) {
        $conflicts[] = 'The veterinarian is not available during the selected time.';
    }
    // ...
}
```

### Assumption
- Veterinarians work regular business hours
- Simple schedule: Monday-Friday 9am-5pm
- No shift rotations
- No on-call
- No 24/7 coverage

### Problem for Human Healthcare

**Veterinary Context:**
- Clinic hours: 9am-6pm weekdays
- Maybe Saturday half-day
- Emergency? Go to animal ER (different facility)
- Simple schedule model

**Human Context:**
- **Primary Care:** Regular hours (similar to vet)
- **Urgent Care:** Extended hours (7am-10pm)
- **Emergency Department:** 24/7/365
- **Hospital Inpatient:** 24/7/365
- **Shift Rotations:**
  - Day shift (7am-7pm)
  - Night shift (7pm-7am)
  - Swing shift
- **On-Call Schedules:**
  - Physician on-call at home
  - Must respond within X minutes
  - Backup on-call
- **Cross-Coverage:**
  - Multiple doctors cover same patient
  - Handoffs between shifts
  - Consults to specialists

### Migration Strategy

**Current Schedule System (Laravel Zap package) supports:**
- Working hours per day
- Per-user schedules
- Per-room schedules

**Needs Enhancement:**

```php
// Add to User model
Schema::table('users', function (Blueprint $table) {
    $table->json('on_call_schedule')->nullable();
    $table->boolean('accepts_walkins')->default(true);
    $table->integer('max_patients_per_day')->nullable();
    $table->string('shift_type')->nullable();  // day, night, swing
});

// Update availability check
public static function checkAvailability($get, $set)
{
    $start = $get('from');
    $end = $get('to');
    $userId = $get('attending_physician_id');
    $roomId = $get('room_id');
    $appointmentType = $get('appointment_type');  // scheduled, walk-in, emergency

    $conflicts = [];

    if ($start && $end) {
        $date = Carbon::parse($start)->format('Y-m-d');
        $startTime = Carbon::parse($start)->format('H:i');
        $endTime = Carbon::parse($end)->format('H:i');

        $doctor = User::find($userId);

        if ($doctor) {
            // Check regular schedule
            if (!$doctor->isAvailableAt($date, $startTime, $endTime)) {
                // Check if on-call
                if (!$doctor->isOnCallAt($date, $startTime)) {
                    $conflicts[] = 'The doctor is not available during the selected time.';
                } else {
                    $conflicts[] = 'The doctor is on-call (not in office) during this time. Confirm before scheduling.';
                }
            }

            // Check max patients per day
            if ($doctor->hasReachedDailyLimit($date)) {
                $conflicts[] = 'The doctor has reached maximum patient capacity for this day.';
            }

            // Emergency appointments can override
            if ($appointmentType === 'emergency') {
                $conflicts = []; // Clear conflicts for emergency
                $set('override_schedule', true);
            }
        }

        // Room availability (hospital may have multiple concurrent uses)
        $room = Room::find($roomId);
        if ($room && !$room->isAvailableAt($date, $startTime, $endTime)) {
            $conflicts[] = 'The room is occupied during the selected time.';
        }
    }

    $set('availability_conflicts', implode(PHP_EOL, $conflicts));
}
```

**Impact:** Scheduling system, appointment booking, staff management, 24/7 operations

---

## 6. Invoice Fiscalization (Croatian Tax System)

### Location
`app/Models/Invoice.php`
`app/Services/FiscalisationService.php`
`composer.json` - `nticaric/fiskalizacija` package

### Current Implementation
```php
// Invoice model fields
'zki' => 'string',  // Zaštitni kod izdavatelja (Protective code)
'jir' => 'string',  // Jedinstveni identifikator računa (Unique invoice ID)
'qrcode' => 'string',  // QR code for verification
'fiscalization_at' => 'datetime',

// Service generates codes and submits to Croatian tax authority
```

### Assumption
- Operating in Croatia
- Must comply with Croatian fiscalization law
- All invoices sent to tax authority in real-time
- Specific XML format
- Certificate-based authentication

### Problem for Human Healthcare

**If Deploying Outside Croatia:**
- Fiscalization system is useless
- Package dependency is unnecessary
- Can be removed

**If Keeping in Croatia:**
- Human healthcare billing more complex
- Need to integrate:
  - HZZO (Croatian Health Insurance Fund)
  - Private insurance companies
  - Pre-authorization systems
  - Claim submission
  - Reimbursement tracking

**If Deploying to US:**
- Need completely different billing system:
  - EDI 837 claims (electronic claims)
  - ICD-10 diagnosis codes
  - CPT procedure codes
  - HCPCS supply codes
  - Insurance eligibility verification
  - Pre-authorization
  - EOB (Explanation of Benefits) processing
  - Payment posting
  - Denial management
  - Medicare/Medicaid compliance

### Migration Strategy

**Decision Point:** What country/region will this deploy to?

**Option 1: Remove Croatian Fiscalization (for non-Croatia)**
```php
// Migration
Schema::table('invoices', function (Blueprint $table) {
    $table->dropColumn(['zki', 'jir', 'qrcode', 'fiscalization_at']);
});

// Remove from composer.json
"nticaric/fiskalizacija": "^2.1",  // DELETE

// Remove service
app/Services/FiscalisationService.php  // DELETE
```

**Option 2: Keep Croatian, Add Insurance**
```php
// Keep fiscalization
// Add insurance claim tracking
Schema::create('insurance_claims', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('invoice_id');
    $table->unsignedBigInteger('insurance_plan_id');
    $table->string('claim_number')->unique();
    $table->string('status');  // submitted, pending, approved, denied, partial
    $table->decimal('amount_billed', 10, 2);
    $table->decimal('amount_approved', 10, 2)->nullable();
    $table->decimal('patient_responsibility', 10, 2)->nullable();
    $table->date('submitted_at')->nullable();
    $table->date('processed_at')->nullable();
    $table->text('denial_reason')->nullable();
    $table->timestamps();
});
```

**Impact:** Billing module, invoicing, financial reporting, compliance

---

## 7. Portal Authentication & Privacy

### Location
`app/Models/Client.php` (Authenticatable)
`app/Providers/Filament/PortalPanelProvider.php`
`config/auth.php` - 'client' guard

### Current Implementation
```php
// Client model extends Authenticatable
class Client extends Authenticatable
{
    // Simple email/password authentication
    protected $hidden = ['password', 'remember_token'];
}

// Portal panel uses 'client' guard
'authGuard' => 'client',
```

### Assumption
- Simple username/password login
- Client sees all their pets' records
- No additional security needed
- No identity verification
- No audit logging

### Problem for Human Healthcare

**Veterinary Context:**
- Low security risk
- Pet medical records not legally protected
- Owner has full rights to pet's info

**Human Context - MUCH Higher Security:**

**HIPAA Requirements (US):**
- **Authentication:**
  - Multi-factor authentication recommended
  - Strong password requirements
  - Account lockout after failed attempts
  - Session timeouts

- **Authorization:**
  - Role-based access control
  - Minimum necessary standard
  - Break-glass access (emergency override)

- **Audit Logging:**
  - Who accessed what, when
  - All create, read, update, delete operations
  - Failed login attempts
  - Export for compliance review

- **Privacy:**
  - Patient must consent to guardian access
  - Adult patients (18+) control their own records
  - Minors' records transfer to them at age 18
  - Special protections for:
    - Reproductive health
    - Mental health
    - Substance abuse treatment
    - HIV/AIDS status

**GDPR Requirements (EU):**
- Right to access
- Right to erasure
- Right to data portability
- Consent management
- Data breach notification

### Migration Strategy

**Phase 1: Enhanced Authentication**

```php
// Add 2FA to guardians table
Schema::table('guardians', function (Blueprint $table) {
    $table->string('two_factor_secret')->nullable();
    $table->text('two_factor_recovery_codes')->nullable();
    $table->timestamp('two_factor_confirmed_at')->nullable();
    $table->integer('failed_login_attempts')->default(0);
    $table->timestamp('locked_until')->nullable();
});

// Implement in PortalPanelProvider
public function panel(Panel $panel): Panel
{
    return $panel
        ->authGuard('guardian')
        ->login()
        ->passwordReset()
        ->emailVerification()
        ->twoFactorAuthentication()  // Enable 2FA
        ->maxContentWidth('full')
        ->plugin(new \Jeffgreco13\FilamentBreezy\BreezyCore([
            'enable_two_factor_authentication' => true,
        ]));
}
```

**Phase 2: Audit Logging**

```php
// Create audit log table
Schema::create('audit_logs', function (Blueprint $table) {
    $table->id();
    $table->string('event_type');  // login, view, create, update, delete
    $table->string('auditable_type');  // Patient, MedicalDocument, etc.
    $table->unsignedBigInteger('auditable_id')->nullable();
    $table->unsignedBigInteger('user_id')->nullable();
    $table->string('user_type')->nullable();  // User, Guardian
    $table->ipAddress('ip_address');
    $table->text('user_agent')->nullable();
    $table->json('old_values')->nullable();
    $table->json('new_values')->nullable();
    $table->timestamps();
});

// Add observer to all sensitive models
class PatientObserver
{
    public function retrieved(Patient $patient)
    {
        AuditLog::create([
            'event_type' => 'view',
            'auditable_type' => Patient::class,
            'auditable_id' => $patient->id,
            'user_id' => auth()->id(),
            'user_type' => auth()->getDefaultDriver(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    // Similar for created, updated, deleted
}
```

**Phase 3: Privacy Controls**

```php
// Add consent table
Schema::create('record_access_consents', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('patient_id');
    $table->unsignedBigInteger('guardian_id')->nullable();
    $table->unsignedBigInteger('user_id')->nullable();
    $table->string('access_type');  // full, limited, emergency_only
    $table->json('restrictions')->nullable();  // What they CAN'T see
    $table->date('valid_from');
    $table->date('valid_until')->nullable();
    $table->boolean('is_active')->default(true);
    $table->text('notes')->nullable();
    $table->timestamps();
});

// Update Patient model
public function canBeViewedBy(Guardian|User $accessor): bool
{
    // Adult patients control their own access
    if ($this->date_of_birth->age >= 18) {
        $consent = $this->recordAccessConsents()
            ->where('guardian_id', $accessor->id)
            ->where('is_active', true)
            ->where('valid_from', '<=', now())
            ->where(function ($q) {
                $q->whereNull('valid_until')
                  ->orWhere('valid_until', '>=', now());
            })
            ->first();

        return $consent !== null;
    }

    // Minor patients - guardian has access
    return $this->guardian_id === $accessor->id;
}
```

**Impact:** Portal access, privacy, compliance, legal liability

---

## 8. Calendar Privacy & HIPAA

### Location
`app/Filament/App/Widgets/CalendarWidget.php`
`app/Models/Reservation.php:toCalendarEvent()`
`resources/views/calendar/event.blade.php`

### Current Implementation
```php
public function toCalendarEvent(): array
{
    return [
        'id' => $this->id,
        'title' => $this->client->full_name,  // Shows patient owner name
        'start' => $this->from,
        'end' => $this->to,
        'resourceId' => $this->service_provider_id,
        // ...
    ];
}
```

### Assumption
- Calendar visible to all staff
- Showing client name is fine
- All staff should see all appointments

### Problem for Human Healthcare

**Veterinary Context:**
- "John Smith - Fluffy (Cat) - Vaccination"
- All staff can see
- No privacy concerns

**Human Context - Privacy Violation:**
- Showing "John Smith" on shared calendar = **HIPAA violation**
- Not all staff should see all patients
- Need role-based visibility

**HIPAA Minimum Necessary Rule:**
- Staff should only see information needed for their job
- Front desk: Can see names (for check-in)
- Doctors: Can see their own patients
- Billing: Can see all patients
- IT: Should see anonymized data only

### Migration Strategy

```php
// Add privacy settings to Organisation
Schema::table('organisations', function (Blueprint $table) {
    $table->boolean('calendar_show_patient_names')->default(false);
    $table->boolean('calendar_show_diagnosis')->default(false);
    $table->json('calendar_privacy_settings')->nullable();
});

// Update Reservation model
public function toCalendarEvent(User $viewer): array
{
    $organisation = $this->organisation;

    // Determine what to show based on role and settings
    $canSeeName = $this->canUserSeePatientName($viewer);
    $canSeeDiagnosis = $this->canUserSeeDiagnosis($viewer);

    return [
        'id' => $this->id,
        'title' => $canSeeName
            ? $this->patient->name
            : 'Patient - ' . $this->id,
        'description' => $canSeeDiagnosis
            ? $this->reason_for_coming
            : 'Appointment',
        'start' => $this->from,
        'end' => $this->to,
        'resourceId' => $this->attending_physician_id,
        'backgroundColor' => $this->service->color ?? '#3B82F6',
        'extendedProps' => [
            'patient_initials' => $canSeeName ? null : $this->patient->initials,
            'appointment_type' => $this->appointment_type,
            'status' => $this->status_id,
        ],
    ];
}

protected function canUserSeePatientName(User $user): bool
{
    // Doctor can see their own patients
    if ($this->attending_physician_id === $user->id) {
        return true;
    }

    // Front desk can see names
    if ($user->hasRole('front_desk')) {
        return true;
    }

    // Nurses in same department
    if ($user->hasRole('nurse') && $user->department_id === $this->department_id) {
        return true;
    }

    // Check organisation settings
    if ($this->organisation->calendar_show_patient_names) {
        return true;
    }

    return false;
}

// Update calendar view
class CalendarWidget extends \Guava\Calendar\Widgets\CalendarWidget
{
    public function getEvents(array $fetchInfo = []): array
    {
        $user = auth()->user();

        return Reservation::query()
            ->whereBetween('from', [$fetchInfo['start'], $fetchInfo['end']])
            ->when(!$user->hasRole('admin'), function ($query) use ($user) {
                // Non-admins only see relevant appointments
                $query->where(function ($q) use ($user) {
                    $q->where('attending_physician_id', $user->id)
                      ->orWhere('branch_id', $user->primary_branch_id);
                });
            })
            ->get()
            ->map(fn($reservation) => $reservation->toCalendarEvent($user))
            ->toArray();
    }
}
```

**Impact:** Calendar display, scheduling, staff workflows, HIPAA compliance

---

## 9. Medical Document Locking vs. Immutability

### Location
`app/Models/MedicalDocument.php`
Fields: `locked_at`, `locked_user_id`

### Current Implementation
```php
// Document can be locked to prevent editing
if ($document->locked_at) {
    throw new Exception('Document is locked');
}

// Lock mechanism prevents concurrent edits
$document->update([
    'locked_at' => now(),
    'locked_user_id' => auth()->id(),
]);
```

### Assumption
- Documents can be edited
- Locking prevents concurrent edits
- Once locked, it's final

### Problem for Human Healthcare

**Veterinary Context:**
- Doctor writes notes
- Can edit/fix typos
- Lock when done
- Simple workflow

**Human Context - Legal Requirements:**

**Medical records are LEGAL DOCUMENTS:**
- **Cannot be altered** after finalization
- Errors must be corrected via addendums
- All changes must be tracked and attributed
- Original must be preserved
- Required for:
  - Malpractice defense
  - Insurance claims
  - Legal proceedings
  - Regulatory compliance

**21 CFR Part 11 (FDA Electronic Records):**
- Audit trail required
- Cannot delete or modify
- Must use electronic signatures
- Must track all changes

**State Medical Board Requirements:**
- Medical records must be maintained for X years
- Cannot be destroyed
- Must be tamper-proof

### Migration Strategy

**Implement Append-Only Medical Records:**

```php
// Migration: Make documents immutable
Schema::table('medical_documents', function (Blueprint $table) {
    // Keep locking for work-in-progress
    // Add finalization
    $table->boolean('is_finalized')->default(false);
    $table->timestamp('finalized_at')->nullable();
    $table->unsignedBigInteger('finalized_by')->nullable();
    $table->string('electronic_signature')->nullable();

    // Version tracking
    $table->integer('version')->default(1);
});

// Create addendums table (already mentioned earlier)
Schema::create('medical_document_addendums', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('medical_document_id');
    $table->text('content');
    $table->string('addendum_type');  // correction, late_entry, additional_info
    $table->unsignedBigInteger('created_by');
    $table->timestamp('created_at');
    $table->string('electronic_signature');
    $table->text('reason_for_addendum');
});

// Model behavior
class MedicalDocument extends Model
{
    public function finalize(string $signature): void
    {
        if ($this->is_finalized) {
            throw new \Exception('Document is already finalized and cannot be changed.');
        }

        DB::transaction(function () use ($signature) {
            $this->update([
                'is_finalized' => true,
                'finalized_at' => now(),
                'finalized_by' => auth()->id(),
                'electronic_signature' => $signature,
                'locked_at' => now(),
                'locked_user_id' => auth()->id(),
            ]);

            // Create audit log
            AuditLog::create([
                'event_type' => 'finalized',
                'auditable_type' => self::class,
                'auditable_id' => $this->id,
                'user_id' => auth()->id(),
                'ip_address' => request()->ip(),
            ]);
        });
    }

    public function addAddendum(string $content, string $type, string $reason): MedicalDocumentAddendum
    {
        if (!$this->is_finalized) {
            throw new \Exception('Document must be finalized before adding addendums. Please edit the document directly.');
        }

        return $this->addendums()->create([
            'content' => $content,
            'addendum_type' => $type,
            'created_by' => auth()->id(),
            'reason_for_addendum' => $reason,
            'electronic_signature' => $this->generateSignature($content),
        ]);
    }

    // Prevent updates after finalization
    protected static function booted()
    {
        static::updating(function ($document) {
            if ($document->is_finalized && $document->isDirty(['content', 'reason_for_coming', 'diagnosis'])) {
                throw new \Exception('Finalized documents cannot be edited. Please add an addendum instead.');
            }
        });
    }
}
```

**Impact:** Medical documentation, legal compliance, workflow, data integrity

---

## 10. Service Catalog - Veterinary vs. Human Procedures

### Location
`app/Models/Service.php`
`database/seeders/DemoOrganisationSeeder.php` (services seeding)

### Current Implementation
```php
// Example veterinary services seeded:
- Vaccination
- Checkup / Wellness Exam
- Dental Cleaning
- Surgery
- Grooming
- Neutering/Spaying
- Microchipping
- Nail Trimming
```

### Assumption
- Pet-specific services
- Simple service catalog
- No coding standards
- No insurance requirements

### Problem for Human Healthcare

**Veterinary Services** vs. **Human Medical Procedures:**

| Veterinary | Human Equivalent | CPT Code | Notes |
|------------|------------------|----------|-------|
| Vaccination | Immunization | 90000 series | Different vaccines |
| Checkup | Office Visit - New Patient | 99201-99205 | E&M codes |
| Checkup | Office Visit - Established | 99211-99215 | Different levels |
| Dental Cleaning | ❌ None (refer to dentist) | - | Different specialty |
| Surgery | Surgical Procedure | 10000-69999 | Thousands of codes |
| Grooming | ❌ None | - | Not medical |
| Neutering/Spaying | ❌ None | - | Not applicable |
| Microchipping | ❌ None | - | Not applicable |
| Nail Trimming | ❌ None | - | Not medical |

**New Services Needed:**
- Primary care visits
- Specialist consultations
- Diagnostic tests (X-ray, MRI, CT, Ultrasound)
- Laboratory tests
- Physical therapy
- Infusions
- Injections
- Minor procedures
- Major procedures
- Emergency services

### Migration Strategy

**Step 1: Add CPT/HCPCS Coding**

```php
// Migration
Schema::table('services', function (Blueprint $table) {
    $table->string('cpt_code', 10)->nullable();
    $table->string('hcpcs_code', 10)->nullable();
    $table->text('description')->nullable();
    $table->string('category')->nullable();  // E&M, Surgery, Diagnostic, etc.
    $table->integer('rvu')->nullable();  // Relative Value Unit
    $table->boolean('requires_preauth')->default(false);
});
```

**Step 2: Create Service Categories**

```php
Schema::create('service_categories', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('cpt_range')->nullable();  // e.g., "99201-99215"
    $table->text('description')->nullable();
    $table->integer('sort_order')->default(0);
    $table->timestamps();
});

// Seed with standard categories
[
    'Evaluation & Management' => '99201-99499',
    'Anesthesia' => '00100-01999',
    'Surgery' => '10000-69999',
    'Radiology' => '70000-79999',
    'Pathology & Laboratory' => '80000-89999',
    'Medicine' => '90000-99199',
]
```

**Step 3: Seed Common Services**

```php
// New seeder: MedicalServicesSeeder.php
class MedicalServicesSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            // E&M - Office Visits
            [
                'name' => 'Office Visit - New Patient, Level 3',
                'cpt_code' => '99203',
                'duration' => '30 minutes',
                'category' => 'Evaluation & Management',
                'description' => 'New patient office visit, moderate complexity',
            ],
            [
                'name' => 'Office Visit - Established Patient, Level 3',
                'cpt_code' => '99213',
                'duration' => '15 minutes',
                'category' => 'Evaluation & Management',
            ],

            // Preventive Care
            [
                'name' => 'Annual Wellness Visit',
                'cpt_code' => '99387',
                'duration' => '45 minutes',
                'category' => 'Evaluation & Management',
            ],

            // Procedures
            [
                'name' => 'EKG with Interpretation',
                'cpt_code' => '93000',
                'duration' => '15 minutes',
                'category' => 'Medicine',
            ],
            [
                'name' => 'Chest X-Ray, 2 Views',
                'cpt_code' => '71046',
                'duration' => '20 minutes',
                'category' => 'Radiology',
            ],

            // Lab
            [
                'name' => 'Complete Blood Count (CBC)',
                'cpt_code' => '85025',
                'duration' => '5 minutes',
                'category' => 'Pathology & Laboratory',
            ],

            // Immunizations
            [
                'name' => 'Influenza Vaccine',
                'cpt_code' => '90686',
                'duration' => '10 minutes',
                'category' => 'Medicine',
            ],
            [
                'name' => 'COVID-19 Vaccine',
                'cpt_code' => '91300',
                'duration' => '10 minutes',
                'category' => 'Medicine',
            ],
        ];

        foreach ($services as $service) {
            Service::create($service);
        }
    }
}
```

**Step 4: Update Service Selection Forms**

```php
// Update ReservationForm
Select::make('service_id')
    ->label('Service/Procedure')
    ->required()
    ->searchable()
    ->options(function (Get $get) {
        return Service::query()
            ->when($get('attending_physician_id'), function ($q) use ($get) {
                $q->whereHas('users', fn($q) => $q->where('user_id', $get('attending_physician_id')));
            })
            ->get()
            ->mapWithKeys(function ($service) {
                $label = $service->name;
                if ($service->cpt_code) {
                    $label .= " (CPT: {$service->cpt_code})";
                }
                return [$service->id => $label];
            });
    })
    ->helperText('Select the primary service or procedure for this visit'),
```

**Impact:** Service management, billing, insurance claims, scheduling

---

## Summary of All Hidden Assumptions

| # | Assumption | Severity | Impact Scope |
|---|-----------|----------|--------------|
| 1 | Age calculation (years only) | Medium | Display, UI, forms |
| 2 | Dangerous flag (binary) | High | Patient safety, risk management |
| 3 | Client-Patient navigation | Critical | Architecture, UX, portal |
| 4 | Medical doc structure (free-text) | High | Documentation, billing, compliance |
| 5 | Provider availability (simple schedule) | Medium | Scheduling, 24/7 ops |
| 6 | Croatian fiscalization | High | Billing, compliance, deployment |
| 7 | Portal auth (simple) | Critical | Security, HIPAA, privacy |
| 8 | Calendar privacy | Critical | HIPAA, staff workflows |
| 9 | Document locking (editable) | Critical | Legal, compliance |
| 10 | Service catalog (vet-specific) | High | Services, billing, insurance |

---

**Document Status:** Complete
**Last Updated:** 2025-11-16
**Maintained By:** Development Team
