# Complete File Change Manifest

**Vetlio → Coodely Hospital Transformation**
**Version:** 1.0
**Date:** 2025-11-16

---

## Overview

This document provides an exhaustive list of every file that needs to be created, modified, or deleted during the transformation from Vetlio (pet clinic) to Coodely Hospital (human patient clinic).

---

## Files to DELETE

### Models (2 files)
```
✗ app/Models/Species.php
✗ app/Models/Breed.php
```

### Seeders (1 file)
```
✗ database/seeders/SpeciesAndBreeds.php
```

**Total Files to Delete: 3**

---

## Files to CREATE

### Migrations (10 files)

```
+ database/migrations/2025_11_16_000001_remove_species_and_breeds.php
+ database/migrations/2025_11_16_000002_add_human_fields_to_patients.php
+ database/migrations/2025_11_16_000003_rename_clients_to_guardians.php
+ database/migrations/2025_11_16_000004_create_insurance_plans_table.php
+ database/migrations/2025_11_16_000005_create_prescriptions_table.php
+ database/migrations/2025_11_16_000006_create_vital_signs_table.php
+ database/migrations/2025_11_16_000007_create_lab_orders_table.php
+ database/migrations/2025_11_16_000008_create_diagnoses_table.php
+ database/migrations/2025_11_16_000009_create_immunizations_table.php
+ database/migrations/2025_11_16_000010_rename_service_provider_columns.php
```

### Models (7 files)

```
+ app/Models/Guardian.php                    [Renamed from Client]
+ app/Models/InsurancePlan.php
+ app/Models/Prescription.php
+ app/Models/VitalSigns.php
+ app/Models/LabOrder.php
+ app/Models/Diagnosis.php
+ app/Models/Immunization.php
```

### Enums (2 files)

```
+ app/Enums/PrescriptionStatus.php
+ app/Enums/LabOrderStatus.php
```

### Services (3 files)

```
+ app/Services/PrescriptionService.php
+ app/Services/VitalSignsService.php
+ app/Services/LabOrderService.php
```

### Filament Resources (6 new resources)

```
+ app/Filament/App/Resources/Prescriptions/
  ├── PrescriptionResource.php
  ├── Pages/
  │   ├── ListPrescriptions.php
  │   ├── CreatePrescription.php
  │   └── EditPrescription.php
  ├── Schemas/
  │   ├── PrescriptionForm.php
  │   ├── PrescriptionTable.php
  │   └── PrescriptionInfolist.php

+ app/Filament/App/Resources/LabOrders/
  ├── LabOrderResource.php
  ├── Pages/
  │   ├── ListLabOrders.php
  │   ├── CreateLabOrder.php
  │   └── EditLabOrder.php
  ├── Schemas/
  │   ├── LabOrderForm.php
  │   ├── LabOrderTable.php
  │   └── LabOrderInfolist.php

+ app/Filament/App/Resources/Diagnoses/
  ├── DiagnosisResource.php
  ├── Pages/
  │   ├── ListDiagnoses.php
  │   ├── CreateDiagnosis.php
  │   └── EditDiagnosis.php
  ├── Schemas/
  │   ├── DiagnosisForm.php
  │   ├── DiagnosisTable.php
  │   └── DiagnosisInfolist.php

+ app/Filament/App/Resources/Immunizations/
  ├── ImmunizationResource.php
  ├── Pages/
  │   ├── ListImmunizations.php
  │   ├── CreateImmunization.php
  │   └── EditImmunization.php
  ├── Schemas/
  │   ├── ImmunizationForm.php
  │   ├── ImmunizationTable.php
  │   └── ImmunizationInfolist.php

+ app/Filament/App/Resources/VitalSigns/
  ├── VitalSignsResource.php
  ├── Pages/
  │   ├── ListVitalSigns.php
  │   ├── CreateVitalSigns.php
  │   └── EditVitalSigns.php
  ├── Schemas/
  │   ├── VitalSignsForm.php
  │   ├── VitalSignsTable.php
  │   └── VitalSignsInfolist.php

+ app/Filament/App/Resources/InsurancePlans/
  ├── InsurancePlanResource.php
  ├── Pages/
  │   ├── ListInsurancePlans.php
  │   ├── CreateInsurancePlan.php
  │   └── EditInsurancePlan.php
  ├── Schemas/
  │   ├── InsurancePlanForm.php
  │   ├── InsurancePlanTable.php
  │   └── InsurancePlanInfolist.php
```

### Factories (7 files)

```
+ database/factories/GuardianFactory.php       [Renamed from ClientFactory]
+ database/factories/InsurancePlanFactory.php
+ database/factories/PrescriptionFactory.php
+ database/factories/VitalSignsFactory.php
+ database/factories/LabOrderFactory.php
+ database/factories/DiagnosisFactory.php
+ database/factories/ImmunizationFactory.php
```

