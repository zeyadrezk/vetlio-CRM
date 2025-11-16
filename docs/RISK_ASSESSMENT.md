# Risk Assessment Matrix

**Vetlio → Coodely Hospital Transformation**
**Version:** 1.0
**Date:** 2025-11-16

---

## Risk Rating Scale

**Severity:**
- **Critical:** System-breaking, legal/compliance violation, data loss
- **High:** Major functionality impacted, significant rework needed
- **Medium:** Important feature affected, workarounds possible
- **Low:** Minor inconvenience, cosmetic issues

**Complexity:**
- **Very High:** 50+ files, architectural changes, 3+ weeks
- **High:** 20-50 files, major refactoring, 1-3 weeks
- **Medium:** 10-20 files, moderate changes, 3-7 days
- **Low:** <10 files, simple changes, 1-2 days

**Probability:**
- **Certain:** Will definitely occur (90-100%)
- **Likely:** Probably will occur (60-89%)
- **Possible:** May occur (30-59%)
- **Unlikely:** Probably won't occur (10-29%)
- **Rare:** Very unlikely (<10%)

---

## Critical Risks

### RISK-001: Patient-Client Relationship Redesign

**Category:** Architecture
**Severity:** Critical
**Complexity:** Very High
**Probability:** Certain

**Description:**
The fundamental 1:N relationship between Client (pet owner) and Patient (pet) needs to be redesigned for human healthcare. This is the most significant architectural change in the transformation.

**Impact:**
- 50+ files affected
- Database schema changes
- All relationship queries need updating
- Portal authentication model changes
- Appointment booking workflow changes
- Billing relationship changes

**Cascading Effects:**
- Reservation forms
- Medical document associations
- Invoice generation
- Portal navigation
- Search functionality
- Reporting

**Mitigation Strategy:**
1. **Decide on architectural model early** (Options A, B, or C)
2. **Recommendation:** Use Strategy B (Guardian Model) for minimal disruption
3. Create comprehensive migration script
4. Test thoroughly in staging
5. Plan for data migration if existing data
6. Update all foreign key relationships
7. Test all CRUD operations
8. Regression test entire system

**Dependencies:**
- All other transformations depend on this decision
- Must complete before moving to Phase 2

**Estimated Effort:** 3-4 weeks

---

### RISK-002: HIPAA Compliance Requirements

**Category:** Legal/Compliance
**Severity:** Critical
**Complexity:** High
**Probability:** Certain (if deploying in US)

**Description:**
Human healthcare in the US requires strict HIPAA compliance. Current system has no HIPAA controls.

**Required Changes:**
- Multi-factor authentication
- Comprehensive audit logging
- Data encryption (at rest and in transit)
- Business Associate Agreements (BAA) tracking
- Breach notification procedures
- Patient consent management
- Access controls (role-based, minimum necessary)
- Secure communication channels
- Data backup and recovery
- Incident response plan

**Impact:**
- Security architecture overhaul
- Authentication system upgrade
- Authorization system implementation
- Audit logging throughout system
- Data encryption implementation
- Privacy controls in UI
- Staff training requirements
- Legal documentation

**Mitigation Strategy:**
1. Conduct HIPAA gap analysis
2. Implement PHI encryption
3. Add comprehensive audit logging
4. Implement role-based access control (RBAC)
5. Add 2FA to authentication
6. Implement consent management
7. Add privacy notices
8. Create breach response plan
9. Conduct security assessment
10. Get HIPAA compliance certification

**Dependencies:**
- Portal authentication (RISK-007)
- Calendar privacy (RISK-008)
- Medical record immutability (RISK-009)

**Estimated Effort:** 6-8 weeks

**Cost:** Potential compliance consultant: $10,000-$50,000

---

### RISK-003: Insurance Integration Complexity

**Category:** Feature Development
**Severity:** Critical
**Complexity:** Very High
**Probability:** Likely

**Description:**
Human healthcare billing is exponentially more complex than veterinary billing due to insurance integration requirements.

**Required Components:**
- Insurance eligibility verification
- Pre-authorization tracking
- Claim submission (EDI 837)
- Claim status checking
- Payment posting (EDI 835)
- Denial management
- Patient statement generation
- Coordination of benefits (COB)
- Secondary insurance billing
- Medicare/Medicaid compliance
- ICD-10 diagnosis coding
- CPT procedure coding

