# 🔄 Transformation Mapping: Vetlio → Coodely Hospital

This document provides a comprehensive mapping of all entities, concepts, and terminology from the veterinary domain to the healthcare domain.

---

## Core Entity Mapping

### Patient vs Client Paradigm

| Vetlio (Veterinary) | Coodely Hospital (Healthcare) | Rationale |
|---------------------|-------------------------------|-----------|
| **Client** (Pet owner) | **Patient** (Person receiving care) | In healthcare, the person receiving medical care is called the patient |
| **Patient** (Pet/Animal) | *Removed* | In human healthcare, there's no separate entity - patients ARE the clients |

### Primary Entities

| Vetlio Entity | Coodely Entity | Changes Required |
|---------------|----------------|------------------|
| `clients` table | `patients` table | **Rename table**, add healthcare fields |
| `patients` table | *Delete* | No longer needed |
| `species` table | *Delete* | Not applicable for humans |
| `breeds` table | *Delete* | Not applicable for humans |
| `reservations` table | `appointments` table | **Rename**, update terminology |
| `branches` table | `departments` table | **Rename**, restructure for medical departments |
| `organisations` table | `hospitals` table | **Rename** (optional, can keep as organisations) |
| `service_provider` (User) | `doctor` | **Terminology change** |
| `medical_documents` table | `medical_records` table | **Rename**, expand for SOAP notes |

---

## Detailed Entity Transformations

### 1. Patient (formerly Client + Patient combined)

**Vetlio Client Table:**
```sql
clients:
- id
- first_name, last_name
- phone, email
- address, city, zip_code
- country_id, gender_id
- date_of_birth
- oib (tax ID)
- active
- organisation_id
```

**Vetlio Patient Table (Animal):**
```sql
patients:
- id
- name
- client_id (owner)
- species_id (Dog, Cat, etc.)
- breed_id (Golden Retriever, etc.)
- gender_id
- date_of_birth
- dangerous (flag)
- allergies
- remarks
```

**Coodely Patient Table (Human):**
```sql
patients:
- id
- first_name, last_name
- phone, email
- address, city, zip_code
- country_id
- gender_id
- date_of_birth
- blood_type_id (NEW: A+, O-, etc.)
- emergency_contact_name (NEW)
- emergency_contact_phone (NEW)
- marital_status (NEW)
- occupation (NEW)
- photo_url
- active
- hospital_id
- created_at, updated_at, deleted_at

REMOVED:
- species_id (not applicable)
- breed_id (not applicable)
- dangerous flag (replaced by medical_alerts)
- allergies column (moved to dedicated allergies table)
```

**Migration Path:**
1. Rename `clients` → `patients`
2. Add new healthcare columns
3. Migrate allergy data to `allergies` table
4. Drop `patients` table (animals)
5. Update all foreign keys

---

### 2. Appointments (formerly Reservations)

**Vetlio Reservations:**
```sql
reservations:
- date, from, to
- client_id
- patient_id (animal)
- service_provider_id (vet)
- service_id
- status_id (Ordered, WaitingRoom, InProcess, Completed)
- room_id
- branch_id
- reason_for_coming
- waiting_room_at, in_process_at, completed_at
```

**Coodely Appointments:**
```sql
appointments:
- date, start_time, end_time
- patient_id (human)
- doctor_id (renamed from service_provider_id)
- service_id (procedure/consultation)
- appointment_type_id (NEW: Consultation, Follow-up, Emergency, Procedure)
- status_id (Scheduled, CheckedIn, InProgress, Completed, Cancelled)
- room_id
- department_id (renamed from branch_id)
- reason
- notes
- checked_in_at (renamed from waiting_room_at)
- started_at (renamed from in_process_at)
- completed_at
- cancelled_at
- cancel_reason_id
- hospital_id

REMOVED:
- Reference to animal patient
```

**Status Enum Mapping:**
```
Vetlio                Coodely
Ordered (1)       →   Scheduled (1)
WaitingRoom (2)   →   CheckedIn (2)
InProcess (3)     →   InProgress (3)
Completed (4)     →   Completed (4)
                  +   Cancelled (5) - enhanced
                  +   NoShow (6) - new
```

