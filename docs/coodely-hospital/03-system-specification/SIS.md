# 📄 System Information Specification (SIS)
## Coodely Hospital Management System

**Version**: 1.0.0
**Project**: Healthcare Management System
**Base System**: Vetlio CRM
**Date**: November 2025

---

## 1. SYSTEM OVERVIEW

**Project Name**: Coodely Hospital Management System
**Target**: Single-tenant healthcare management (Phase 1), Multi-tenant SaaS (Future)
**Tech Stack**: Laravel 12, Filament 4.1, MySQL, Redis, Livewire

### Purpose
Transform Vetlio veterinary CRM into a comprehensive human healthcare management system for hospitals and clinics.

### Stakeholders
1. **Hospital Administrator** - Full system access
2. **Doctor/Physician** - Medical records, prescriptions, appointments
3. **Nurse** - Vital signs, appointment assistance
4. **Receptionist** - Scheduling, patient intake
5. **Lab Technician** - Lab results
6. **Pharmacist** - Prescriptions
7. **Patient** - Portal access

---

## 2. FUNCTIONAL REQUIREMENTS

### 2.1 Patient Management (10 requirements)

| ID | Requirement | Priority |
|----|-------------|----------|
| FR-PM-001 | Register new patients with demographics | Critical |
| FR-PM-002 | Store patient medical history | Critical |
| FR-PM-003 | Track patient allergies with severity | Critical |
| FR-PM-004 | Record patient blood type | High |
| FR-PM-005 | Support patient photo upload | Medium |
| FR-PM-006 | Track patient insurance information | High |
| FR-PM-007 | Maintain immunization records | High |
| FR-PM-008 | Flag critical patient alerts | Critical |
| FR-PM-009 | Soft-delete patients (archive) | High |
| FR-PM-010 | Search patients globally | Critical |

**See**: [Functional Requirements](./functional-requirements.md) for complete list (77 requirements across 9 modules)

---

## 3. NON-FUNCTIONAL REQUIREMENTS

### 3.1 Performance
- Page load: < 2 seconds (95th percentile)
- API response: < 500ms
- Support 100+ concurrent users

### 3.2 Security
- AES-256 encryption at rest
- TLS 1.3 in transit
- Bcrypt password hashing
- Role-based access control
- Audit logging

### 3.3 Compliance
- HIPAA compliant (if US)
- GDPR compliant (if EU)
- Data retention policies
- Right to be forgotten

**See**: [Non-Functional Requirements](./non-functional-requirements.md) for complete specifications

---

## 4. DATA MODELS

### Core Entities
- **patients** - Human patients (formerly clients + patients)
- **appointments** - Scheduled visits (formerly reservations)
- **medical_records** - Clinical notes (formerly medical_documents)
- **users** - Staff (doctors, nurses, etc.)
- **departments** - Hospital departments (formerly branches)
- **hospitals** - Organizations (formerly organisations)

### New Healthcare Entities
- **vital_signs** - BP, heart rate, temperature, etc.
- **prescriptions** - Medication orders
- **lab_tests** & **lab_results** - Diagnostic tests
- **diagnoses** - ICD-10 coded diagnoses
- **allergies** - Patient allergies
- **immunizations** - Vaccination records
- **insurance_providers** - Insurance information

**See**: [Data Models](./data-models.md) for complete schema

---

## 5. USER ROLES & PERMISSIONS

| Role | Key Permissions |
|------|----------------|
| **Admin** | Full system access, settings management |
| **Doctor** | Create/view medical records, prescriptions, diagnoses |
| **Nurse** | Record vital signs, view medical records (read-only) |
| **Receptionist** | Schedule appointments, patient registration |
| **Lab Tech** | Enter lab results, view lab orders |
| **Pharmacist** | View/fulfill prescriptions |
| **Patient** | View own records via portal (read-only) |

---

## 6. SYSTEM CONSTRAINTS

### Technical Constraints
- PHP 8.3+
- MySQL 8.0+ or MariaDB 10.6+
- Modern browser (Chrome, Firefox, Safari, Edge - latest 2 versions)
- Minimum server: 4GB RAM, 2 CPU cores

### Business Constraints
- Single hospital/clinic in Phase 1
- English language initially
- No real-time messaging (Phase 1)
- No mobile app (Phase 1)

---

## 7. ASSUMPTIONS

1. Users have stable internet connection
2. Medical staff are computer literate
3. Patients have email for portal access
4. Hospital has existing backup infrastructure
5. Compliance requirements will be region-specific

---

## 8. DEPENDENCIES

- Laravel 12
- Filament 4.1
- Spatie Media Library (file management)
- Spatie Laravel-PDF (PDF generation)
- Redis (caching, queues)
- Email service (SMTP/SendGrid)

---

## 9. SUCCESS CRITERIA

- ✅ All 77 functional requirements implemented
- ✅ 90%+ automated test coverage
- ✅ < 2 second page load time
- ✅ Zero critical security vulnerabilities
- ✅ HIPAA/GDPR compliance features complete
- ✅ User acceptance testing passed
- ✅ Complete documentation

---

**For detailed specifications, see**:
- [Functional Requirements](./functional-requirements.md) - All 77 requirements
- [Non-Functional Requirements](./non-functional-requirements.md) - Security, performance, compliance
- [Data Models](./data-models.md) - Complete database schema

**Document Version**: 1.0
**Last Updated**: November 2025