**Impact:**
- Entire billing module needs expansion
- Integration with clearinghouses
- EDI transaction implementation
- Insurance plan management
- Complex calculation logic
- Staff training on insurance processes

**Mitigation Strategy:**
1. **Phase 1:** Basic insurance information only
   - Store insurance plan details
   - Manual claim submission
   - Payment tracking

2. **Phase 2:** Electronic claim submission
   - Integrate with clearinghouse (Change Healthcare, Availity, etc.)
   - Implement EDI 837 generation
   - Basic claim tracking

3. **Phase 3:** Full revenue cycle management
   - Eligibility verification
   - Pre-authorization management
   - Automated posting
   - Denial workflows
   - Patient statements

**Alternative:** Use third-party billing service

**Dependencies:**
- Service catalog with CPT codes (RISK-010)
- Diagnosis coding system
- Price list management

**Estimated Effort:**
- Phase 1: 2-3 weeks
- Phase 2: 6-8 weeks
- Phase 3: 12-16 weeks

**Cost:**
- Clearinghouse fees: $100-$500/month
- Development: Significant
- Alternative (outsource billing): $3-$8 per claim

---

## High Risks

### RISK-004: Species/Breed Removal Cascading Changes

**Category:** Data Model
**Severity:** High
**Complexity:** Medium
**Probability:** Certain

**Description:**
Removing `species` and `breeds` tables affects 24 files with foreign key relationships, queries, forms, and display logic.

**Affected Components:**
- Patient model (relationships)
- Patient forms (fields)
- Patient tables (columns)
- Patient search (filters)
- Patient display (descriptions)
- Reservation forms
- Medical document displays
- Portal patient views
- Factories (test data)
- Seeders
- Global search

**Migration Path:**
1. Identify all foreign key constraints
2. Update all queries to remove joins
3. Remove form fields
4. Update display methods
5. Update search/filters
6. Update factories
7. Delete seeders
8. Drop tables
9. Test thoroughly

**Mitigation Strategy:**
- Create comprehensive test suite before changes
- Use database transaction for migration
- Have rollback plan
- Test in development first
- Use find/replace carefully
- Verify all references removed

**Dependencies:**
- Must complete after deciding on patient model

**Estimated Effort:** 1-2 weeks

---

### RISK-005: Billing/Invoicing Complexity

**Category:** Feature Development
**Severity:** High
**Complexity:** Very High
**Probability:** Likely

**Description:**
Current invoicing is simple. Human healthcare billing requires:
- Multiple insurance payers
- Primary/secondary/tertiary insurance
- Patient responsibility calculation
- Copays, deductibles, coinsurance
- Allowed amounts vs. billed amounts
- Adjustment codes
- Payment posting
- Credit balances
- Refund processing

**Current State:**
```php
// Simple invoice: Service price × Quantity + Tax = Total
$invoice->total = $subtotal + $tax;
```

**Required State:**
```php
// Complex:
$billed_amount = $service->price;
$allowed_amount = $insurance->contract_rate;
$adjustment = $billed_amount - $allowed_amount;
$insurance_payment = $allowed_amount * $insurance->coinsurance_pct;
$patient_copay = $insurance->copay;
$patient_coinsurance = $allowed_amount * (1 - $insurance->coinsurance_pct);
$patient_deductible = $remaining_deductible;
$patient_responsibility = $patient_copay + $patient_coinsurance + $patient_deductible;
$total_payments = $insurance_payment + $patient_payments;
$balance = $billed_amount - $total_payments - $adjustments;
```

**Impact:**
- Invoice model expansion
- Invoice calculator rewrite
- Payment posting complexity
- Reporting complexity
- Reconciliation processes

**Mitigation Strategy:**
1. Phase implementation (simple → complex)
2. Consider third-party billing software integration
3. Hire billing specialist consultant
4. Use existing billing libraries if available
5. Extensive testing with various scenarios

**Dependencies:**
- Insurance integration (RISK-003)
- Service catalog with CPT codes

**Estimated Effort:** 8-12 weeks for full implementation

---

### RISK-006: Portal Authentication & Privacy

**Category:** Security/Privacy
**Severity:** High
**Complexity:** High
**Probability:** Certain