---

### 3. Medical Records (formerly Medical Documents)

**Vetlio Medical Documents:**
```sql
medical_documents:
- code (auto-generated)
- patient_id (animal)
- client_id (owner)
- reservation_id
- service_provider_id (vet)
- content (clinical findings)
- locked_at
- price_list_id
```

**Coodely Medical Records:**
```sql
medical_records:
- code
- patient_id (human)
- appointment_id (renamed from reservation_id)
- doctor_id (renamed from service_provider_id)
- department_id
- visit_date
- chief_complaint (NEW - SOAP: Subjective)
- history_of_present_illness (NEW - SOAP: Subjective)
- physical_examination (NEW - SOAP: Objective)
- assessment (NEW - SOAP: Assessment/Diagnosis)
- plan (NEW - SOAP: Plan/Treatment)
- follow_up_date (NEW)
- locked_at
- locked_by
- price_list_id
- hospital_id

REMOVED:
- client_id (patient is the client in healthcare)
```

**SOAP Format Addition:**
SOAP (Subjective, Objective, Assessment, Plan) is the standard clinical note format:
- **Subjective**: Patient's complaints and symptoms
- **Objective**: Measurable findings (vitals, exam results)
- **Assessment**: Doctor's diagnosis
- **Plan**: Treatment plan and next steps

---

### 4. Departments (formerly Branches)

**Vetlio Branches:**
```sql
branches:
- name (location name)
- address, city, zip_code
- phone, email
- branch_mark (for fiscalization)
- organisation_id
```

**Coodely Departments:**
```sql
departments:
- name (Cardiology, Emergency, Pediatrics, etc.)
- code (DEPT-CARD, DEPT-ER, etc.)
- description
- department_type (NEW: Inpatient, Outpatient, Emergency, Diagnostic)
- floor_number (NEW)
- phone, email
- head_of_department_id (NEW: User reference)
- hospital_id
- active

Purpose: Medical specializations/departments instead of physical locations
```

**Common Department Types:**
- Cardiology
- Emergency/ER
- Pediatrics
- Obstetrics & Gynecology
- Orthopedics
- Radiology
- Laboratory
- Pharmacy
- General Medicine
- Surgery

---

### 5. Users (Staff)

**Vetlio Users:**
```sql
users:
- first_name, last_name
- email, password
- phone
- title
- administrator (boolean)
- service_provider (boolean - can perform services)
- primary_branch_id
- colour (for calendar)
- organisation_id
```

**Coodely Users:**
```sql
users:
- first_name, last_name
- email, password
- phone
- title (Dr., RN, etc.)
- role_id (NEW: Doctor, Nurse, Admin, Receptionist, Lab Tech, Pharmacist)
- specialization (NEW: Cardiology, Pediatrics, etc.)
- license_number (NEW)
- administrator (boolean)
- can_see_patients (renamed from service_provider)
- is_available (NEW: for scheduling)
- primary_department_id (renamed from primary_branch_id)
- color (for calendar)
- hospital_id
```

**User Roles Enum:**
```php
enum UserRole: int {
    case Admin = 1;
    case Doctor = 2;
    case Nurse = 3;
    case Receptionist = 4;
    case LabTechnician = 5;
    case Pharmacist = 6;
}
```

**Specializations (for Doctors):**
- General Practice
- Cardiology
- Dermatology
- Emergency Medicine
- Family Medicine
- Internal Medicine
- Neurology
- Obstetrics & Gynecology
- Pediatrics
- Psychiatry
- Surgery
- Orthopedics
- Radiology

---

### 6. Services (Procedures/Consultations)

**Vetlio Services:**
```sql
services:
- name (Vaccination, Surgery, Checkup)
- code
- duration (minutes)
- color (for calendar)
- service_group_id
- organisation_id
```