### Seeders (3 files)

```
+ database/seeders/MedicalCodesSeeder.php      [ICD-10, CPT codes]
+ database/seeders/InsuranceProvidersSeeder.php
+ database/seeders/MedicationsSeeder.php       [Common medications]
```

### Documentation (5 files)

```
+ docs/TRANSFORMATION_PLAN.md                  [This document's parent]
+ docs/FILE_CHANGE_MANIFEST.md                 [This document]
+ docs/ARCHITECTURE_DECISIONS.md
+ docs/HIDDEN_ASSUMPTIONS.md
+ docs/RISK_ASSESSMENT.md
```

**Total Files to Create: 100+**

---

## Files to MODIFY

### High Priority - Critical Changes (24 files)

#### Models (5 files)

```
M app/Models/Patient.php
  - Remove species/breed relationships
  - Remove color, dangerous, dangerous_note fields
  - Add blood_type, height, weight, ssn, mrn fields
  - Add insurance fields
  - Add emergency contact fields
  - Update description() method
  - Add human-specific accessors/mutators

M app/Models/Client.php → app/Models/Guardian.php
  - Rename file
  - Rename class
  - Update namespace
  - Update relatedLabel() method
  - Update relationships

M app/Models/Reservation.php
  - Update service_provider_id → attending_physician_id references
  - Update terminology in methods

M app/Models/MedicalDocument.php
  - Add relationships: prescriptions, vitalSigns, labOrders, diagnoses
  - Update relatedLabel() method (remove Croatian)
  - Add methods for complete medical record

M app/Models/User.php
  - Rename service_provider column references
  - Update scopes: whereServiceProvider() → whereAttendingPhysician()
  - Update toCalendarResource() method
```

#### Filament App Resources (10 files)

```
M app/Filament/App/Resources/Patients/PatientResource.php
  - Update icon (dog → person)
  - Update navigation label
  - Update search includes (remove species/breed)
  - Update global search

M app/Filament/App/Resources/Patients/Schemas/PatientForm.php
  - Remove species_id, breed_id, color fields
  - Add blood_type field
  - Add height/weight fields with BMI calculation
  - Add SSN/MRN fields
  - Add insurance section
  - Add emergency contact section
  - Rename dangerous → special_needs
  - Update labels

M app/Filament/App/Resources/Patients/Schemas/PatientInfolist.php
  - Remove species/breed display
  - Add blood type display
  - Add height/weight/BMI display
  - Add insurance info display
  - Add emergency contact display
  - Update layout

M app/Filament/App/Resources/Patients/Tables/PatientsTable.php
  - Remove species/breed columns
  - Add blood_type column
  - Add mrn column
  - Add insurance_provider column
  - Update search
  - Update filters

M app/Filament/App/Resources/Reservations/ReservationResource.php
  - Update icon
  - Update labels (Veterinarian → Doctor)

M app/Filament/App/Resources/Reservations/Schemas/ReservationForm.php
  - Update patient field icon (dog → person)
  - Update service_provider_id → attending_physician_id
  - Update label: "Veterinarian" → "Doctor"
  - Update availability conflict message
  - Remove species/breed filtering

M app/Filament/App/Resources/Reservations/Schemas/ReservationInfolist.php
  - Update labels
  - Update relationships display

M app/Filament/App/Resources/Reservations/Tables/ReservationsTable.php
  - Update column labels
  - Update relationships

M app/Filament/App/Resources/MedicalDocuments/Schemas/MedicalDocumentForm.php
  - Add vital signs section
  - Add diagnosis section
  - Add prescriptions repeater
  - Add lab orders section
  - Update patient display

M app/Filament/App/Resources/MedicalDocuments/Tables/MedicalDocumentsTable.php
  - Update patient display (remove species/breed)
  - Add diagnosis column
  - Update search
```

#### Widgets (2 files)

```
M app/Filament/App/Widgets/AppointmentsTodayWidget.php
  - Update patient display
  - Update veterinarian → doctor labels

M app/Filament/App/Widgets/CalendarWidget.php
  - Update event display
  - Add privacy controls (HIPAA)
  - Update terminology
```

#### Portal Resources (5 files)

```
M app/Filament/Portal/Resources/Patients/PatientResource.php
  - Update for guardian model
  - Update labels

M app/Filament/Portal/Resources/Patients/Schemas/PatientForm.php
  - Remove species/breed
  - Add human fields (read-only)

M app/Filament/Portal/Resources/Patients/Schemas/PatientInfolist.php
  - Remove species/breed display
  - Add human fields display

M app/Filament/Portal/Resources/Patients/Tables/PatientsTable.php
  - Update columns
  - Remove species/breed

M app/Filament/Portal/Actions/AppointmentRequestAction.php
  - Update patient selection
  - Remove species/breed
  - Update labels
```