**Description:**
Current portal has simple authentication and no privacy controls. Human healthcare requires much stronger security and privacy.

**Current Gaps:**
- No multi-factor authentication
- No audit logging
- No session management
- No role-based access for portal
- No consent management
- All guardians see all patient data

**Required:**
- 2FA implementation
- Audit logging
- Strong password requirements
- Session timeouts
- Patient consent for guardian access
- Granular privacy controls
- Emergency access logging

**Impact:**
- Authentication system upgrade
- Portal UI changes
- Database schema additions
- Consent workflow
- Audit logging infrastructure

**Mitigation Strategy:**
1. Implement 2FA using Laravel Fortify or Filament Breezy
2. Add audit logging package (spatie/laravel-activitylog)
3. Implement consent management
4. Add privacy controls
5. Test thoroughly
6. Document for users

**Dependencies:**
- HIPAA compliance (RISK-002)
- Patient-client relationship model

**Estimated Effort:** 3-4 weeks

---

### RISK-007: Service Catalog Replacement

**Category:** Data Migration
**Severity:** High
**Complexity:** High
**Probability:** Certain

**Description:**
Current service catalog is veterinary-specific. Need to replace with human medical procedures.

**Current Services:**
- Vaccination (pets)
- Grooming
- Neutering/Spaying
- Dental cleaning
- Microchipping
- etc.

**New Services Needed:**
- Office visits (E&M codes)
- Diagnostic tests
- Laboratory tests
- Procedures
- Immunizations (human)
- Physical therapy
- etc.

**Complexity Factors:**
- Thousands of potential CPT codes
- Different categories
- Different pricing models
- Insurance contract rates
- Modifier codes
- Bundling rules

**Impact:**
- Service model expansion
- Service groups restructure
- Pricing model complexity
- Forms update (service selection)
- Scheduling logic (different durations)
- Billing integration

**Mitigation Strategy:**
1. Start with basic service set (20-30 common services)
2. Add CPT code field to services table
3. Create service categories
4. Import from standard CPT code database
5. Allow custom services
6. Expand gradually

**Dependencies:**
- Billing system (RISK-005)
- Reservation forms

**Estimated Effort:** 2-3 weeks initial, ongoing maintenance

---

## Medium Risks

### RISK-008: Croatian Localization Removal

**Category:** Localization
**Severity:** Medium
**Complexity:** Medium
**Probability:** Certain

**Description:**
Hardcoded Croatian text throughout codebase needs translation and extraction to language files.

**Affected Areas:**
- Patient model (`relatedLabel()`)
- Client model (`relatedLabel()`)
- PDF templates (invoice)
- Views
- Error messages

**Croatian Terms Found:**
- "Klijent" → "Client/Guardian"
- "Nalaz" → "Medical Record"
- "opasan" → "dangerous/at-risk"
- "god." → "years old"
- "Račun" → "Invoice"
- "Kupac" → "Customer"
- etc.

**Migration Strategy:**
1. Extract all hardcoded Croatian text
2. Move to language files (`lang/hr/`)
3. Create English equivalents
4. Use `__()` helper for all text
5. Test language switching
6. Update PDF templates
7. Update email templates

**Mitigation Strategy:**
- Use global find/replace carefully
- Test all PDFs after changes
- Verify email templates render correctly
- Check for encoding issues

**Dependencies:**
- None (can be done independently)

**Estimated Effort:** 1-2 weeks

---

### RISK-009: Medical Record Immutability

**Category:** Legal/Compliance
**Severity:** Medium
**Complexity:** Medium
**Probability:** Likely

**Description:**
Current system allows editing medical documents. Human healthcare requires immutability with addendum system.

**Current Behavior:**
- Documents can be locked
- But can be edited before locking
- No version history
- No audit trail of changes

**Required Behavior:**
- Documents are finalized (not just locked)
- Cannot be edited after finalization
- Changes must be added as addendums
- Addendums are timestamped and attributed
- All changes logged
- Original preserved

**Impact:**
- MedicalDocument model changes
- New MedicalDocumentAddendum model
- Form behavior changes
- UI for adding addendums
- Display of addendums
- PDF generation includes addendums

**Mitigation Strategy:**
1. Add finalization feature
2. Create addendum system
3. Prevent edits after finalization
4. Add audit logging
5. Update UI with clear workflow
6. Train users on new process