**Coodely Services:**
```sql
services:
- name (General Consultation, Blood Test, X-Ray, Surgery)
- code (CPT code in US, local codes elsewhere)
- service_category_id (NEW: Consultation, Diagnostic, Procedure, Surgery)
- department_id (NEW: which department performs this)
- description
- duration (minutes)
- price
- is_active
- hospital_id

Categories:
- Consultation
- Diagnostic Test
- Lab Test
- Imaging (X-Ray, MRI, CT)
- Minor Procedure
- Major Surgery
- Therapy
- Vaccination
```

---

### 7. Invoicing & Billing

**Vetlio Invoices:**
```sql
invoices:
- code
- client_id
- invoice_date
- payment_method_id
- total, total_tax, total_base_price
- zki, jir (Croatian fiscalization)
- qrcode
- fiscalization_at
- organisation_id
```

**Coodely Invoices:**
```sql
invoices:
- code
- patient_id (renamed from client_id)
- invoice_date
- insurance_claim_id (NEW: link to insurance)
- patient_responsibility (NEW: co-pay, deductible)
- insurance_coverage (NEW)
- payment_method_id
- total, total_tax, total_base_price
- fiscalization_at (if applicable by country)
- hospital_id

NEW: Insurance integration
- Insurance provider
- Coverage percentage
- Deductible amount
- Co-pay amount
- Claim status
```

---

## New Healthcare-Specific Entities

These entities don't exist in Vetlio and need to be created from scratch:

### 1. Vital Signs

```sql
vital_signs:
- patient_id
- medical_record_id (optional link)
- appointment_id (optional link)
- blood_pressure_systolic (e.g., 120)
- blood_pressure_diastolic (e.g., 80)
- heart_rate (beats per minute)
- respiratory_rate (breaths per minute)
- temperature (Celsius or Fahrenheit)
- temperature_unit ('C' or 'F')
- weight (kg or lbs)
- weight_unit
- height (cm or inches)
- height_unit
- oxygen_saturation (percentage)
- recorded_at
- recorded_by (user_id)
- hospital_id
```

### 2. Prescriptions

```sql
prescriptions:
- prescription_number
- patient_id
- doctor_id
- medical_record_id (optional)
- medication_id
- medication_name
- dosage
- frequency
- route (Oral, IV, IM, etc.)
- duration_value, duration_unit
- quantity
- refills
- instructions
- issued_at
- expires_at
- status_id (Active, Filled, Expired, Cancelled)
- hospital_id
```

### 3. Lab Tests & Results

```sql
lab_tests:
- test_number
- patient_id
- ordered_by (doctor_id)
- medical_record_id (optional)
- test_type_id
- test_name
- status_id (Ordered, In-Progress, Completed)
- ordered_at
- collected_at
- completed_at
- performed_by (user_id - lab technician)
- hospital_id

lab_results:
- lab_test_id
- parameter_name (e.g., "Hemoglobin")
- value
- unit (g/dL, mg/dL, etc.)
- reference_range_min
- reference_range_max
- is_abnormal (boolean flag)
- notes
```

### 4. Diagnoses

```sql
diagnoses:
- patient_id
- medical_record_id
- icd10_code (standardized diagnosis code)
- diagnosis_name
- diagnosis_type (Primary, Secondary)
- severity
- diagnosed_at
- resolved_at (if applicable)
- notes
- diagnosed_by (doctor_id)
- hospital_id
```

### 5. Allergies

```sql
allergies:
- patient_id
- allergen
- allergy_type (Drug, Food, Environmental, Other)
- severity (Mild, Moderate, Severe, Life-threatening)
- reaction
- diagnosed_at
- notes
- hospital_id
```

### 6. Immunizations

```sql
immunizations:
- patient_id
- vaccine_name
- vaccine_code (CVX code)
- administered_at
- administered_by (user_id)
- dose_number
- site (Left arm, Right arm, etc.)
- manufacturer
- lot_number
- expiration_date
- next_due_date
- notes
- hospital_id
```

### 7. Insurance Providers

```sql
insurance_providers:
- patient_id
- provider_name
- policy_number
- group_number
- coverage_type (Primary, Secondary)
- effective_date
- expiration_date
- copay_amount
- deductible_amount
- notes
- hospital_id
```