#### Panel Providers (2 files)

```
M app/Providers/Filament/AppPanelProvider.php
  - Update navigation
  - Add new resources
  - Update labels

M app/Providers/Filament/PortalPanelProvider.php
  - Update authentication guard (client → guardian)
  - Update navigation
  - Update labels
```

---

### Medium Priority - Important Changes (30 files)

#### Database Factories (7 files)

```
M database/factories/PatientFactory.php
  - Remove species_id, breed_id
  - Add blood_type
  - Add height, weight
  - Add SSN, MRN generation
  - Add insurance fields
  - Add emergency contact
  - Add human-specific test data
  - Update demo() state

M database/factories/ClientFactory.php → GuardianFactory.php
  - Rename file and class
  - Update relationships

M database/factories/ReservationFactory.php
  - Update service_provider_id references

M database/factories/MedicalDocumentFactory.php
  - Update to include vitals, diagnosis

M database/factories/UserFactory.php
  - Update service_provider field

M database/factories/InvoiceFactory.php
  - Update client_id → guardian_id

M database/factories/PaymentFactory.php
  - Update client_id → guardian_id
```

#### Seeders (3 files)

```
✗ database/seeders/SpeciesAndBreeds.php        [DELETE]

M database/seeders/DemoOrganisationSeeder.php
  - Remove species/breed seeding
  - Update patient seeding with human data
  - Update client → guardian references
  - Add insurance plan seeding
  - Add prescription seeding
  - Add vitals seeding

M database/seeders/DatabaseSeeder.php
  - Remove SpeciesAndBreeds::class
  - Add new seeders
```

#### Views - PDF Templates (5 files)

```
M resources/views/pdf/invoice.blade.php
  - Remove Croatian text
  - Update labels
  - Update client → guardian

M resources/views/pdf/layouts/header.blade.php
  - Update branding (Vetlio → Coodely Hospital)
  - Update logo

M resources/views/pdf/layouts/footer.blade.php
  - Update branding
  - Update contact info

M resources/views/pdf/layouts/app.blade.php
  - Update styles
  - Update branding

M resources/views/components/pdf-layout.blade.php
  - Update layout for hospital context
```

#### Views - Email Templates (3 files)

```
M resources/views/emails/generic.blade.php
  - Update branding
  - Remove Croatian text

M resources/views/mail/generic-mail.blade.php
  - Update branding

M resources/views/calendar/event.blade.php
  - Update patient display
  - Add privacy controls
```

#### Services (4 files)

```
M app/Services/ReservationService.php
  - Update terminology
  - Update patient validation

M app/Services/AvailableSlotsService.php
  - Update service provider references

M app/Services/InvoiceService.php
  - Update client → guardian

M app/Services/InvoiceCalculator.php
  - Add insurance calculations
```

#### Language Files (3 files)

```
M lang/en/enums.php
  - Remove/update patient_gender context
  - Add new enums for prescriptions, lab orders

M lang/en/validation.php
  - Add human-specific validations

+ lang/en/medical.php                          [NEW]
  - Medical terminology translations
```

#### Configuration Files (5 files)

```
M .env.example
  - Change: DB_DATABASE=vetlio → coodely_hospital
  - Change: MAIL_FROM_ADDRESS
  - Update app name

M config/app.php
  - Update app name
  - Update timezone if needed

M config/auth.php
  - Rename 'client' guard → 'guardian'

M composer.json
  - Update package name
  - Update description
  - Update repository info

M package.json
  - Update name
  - Update description
```

---

### Low Priority - Cosmetic Changes (33 files)

#### Documentation (5 files)

```
M README.md
  - Complete rewrite for Coodely Hospital
  - Update features list
  - Update installation instructions
  - Update screenshots
  - Remove veterinary references

M CHANGELOG.md
  - Add transformation entry

+ docs/ADMIN_GUIDE.md                          [NEW]
+ docs/USER_GUIDE.md                           [NEW]
+ docs/API_DOCUMENTATION.md                    [NEW if API exists]
```

#### Assets (15 files - all screenshots)

```
M docs/screenshots/dashboard.png
M docs/screenshots/login.png
M docs/screenshots/waiting-room.png
M docs/screenshots/email-templates.png
M docs/screenshots/edit-service.png
M docs/screenshots/invoice-form.png
M docs/screenshots/invoice-view.png
M docs/screenshots/arrival-confirmation.png
M docs/screenshots/portal.png
M docs/screenshots/portal-appointment-request.png
M docs/screenshots/users.png
M docs/screenshots/send-email.png
M docs/screenshots/reservation-form.png
M docs/screenshots/client-card.png
M docs/assets/logo.png
```