**Dependencies:**
- Audit logging system
- Electronic signature implementation

**Estimated Effort:** 2-3 weeks

---

### RISK-010: Terminology Update Across Codebase

**Category:** Refactoring
**Severity:** Medium
**Complexity:** Low
**Probability:** Certain

**Description:**
"Veterinarian" → "Doctor", "Owner" → "Guardian", "service_provider" → "attending_physician" across 34+ files.

**Impact:**
- 34+ files need updating
- Database columns renamed
- Forms updated
- Views updated
- Language files updated

**Risk:**
- Missing some references
- Breaking existing queries
- Database migration errors

**Mitigation Strategy:**
1. Use global search to find all references
2. Create comprehensive list
3. Update database columns first
4. Update model references
5. Update form labels
6. Update views
7. Test all affected pages
8. Run full test suite

**Tools:**
- IDE global find/replace
- Database migration
- Regression testing

**Dependencies:**
- None (cosmetic changes mostly)

**Estimated Effort:** 1 week

---

### RISK-011: Fiscal

ization (Croatian Tax)

**Category:** Compliance/Regional
**Severity:** Medium
**Complexity:** Medium
**Probability:** Possible

**Description:**
Current system has Croatian fiscalization. Decision needed: keep, modify, or remove.

**If Removing (deploying outside Croatia):**
- Drop fiscalization columns
- Remove service
- Remove package dependency
- Update invoice PDFs
- Simple task

**If Keeping (Croatia deployment):**
- May need updates for healthcare billing
- Different tax treatment for medical services
- Integration with insurance
- More complex

**If Replacing (US deployment):**
- Need different compliance (possibly none for invoicing)
- But need insurance claim submission (much more complex)

**Mitigation Strategy:**
- **Decision needed:** Target deployment region?
- If removing: Simple cleanup
- If keeping: May need specialist consultant
- If replacing: See RISK-003 (Insurance Integration)

**Dependencies:**
- Invoice module
- Deployment strategy

**Estimated Effort:**
- Remove: 1-2 days
- Keep: 1-2 weeks (if changes needed)
- Replace: See RISK-003

---

## Low Risks

### RISK-012: Test Data & Factories

**Category:** Testing
**Severity:** Low
**Complexity:** Medium
**Probability:** Certain

**Description:**
All factories generate pet-specific data. Need to update with human data.

**Affected Factories:**
- PatientFactory (major changes)
- ClientFactory → GuardianFactory
- ReservationFactory
- MedicalDocumentFactory
- Others

**Impact:**
- Test data generation
- Seeding for development
- Demo data quality

**Mitigation Strategy:**
1. Update PatientFactory first (biggest change)
2. Remove species/breed generation
3. Add human-specific data (blood type, SSN, insurance)
4. Use Faker for realistic human names, addresses
5. Update demo patient
6. Test seeding
7. Update other factories
8. Create new factories for new models

**Dependencies:**
- Patient model changes must be complete

**Estimated Effort:** 3-5 days

---

### RISK-013: Documentation & Screenshots

**Category:** Documentation
**Severity:** Low
**Complexity:** Low
**Probability:** Certain

**Description:**
All documentation and screenshots reference Vetlio pet clinic. Need complete replacement.

**Affected Files:**
- README.md
- All screenshots (15 files)
- docs/
- Logo
- Branding

**Impact:**
- User confusion
- Unprofessional appearance
- Marketing materials

**Mitigation Strategy:**
1. Update README.md
2. Replace logo
3. Take new screenshots after UI changes
4. Write new documentation
5. Update branding
6. Update domain references

**Dependencies:**
- All UI changes complete before screenshots
- Branding decision (logo, colors)

**Estimated Effort:** 3-5 days

---

### RISK-014: Configuration & Environment

**Category:** Configuration
**Severity:** Low
**Complexity:** Low
**Probability:** Certain

**Description:**
Configuration files reference Vetlio throughout.

**Affected Files:**
- .env.example
- config/app.php
- composer.json
- package.json

**Changes Needed:**
- DB_DATABASE=vetlio → coodely_hospital
- APP_NAME
- Domain references
- Email addresses

**Mitigation Strategy:**
- Update configuration files
- Test after changes
- Update deployment scripts
- Update CI/CD if exists

