# Functional Requirements - Coodely Hospital

## 1. Patient Management (FR-PM)

- FR-PM-001: Register patients with demographics (name, DOB, gender, contact)
- FR-PM-002: Store medical history (chronic conditions, surgeries, family history)
- FR-PM-003: Track allergies with severity levels
- FR-PM-004: Record blood type (A+, A-, B+, B-, AB+, AB-, O+, O-)
- FR-PM-005: Upload patient photos
- FR-PM-006: Track insurance information
- FR-PM-007: Maintain immunization records
- FR-PM-008: Flag critical alerts (allergies, DNR, medical warnings)
- FR-PM-009: Soft-delete patients (HIPAA/GDPR compliance)
- FR-PM-010: Global patient search

## 2. Appointment Management (FR-AP)

- FR-AP-001: Schedule appointments with date/time/doctor/department
- FR-AP-002: Support appointment types (Consultation, Follow-up, Emergency, Procedure)
- FR-AP-003: Validate doctor availability
- FR-AP-004: Track status workflow (Scheduled → CheckedIn → InProgress → Completed)
- FR-AP-005: Send appointment reminders (email/SMS)
- FR-AP-006: Allow rescheduling
- FR-AP-007: Support cancellation with reason
- FR-AP-008: Display calendar view
- FR-AP-009: Drag-and-drop scheduling
- FR-AP-010: Support walk-ins
- FR-AP-011: Track appointment duration
- FR-AP-012: Support recurring appointments

## 3. Medical Records (FR-MR)

- FR-MR-001: Create clinical notes (SOAP format)
- FR-MR-002: Record vital signs (BP, HR, temp, weight, height, O2)
- FR-MR-003: Support ICD-10 diagnosis codes
- FR-MR-004: Attach files (lab results, imaging, documents)
- FR-MR-005: Lock records after finalization
- FR-MR-006: Display patient timeline
- FR-MR-007: Support templates
- FR-MR-008: Track who created/modified
- FR-MR-009: E-signature support
- FR-MR-010: Generate PDFs

## 4. Prescription Management (FR-PR)

- FR-PR-001: Create prescriptions linked to visits
- FR-PR-002: Search medications database
- FR-PR-003: Validate drug interactions
- FR-PR-004: Check patient allergies
- FR-PR-005: Support refills
- FR-PR-006: Print/email prescriptions
- FR-PR-007: Track status (Issued → Filled → Completed)
- FR-PR-008: Track controlled substances

## 5. Lab & Diagnostics (FR-LAB)

- FR-LAB-001: Create lab test orders
- FR-LAB-002: Enter results with reference ranges
- FR-LAB-003: Attach lab report files
- FR-LAB-004: Notify doctor when ready
- FR-LAB-005: Track status (Ordered → In Progress → Completed)
- FR-LAB-006: Support common test panels

## 6. Billing & Invoicing (FR-BL)

- FR-BL-001: Generate invoices for services
- FR-BL-002: Support multiple payment methods
- FR-BL-003: Process insurance claims
- FR-BL-004: Calculate co-pay/deductible
- FR-BL-005: Track payment status
- FR-BL-006: Generate receipts
- FR-BL-007: Support payment plans
- FR-BL-008: Integrate fiscalization (if required)
- FR-BL-009: Track outstanding balances
- FR-BL-010: Generate financial reports

## 7. User & Role Management (FR-UR)

- FR-UR-001: Support multiple user roles
- FR-UR-002: Assign permissions per role (RBAC)
- FR-UR-003: Track doctor specializations
- FR-UR-004: Assign doctors to departments
- FR-UR-005: Manage doctor schedules
- FR-UR-006: Track login/activity
- FR-UR-007: Support user avatar
- FR-UR-008: Deactivate users (soft delete)

## 8. Patient Portal (FR-PP)

- FR-PP-001: View medical records
- FR-PP-002: View appointment history
- FR-PP-003: Request appointments online
- FR-PP-004: View invoices/payments
- FR-PP-005: View prescriptions
- FR-PP-006: View lab results
- FR-PP-007: Update contact information
- FR-PP-008: Receive email notifications
- FR-PP-009: Upload documents

## 9. Reporting & Analytics (FR-RP)

- FR-RP-001: Daily appointment reports
- FR-RP-002: Revenue reports
- FR-RP-003: Patient demographics
- FR-RP-004: Common diagnoses
- FR-RP-005: Doctor performance metrics
- FR-RP-006: Export to PDF/Excel

**Total**: 77 Functional Requirements

**Document Version**: 1.0