---

## Terminology Changes

### General Terms

| Vetlio | Coodely | Context |
|--------|---------|---------|
| Client | Patient | The person receiving care |
| Patient | *N/A* | The animal (removed) |
| Veterinarian | Doctor/Physician | Medical professional |
| Clinic | Hospital/Clinic | Facility |
| Branch | Department | Organizational unit |
| Service Provider | Doctor | Person providing care |
| Reservation | Appointment | Scheduled visit |
| Medical Document | Medical Record | Clinical documentation |

### Status & Workflow Terms

| Vetlio | Coodely | Context |
|--------|---------|---------|
| Ordered | Scheduled | Appointment booked |
| Waiting Room | Checked-In | Patient arrived |
| In Process | In Progress | Currently seeing doctor |
| Completed | Completed | Visit finished |
| Dangerous Animal | Medical Alert | Critical patient flag |

### Medical-Specific Terms

| Concept | Healthcare Term | Description |
|---------|----------------|-------------|
| Animal Species | Blood Type | Medical classification |
| Breed | *Removed* | Not applicable |
| Owner Contact | Emergency Contact | Person to notify |
| Vaccination | Immunization | Preventive medicine |
| Finding | Diagnosis | Medical conclusion |
| Treatment | Prescription/Plan | Medical intervention |

---

## Removed Concepts

These veterinary-specific concepts are removed entirely:

1. **Species** - Not applicable for humans
2. **Breed** - Not applicable for humans
3. **Dangerous Animal Flag** - Replaced by general medical alerts/allergies
4. **Pet-specific treatments** - Replaced by human medical procedures
5. **Animal behavior notes** - Replaced by patient history

---

## Enhanced Concepts

### From Vetlio's Strengths to Healthcare Enhancements

| Vetlio Feature | Enhanced for Healthcare |
|----------------|------------------------|
| **Appointment Scheduling** | + Appointment types (consultation, follow-up, emergency)<br>+ Walk-in support<br>+ Recurring appointments |
| **Calendar View** | + Multi-resource scheduling<br>+ Department-based filtering<br>+ Color-coding by appointment type |
| **Email Reminders** | + SMS reminders<br>+ Multi-language support<br>+ Customizable reminder timing |
| **Client Portal** | + View lab results<br>+ Download prescriptions<br>+ Request prescription refills<br>+ Upload insurance documents |
| **Medical Documents** | + SOAP note format<br>+ E-signature support<br>+ ICD-10 diagnosis codes<br>+ CPT procedure codes |
| **Invoicing** | + Insurance claim processing<br>+ Co-pay calculation<br>+ Payment plans<br>+ Explanation of Benefits (EOB) |
| **Multi-tenancy** | + Department-based access control<br>+ Specialty-based routing<br>+ Cross-department referrals |

---

## Implementation Priority

### Phase 1 (Critical - Core Functionality)
1. Patient management (Client → Patient transformation)
2. Appointments (Reservation → Appointment transformation)
3. Medical Records (Medical Document enhancement)
4. User roles and permissions

### Phase 2 (High Priority - Medical Workflow)
1. Vital Signs
2. Prescriptions
3. Diagnoses
4. Allergies

### Phase 3 (Important - Clinical Support)
1. Lab Tests & Results
2. Immunizations
3. Medical history timeline

### Phase 4 (Valuable - Financial)
1. Insurance integration
2. Enhanced billing
3. Claims processing

---

## Migration Checklist

- [ ] Database tables renamed
- [ ] All foreign keys updated
- [ ] Enum values mapped correctly
- [ ] Data migrated from old to new structure
- [ ] Veterinary-specific columns removed (after data backup)
- [ ] New healthcare columns added
- [ ] Models updated with new relationships
- [ ] Filament resources renamed and reconfigured
- [ ] Routes updated
- [ ] Email templates updated with new terminology
- [ ] Frontend text updated (buttons, labels, headings)
- [ ] Documentation updated
- [ ] Tests updated and passing

---

**Document Version**: 1.0
**Last Updated**: November 2025
