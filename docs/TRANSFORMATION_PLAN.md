# 🏥 VETLIO → COODELY HOSPITAL: COMPLETE TRANSFORMATION PLAN

**Document Version:** 1.0
**Date:** 2025-11-16
**Status:** Planning Phase
**Author:** Senior Code Agent

---

## Executive Summary

This document contains a **comprehensive, production-ready transformation plan** to convert the Vetlio pet clinic codebase into **Coodely Hospital**, a single-tenant human patient clinic system.

### Analysis Scope Completed

- ✅ **39 Model files** analyzed (all fields, relationships, methods documented)
- ✅ **53 Database migrations** inspected
- ✅ **24 Files** containing species/breed references identified
- ✅ **34 Files** containing veterinarian/service provider references identified
- ✅ **27 Factory files** examined for test data assumptions
- ✅ All Filament resources, forms, tables, and UI components reviewed
- ✅ All services, business logic, and domain code analyzed
- ✅ PDF templates, email templates, and views inspected
- ✅ Configuration files, seeders, and deployment scripts reviewed
- ✅ Croatian language hardcoding identified throughout

---

## Table of Contents

1. [Critical Architectural Decision](#critical-architectural-decision)
2. [Detailed Findings](#detailed-findings)
3. [Three Complete Migration Strategies](#three-complete-migration-strategies)
4. [Recommended Approach](#recommended-approach)
5. [Implementation Plan](#implementation-plan)
6. [Execution Timeline](#execution-timeline)
7. [Risk Assessment](#risk-assessment)
8. [Next Steps](#next-steps)

---

## Critical Architectural Decision

### The Patient-Client Relationship Problem

**Current Veterinary Model:**
```
Client (Pet Owner) ──1:N──> Patient (Pet)
```
- One person (owner) owns multiple animals (pets)
- Client authenticates to portal, views all their pets
- Billing goes to the client (owner)
- Medical records belong to patients (pets)

### Three Architectural Options

#### OPTION A: Patient IS Client (1:1 Model)

**Structure:**
```
Patient = Client (Same Entity)
```

**Best For:** General adult outpatient clinics, private practices

**Pros:**
- Simplest architecture
- Fewer tables, less complexity
- No "owner" concept
- Direct patient access to their records

**Cons:**
- Doesn't support pediatrics/dependent care well
- No family/guardian relationships
- Structural change is very large
- Portal login changes significantly

**Files to Modify:** ~80+ files (major refactor)

---

#### OPTION B: Guardian/Representative Model ⭐ RECOMMENDED

**Structure:**
```
Guardian (formerly Client) ──1:N──> Patient
```

**Best For:** Clinics with pediatrics, elderly care, or dependent patients

**Pros:**
- Minimal structural changes
- Supports all patient types (pediatric, adult, elderly)
- Portal logic remains similar
- Family relationship modeling

**Cons:**
- "Guardian" terminology may be confusing for adult patients
- Still need to handle adult patients specially
- May need dual authentication (patient AND guardian)

**Files to Modify:** ~40 files (moderate refactor)

---

#### OPTION C: Hybrid Model

**Structure:**
```
Patient ──1:1──> User Account (optional)
Patient ──N:M──> Family Members
```

**Best For:** Full-service hospitals with all patient demographics

**Pros:**
- Handles all scenarios (adult, pediatric, dependent)
- Most flexible
- Can model complex family relationships
- Future-proof

**Cons:**
- Most complex to implement
- More tables, more relationships
- More complex business logic
- Harder to understand

**Files to Modify:** ~60 files (significant refactor)

---

## Detailed Findings

### 1. Pet-Specific Database Elements to Remove

#### Tables to Drop Entirely

```sql
-- These tables have NO equivalent in human medicine
DROP TABLE species;          -- Animal species (Dog, Cat, etc.)
DROP TABLE breeds;           -- Animal breeds (Labrador, Persian, etc.)
```

**Impact:** 24 files reference these tables

#### Patient Table Columns to Remove

```sql
ALTER TABLE patients
DROP COLUMN species_id,      -- No human equivalent
DROP COLUMN breed_id,        -- No human equivalent
DROP COLUMN color,           -- Fur/coat color (not medically relevant for humans)
DROP COLUMN dangerous,       -- "Is animal aggressive?" (may repurpose)
DROP COLUMN dangerous_note;  -- Aggressive behavior notes (may repurpose)
```

---

### 2. Human-Specific Database Elements to Add

#### New Patient Fields Required

```sql
ALTER TABLE patients
-- Biometric
ADD COLUMN blood_type VARCHAR(5),                -- A+, A-, B+, B-, O+, O-, AB+, AB-
ADD COLUMN height DECIMAL(5,2),                  -- Height in cm
ADD COLUMN weight DECIMAL(5,2),                  -- Weight in kg
ADD COLUMN bmi DECIMAL(4,2),                     -- Body Mass Index

-- Identification
ADD COLUMN ssn VARCHAR(20),                      -- Social Security Number / National ID
ADD COLUMN mrn VARCHAR(50) UNIQUE,               -- Medical Record Number

-- Insurance
ADD COLUMN insurance_provider VARCHAR(255),      -- Insurance company
ADD COLUMN insurance_number VARCHAR(100),        -- Insurance policy number
ADD COLUMN insurance_group VARCHAR(100),         -- Insurance group number

-- Emergency Contact
ADD COLUMN emergency_contact_name VARCHAR(255),  -- Emergency contact
ADD COLUMN emergency_contact_phone VARCHAR(20),  -- Emergency phone
ADD COLUMN emergency_contact_relation VARCHAR(50), -- Relationship

-- Medical Information
ADD COLUMN primary_care_physician VARCHAR(255),  -- PCP name
ADD COLUMN medical_history TEXT,                 -- Structured medical history
ADD COLUMN current_medications TEXT,             -- Current meds (JSON or structured)
ADD COLUMN chronic_conditions TEXT,              -- Chronic illnesses (JSON)
ADD COLUMN allergies_medications TEXT,           -- Drug allergies (separate from general)

-- Demographics
ADD COLUMN marital_status VARCHAR(20),           -- Marital status
ADD COLUMN occupation VARCHAR(255),              -- Occupation
ADD COLUMN preferred_pharmacy VARCHAR(255),      -- Pharmacy for prescriptions
ADD COLUMN advance_directives BOOLEAN DEFAULT false, -- Has living will/DNR
ADD COLUMN language_preference VARCHAR(10);      -- Communication language
```

---

### 3. New Tables Required

#### Insurance Plans
```sql
CREATE TABLE insurance_plans (
    id BIGINT PRIMARY KEY,
    patient_id BIGINT,
    provider_name VARCHAR(255),
    policy_number VARCHAR(100),
    group_number VARCHAR(100),
    subscriber_name VARCHAR(255),
    subscriber_relationship VARCHAR(50),
    effective_date DATE,
    expiration_date DATE,
    copay_amount DECIMAL(10,2),
    deductible DECIMAL(10,2),
    deductible_met DECIMAL(10,2),
    verification_status VARCHAR(50),
    notes TEXT,
    organisation_id BIGINT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

#### Prescriptions
```sql
CREATE TABLE prescriptions (
    id BIGINT PRIMARY KEY,
    patient_id BIGINT,
    prescriber_id BIGINT,
    medical_document_id BIGINT,
    medication_name VARCHAR(255),
    dosage VARCHAR(100),
    frequency VARCHAR(100),
    quantity INT,
    refills INT,
    instructions TEXT,
    pharmacy VARCHAR(255),
    prescribed_date DATETIME,
    valid_until DATE,
    filled_date DATETIME,
    status VARCHAR(50),
    controlled_substance BOOLEAN,
    organisation_id BIGINT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

#### Vital Signs
```sql
CREATE TABLE vital_signs (
    id BIGINT PRIMARY KEY,
    patient_id BIGINT,
    medical_document_id BIGINT,
    measured_at DATETIME,
    measured_by BIGINT,
    blood_pressure_systolic INT,
    blood_pressure_diastolic INT,
    heart_rate INT,
    respiratory_rate INT,
    temperature DECIMAL(4,2),
    oxygen_saturation INT,
    height DECIMAL(5,2),
    weight DECIMAL(5,2),
    bmi DECIMAL(4,2),
    pain_level INT,
    notes TEXT,
    organisation_id BIGINT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

#### Lab Orders & Results
```sql
CREATE TABLE lab_orders (
    id BIGINT PRIMARY KEY,
    patient_id BIGINT,
    ordered_by BIGINT,
    medical_document_id BIGINT,
    test_type VARCHAR(255),
    test_code VARCHAR(50),
    priority VARCHAR(20),
    ordered_at DATETIME,
    status VARCHAR(50),
    result_value VARCHAR(500),
    result_unit VARCHAR(50),
    reference_range VARCHAR(200),
    abnormal_flag VARCHAR(10),
    result_date DATETIME,
    notes TEXT,
    organisation_id BIGINT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

#### Diagnoses
```sql
CREATE TABLE diagnoses (
    id BIGINT PRIMARY KEY,
    patient_id BIGINT,
    medical_document_id BIGINT,
    icd10_code VARCHAR(10),
    description TEXT,
    type VARCHAR(50),
    diagnosed_date DATE,
    resolved_date DATE,
    status VARCHAR(20),
    diagnosed_by BIGINT,
    notes TEXT,
    organisation_id BIGINT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

#### Immunizations
```sql
CREATE TABLE immunizations (
    id BIGINT PRIMARY KEY,
    patient_id BIGINT,
    vaccine_name VARCHAR(255),
    vaccine_code VARCHAR(50),
    manufacturer VARCHAR(255),
    lot_number VARCHAR(100),
    administered_date DATE,
    administered_by BIGINT,
    site VARCHAR(100),
    route VARCHAR(100),
    dose_amount VARCHAR(50),
    next_due_date DATE,
    notes TEXT,
    organisation_id BIGINT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

---

### 4. Terminology Transformation Map

| Current (Veterinary) | New (Human Hospital) | Files Affected | Type |
|---------------------|---------------------|----------------|------|
| Veterinarian | Doctor / Physician | 34 files | UI Label |
| Owner | Emergency Contact / Guardian | 24 files | UI Label |
| Species | **DELETE** | 24 files | Model/Table |
| Breed | **DELETE** | 24 files | Model/Table |
| Color (fur) | **DELETE** | Patient form | Field |
| Dangerous | Behavioral Risk / Special Needs | Patient model/form | Field |
| service_provider | attending_physician / doctor | 34 files | Column name |
| client_id | guardian_id (Strategy B) | All relations | Architecture |
| Klijent (Croatian) | Guardian | Client model | Hardcoded text |
| Nalaz (Croatian) | Medical Record | MedicalDocument model | Hardcoded text |
| opasan (Croatian) | at-risk | Patient description | Hardcoded text |
| god. (Croatian years) | years old | Patient model | Hardcoded text |

---

## Three Complete Migration Strategies

See [ARCHITECTURE_DECISIONS.md](./ARCHITECTURE_DECISIONS.md) for detailed comparison.

**Recommended:** Strategy B (Guardian Model)

---

## Implementation Plan

### Phase 1: Database Foundation (Week 1-2)

**Migrations to Create:**
1. `remove_species_and_breeds.php` - Drop pet-specific tables
2. `add_human_fields_to_patients.php` - Add human medical fields
3. `rename_clients_to_guardians.php` - Rename throughout
4. `create_insurance_plans_table.php`
5. `create_prescriptions_table.php`
6. `create_vital_signs_table.php`
7. `create_lab_orders_table.php`
8. `create_diagnoses_table.php`
9. `create_immunizations_table.php`
10. `rename_service_provider_columns.php`

**Tasks:**
- [ ] Create all 10 migration files
- [ ] Test migrations in development
- [ ] Verify data integrity
- [ ] Update seeders
- [ ] Test rollback functionality

---

### Phase 2: Model Layer (Week 3-4)

**Models to Delete:**
- `app/Models/Species.php`
- `app/Models/Breed.php`

**Models to Create:**
- `app/Models/Guardian.php` (renamed from Client)
- `app/Models/InsurancePlan.php`
- `app/Models/Prescription.php`
- `app/Models/VitalSigns.php`
- `app/Models/LabOrder.php`
- `app/Models/Diagnosis.php`
- `app/Models/Immunization.php`

**Models to Modify:**
- `app/Models/Patient.php` - Major changes
- `app/Models/Reservation.php` - Update terminology
- `app/Models/MedicalDocument.php` - Add new relationships
- `app/Models/User.php` - Rename service_provider

**Tasks:**
- [ ] Delete species/breed models
- [ ] Create 7 new models with relationships
- [ ] Update Patient model (remove species/breed)
- [ ] Rename Client to Guardian
- [ ] Update all relationships
- [ ] Test model factories

---

### Phase 3: Business Logic (Week 5-6)

**Services to Update:**
- `ReservationService.php`
- `AvailableSlotsService.php`
- `InvoiceService.php`
- `InvoiceCalculator.php`

**Services to Create:**
- `PrescriptionService.php`
- `VitalSignsService.php`
- `LabOrderService.php`

**Tasks:**
- [ ] Update reservation logic
- [ ] Update medical document handling
- [ ] Create prescription workflow
- [ ] Create vitals recording service
- [ ] Update invoice generation
- [ ] Test all service methods

---

### Phase 4: UI Layer - Filament Resources (Week 7-8)

**Resources to Update:**
- `PatientResource.php` - Major overhaul
- `ReservationResource.php` - Update labels
- `MedicalDocumentResource.php` - Add new sections

**Resources to Create:**
- `PrescriptionResource.php`
- `LabOrderResource.php`
- `DiagnosisResource.php`
- `ImmunizationResource.php`
- `VitalSignsResource.php`
- `InsurancePlanResource.php`

**Forms to Update:**
- `PatientForm.php` - Remove species/breed, add human fields
- `ReservationForm.php` - Update terminology
- `MedicalDocumentForm.php` - Add vitals, diagnosis, prescriptions

**Tasks:**
- [ ] Update PatientResource completely
- [ ] Update all forms
- [ ] Update all tables
- [ ] Update all infolists
- [ ] Create 6 new resources
- [ ] Test CRUD operations

---

### Phase 5: Portal Updates (Week 9-10)

**Portal Changes:**
- Rename Client Portal → Guardian Portal
- Update authentication
- Update patient views
- Add privacy controls

**Files to Modify:**
- All files in `app/Filament/Portal/`
- Portal auth views
- Portal dashboard

**Tasks:**
- [ ] Rename portal references
- [ ] Update authentication flow
- [ ] Update patient listing
- [ ] Update appointment requests
- [ ] Add privacy controls
- [ ] Test portal workflow end-to-end

---

### Phase 6: Localization & Cleanup (Week 11-12)

**Croatian Text to Replace:**
- "Račun" → "Invoice"
- "Kupac" → "Customer"
- "Klijent" → "Guardian"
- "Nalaz" → "Medical Record"
- "opasan" → "at-risk"
- "god." → "years old"

**Files to Update:**
- PDF templates
- Email templates
- Language files
- Views

**Tasks:**
- [ ] Extract hardcoded text to language files
- [ ] Translate Croatian to English
- [ ] Update PDF templates
- [ ] Update email templates
- [ ] Update all labels/icons
- [ ] Test localization

---

### Phase 7: Testing & QA (Week 13-14)

**Test Updates:**
- Update all factories with human data
- Rewrite test cases
- Create new test suites

**Tasks:**
- [ ] Update PatientFactory
- [ ] Update all other factories
- [ ] Rewrite seeders
- [ ] Create integration tests
- [ ] Create end-to-end tests
- [ ] Performance testing
- [ ] Security audit (HIPAA compliance)

---

### Phase 8: Documentation & Deployment (Week 15-16)

**Documentation:**
- Update README
- Create new screenshots
- Write admin documentation
- Write user documentation

**Tasks:**
- [ ] Update README.md
- [ ] Create new screenshots
- [ ] Write admin guide
- [ ] Write user guide
- [ ] Write API documentation (if applicable)
- [ ] Deployment preparation
- [ ] Production deployment

---

## Execution Timeline

**Total Duration:** 16 weeks (4 months)

| Week | Phase | Key Deliverables |
|------|-------|------------------|
| 1-2 | Database Foundation | 10 migrations complete |
| 3-4 | Model Layer | 7 new models, 4 updated models |
| 5-6 | Business Logic | 3 new services, 4 updated services |
| 7-8 | UI Layer | 6 new resources, 3 updated resources |
| 9-10 | Portal Updates | Guardian portal complete |
| 11-12 | Localization | All Croatian text removed |
| 13-14 | Testing & QA | Full test coverage |
| 15-16 | Documentation | Production ready |

---

## Risk Assessment

See [RISK_ASSESSMENT.md](./RISK_ASSESSMENT.md) for detailed risk matrix.

**Critical Risks:**
- Patient-Client relationship redesign (architectural)
- HIPAA compliance requirements
- Insurance integration complexity

**High Risks:**
- Species/Breed removal (cascading changes)
- Billing/invoicing complexity
- Portal authentication/privacy

**Medium Risks:**
- Terminology updates
- Croatian localization
- Service catalog replacement

**Low Risks:**
- Test data updates
- Documentation updates
- Screenshot replacement

---

## Next Steps

### Critical Decisions Needed

Before implementation can begin, the following must be decided:

1. **Which architectural model?** (A, B, or C)
   - **Recommendation:** Strategy B (Guardian Model)

2. **Keep Croatian fiscalization or remove?**
   - Current: Croatian tax compliance (ZKI, JIR codes)
   - Decision needed for target deployment region

3. **Target country/region?**
   - Affects compliance requirements (HIPAA, GDPR, etc.)

4. **Insurance module scope?**
   - Basic info only?
   - Full claims processing?

5. **Single-tenant or keep multi-tenant?**
   - Current: Multi-tenant SaaS
   - Stated requirement: Single-tenant

6. **Branding confirmation:**
   - Name: "Coodely Hospital" ✓
   - Logo/colors needed
   - Domain name

### Immediate Actions

1. Review this transformation plan
2. Make architectural decision (recommend B)
3. Approve implementation timeline
4. Begin Phase 1 (Database Foundation)

---

## Appendices

- [Appendix A: Complete File Change Manifest](./FILE_CHANGE_MANIFEST.md)
- [Appendix B: Hidden Assumptions & Edge Cases](./HIDDEN_ASSUMPTIONS.md)
- [Appendix C: Code Transformation Examples](./CODE_TRANSFORMATIONS.md)
- [Appendix D: Architecture Decision Records](./ARCHITECTURE_DECISIONS.md)
- [Appendix E: Risk Assessment Matrix](./RISK_ASSESSMENT.md)

---

**Document Status:** Draft - Awaiting Approval
**Next Review:** Upon architectural decision
**Maintained By:** Development Team