**Dependencies:**
- None

**Estimated Effort:** 1-2 hours

---

## Risk Summary Matrix

| Risk ID | Name | Severity | Complexity | Probability | Effort | Priority |
|---------|------|----------|------------|-------------|--------|----------|
| RISK-001 | Patient-Client Relationship | Critical | Very High | Certain | 3-4w | 1 |
| RISK-002 | HIPAA Compliance | Critical | High | Certain | 6-8w | 2 |
| RISK-003 | Insurance Integration | Critical | Very High | Likely | 12-16w | 3 |
| RISK-004 | Species/Breed Removal | High | Medium | Certain | 1-2w | 4 |
| RISK-005 | Billing Complexity | High | Very High | Likely | 8-12w | 5 |
| RISK-006 | Portal Auth/Privacy | High | High | Certain | 3-4w | 6 |
| RISK-007 | Service Catalog | High | High | Certain | 2-3w | 7 |
| RISK-008 | Croatian Localization | Medium | Medium | Certain | 1-2w | 8 |
| RISK-009 | Medical Record Immutability | Medium | Medium | Likely | 2-3w | 9 |
| RISK-010 | Terminology Updates | Medium | Low | Certain | 1w | 10 |
| RISK-011 | Fiscalization | Medium | Medium | Possible | 1-2w | 11 |
| RISK-012 | Test Data/Factories | Low | Medium | Certain | 3-5d | 12 |
| RISK-013 | Documentation | Low | Low | Certain | 3-5d | 13 |
| RISK-014 | Configuration | Low | Low | Certain | 1-2h | 14 |

---

## Risk Mitigation Timeline

### Phase 1: Foundation (Weeks 1-4)
**Address:**
- RISK-001 (Patient-Client - MUST DECIDE FIRST)
- RISK-004 (Species/Breed removal)
- RISK-014 (Configuration)

### Phase 2: Core Features (Weeks 5-12)
**Address:**
- RISK-007 (Service Catalog)
- RISK-009 (Medical Records)
- RISK-010 (Terminology)
- RISK-008 (Localization)

### Phase 3: Security & Compliance (Weeks 13-20)
**Address:**
- RISK-006 (Portal Auth)
- RISK-002 (HIPAA)

### Phase 4: Advanced Features (Weeks 21-36)
**Address:**
- RISK-005 (Billing)
- RISK-003 (Insurance)
- RISK-011 (Fiscalization decision)

### Phase 5: Polish (Weeks 37-40)
**Address:**
- RISK-012 (Test Data)
- RISK-013 (Documentation)

---

## Contingency Plans

### If RISK-001 Blocked (Patient-Client Architecture)
- **Blocker:** Cannot decide on model
- **Contingency:** Implement Strategy B (Guardian) as default, design for easy migration to A or C later
- **Impact:** 1-week delay

### If RISK-002 Blocked (HIPAA)
- **Blocker:** Too complex/expensive
- **Contingency:** Deploy outside US, or as non-HIPAA compliant system (not recommended)
- **Impact:** Legal liability

### If RISK-003 Blocked (Insurance)
- **Blocker:** Too complex
- **Contingency:**
  - Option 1: Outsource billing to third-party
  - Option 2: Implement basic insurance info only, manual claims
  - Option 3: Cash-only practice (simplest)
- **Impact:** Revenue cycle complexity

### If Budget Overrun
- **Mitigation:**
  - Phase implementation
  - Remove nice-to-have features
  - Focus on MVP (Minimum Viable Product)
  - Consider contractors for specialized work

### If Timeline Overrun
- **Mitigation:**
  - Extend Phase 4 (Insurance can be added later)
  - Launch with manual billing
  - Progressive enhancement

---

## Monitoring & Review

**Review Frequency:** Weekly during active development

**Key Metrics:**
- Risks closed vs. new risks identified
- Effort actual vs. estimated
- Budget spent vs. remaining
- Timeline adherence
- Test coverage
- Bug count

**Escalation:**
- Medium risks not resolved in 1 week → Escalate
- High risks not resolved in 2 weeks → Escalate
- Critical risks not resolved in 1 week → IMMEDIATE escalation

---

**Document Status:** Complete
**Last Updated:** 2025-11-16
**Maintained By:** Development Team
**Next Review:** Upon project start