#### App Panel Pages (5 files)

```
M app/Filament/App/Pages/Dashboard.php
  - Update stats/widgets
  - Update terminology

M app/Filament/App/Pages/Calendar.php
  - Update event display
  - Add privacy controls

M app/Filament/App/Pages/AppointmentRequests.php
  - Update patient display
  - Update terminology

M app/Filament/App/Pages/WaitingRoom.php
  - Update patient display
  - Remove species/breed

M app/Filament/App/Clusters/Settings/Pages/Organisation.php
  - Update branding fields
```

#### Portal Pages (3 files)

```
M app/Filament/Portal/Pages/Dashboard.php
  - Update for guardian context

M app/Filament/Portal/Pages/Appointments.php
  - Update patient display

M app/Filament/Portal/Pages/MedicalDocuments.php
  - Update patient display
  - Add privacy notices
```

#### Auth Views (3 files)

```
M resources/views/filament/app/auth/custom-login-view.blade.php
  - Update branding

M resources/views/filament/portal/auth/custom-login-view.blade.php
  - Update branding
  - Update text (client → guardian)

M resources/views/filament/public/pages/confirm-appointment-arrival.blade.php
  - Update branding
  - Update terminology
```

#### Observers (2 files)

```
M app/Observers/ReservationObserver.php
  - Update service provider references

M app/Observers/PatientObserver.php           [if exists]
  - Remove species/breed logic
```

---

## Global Find & Replace Operations

Execute these across the entire codebase (case-sensitive):

### Database/Schema Terms
```bash
"species_id" → [REMOVE/DELETE]
"breed_id" → [REMOVE/DELETE]
"client_id" → "guardian_id"
"service_provider" → "attending_physician"
"clients" (table) → "guardians"
```

### Model/Class Names
```bash
"Species" → [DELETE REFERENCES]
"Breed" → [DELETE REFERENCES]
"Client::" → "Guardian::"
"App\\Models\\Client" → "App\\Models\\Guardian"
```

### UI/Display Terms
```bash
"Veterinarian" → "Doctor"
"veterinarian" → "doctor"
"Owner" → "Guardian"
"owner" → "guardian"
"Pet" → "Patient"
"pet" → "patient"
```

### Croatian to English
```bash
"Klijent" → "Guardian"
"Nalaz" → "Medical Record"
"opasan" → "at-risk"
" god." → " years old"
"Račun" → "Invoice"
"Kupac" → "Customer"
"Osnovica" → "Subtotal"
"PDV" → "VAT"
"Ukupno za platiti" → "Total Amount"
"Hvala na povjerenju" → "Thank you"
```

### Configuration/Environment
```bash
"vetlio" → "coodely_hospital"
"Vetlio" → "Coodely Hospital"
"vetlio.test" → "coodely-hospital.test"
"hello@vetlio.com" → "hello@coodely.hospital"
```

---

## Summary Statistics

| Category | Delete | Create | Modify | Total |
|----------|--------|--------|--------|-------|
| Models | 2 | 7 | 5 | 14 |
| Migrations | 0 | 10 | 0 | 10 |
| Factories | 0 | 7 | 7 | 14 |
| Seeders | 1 | 3 | 2 | 6 |
| Services | 0 | 3 | 4 | 7 |
| Resources | 0 | 6 | 3 | 9 |
| Resource Schemas | 0 | 18 | 10 | 28 |
| Views | 0 | 0 | 13 | 13 |
| Config | 0 | 1 | 5 | 6 |
| Documentation | 0 | 5 | 2 | 7 |
| Assets | 0 | 0 | 15 | 15 |
| Other | 0 | 3 | 20 | 23 |
| **TOTAL** | **3** | **63** | **86** | **152** |

---

## Verification Checklist

After transformation, verify:

- [ ] All species/breed references removed
- [ ] All Croatian text translated
- [ ] All patient forms have human fields
- [ ] All guardians (formerly clients) renamed
- [ ] All service providers renamed to doctors/physicians
- [ ] All new resources functional
- [ ] All migrations run successfully
- [ ] All tests pass
- [ ] All seeders produce human data
- [ ] All PDF templates updated
- [ ] All email templates updated
- [ ] All screenshots replaced
- [ ] Documentation complete
- [ ] Privacy controls in place
- [ ] No veterinary terminology remaining

---

**Document Status:** Complete
**Last Updated:** 2025-11-16
**Maintained By:** Development Team
