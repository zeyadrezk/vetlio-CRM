# Architecture Decision Records

**Vetlio → Coodely Hospital Transformation**
**Version:** 1.0
**Date:** 2025-11-16

---

## ADR-001: Patient-Client Relationship Model

**Status:** Proposed (Awaiting Decision)
**Date:** 2025-11-16
**Decision Maker:** [TBD]

### Context

The veterinary clinic has a fundamental 1:N relationship: one Client (pet owner) owns many Patients (pets). For human healthcare, this relationship must be redesigned. Three viable architectural options exist, each with different trade-offs.

### Decision Options

#### Option A: Patient = Client (1:1 Merge)

**Architecture:**
```
Patient (Single Entity)
├── Authentication fields (email, password)
├── Demographic fields (name, DOB, address)
└── Medical fields (blood type, allergies, etc.)
```

**Pros:**
- Simplest data model
- Fewest tables
- No "owner" confusion
- Direct patient access to records
- Aligns with adult outpatient care

**Cons:**
- Doesn't support pediatrics well
- No guardian/family relationships
- Largest code refactor (80+ files)
- Portal completely restructured
- Billing relationship unclear

**Best For:**
- Adult-only outpatient clinics
- Concierge medicine
- Private practices without pediatrics

**Estimated Effort:** 10-12 weeks

---

#### Option B: Guardian Model (1:N Preserved) ⭐ RECOMMENDED

**Architecture:**
```
Guardian (Renamed from Client)
└── 1:N Relationship
    └── Patient
        ├── Can be minor (managed by guardian)
        └── Can be adult (self-managing flag)
```

**Pros:**
- Minimal structural changes (40 files)
- Supports all patient demographics
- Portal logic remains similar
- Family relationship modeling
- Familiar model (lowest risk)
- Gradual migration possible

**Cons:**
- "Guardian" terminology may confuse adult patients
- Needs special handling for self-managing adults
- Potential for dual authentication (patient + guardian)

**Best For:**
- Family medicine practices
- Pediatrics
- Clinics with mixed demographics
- **This transformation (recommended)**

**Estimated Effort:** 6-8 weeks

**Implementation Details:**

1. **Database Changes:**
   ```sql
   RENAME TABLE clients TO guardians;
   ALTER TABLE patients RENAME COLUMN client_id TO guardian_id;
   ALTER TABLE patients ADD COLUMN is_self_managing BOOLEAN DEFAULT false;
   ALTER TABLE patients ADD COLUMN user_id BIGINT NULL;  -- For adult self-auth
   ```

2. **Model Changes:**
   ```php
   // Client.php → Guardian.php
   class Guardian extends Authenticatable
   {
       public function patients(): HasMany
       {
           return $this->hasMany(Patient::class, 'guardian_id');
       }
   }

   class Patient extends Model
   {
       public function guardian(): BelongsTo
       {
           return $this->belongsTo(Guardian::class);
       }

       public function isSelfManaging(): bool
       {
           return $this->date_of_birth->age >= 18 && $this->is_self_managing;
       }
   }
   ```

3. **Portal Logic:**
   ```php
   // Guardian sees their patients
   $patients = auth()->guard('guardian')->user()->patients;

   // Adult patients can also authenticate
   if ($patient->isSelfManaging()) {
       // Allow direct login
   }
   ```

---

#### Option C: Hybrid Model (Most Flexible)

**Architecture:**
```
Patient (Primary Entity)
├── user_id (optional) - For self-managing adults
├── guardian_id (optional) - For minors/dependents
└── Many:Many
    └── FamilyRelationships
        ├── Related Patient
        ├── Responsible Party
        └── Permissions
```

**Pros:**
- Handles all scenarios
- Most flexible
- Future-proof
- Complex family modeling
- Best long-term solution

**Cons:**
- Most complex (60 files)
- More tables/relationships
- More complex business logic
- Harder to understand/maintain
- Longer development time

**Best For:**
- Full-service hospitals
- Complex family dynamics
- Long-term scalability
- Enterprise systems

**Estimated Effort:** 8-10 weeks

**Implementation Details:**

1. **Database Changes:**
   ```sql
   -- Patients can self-authenticate
   ALTER TABLE patients ADD COLUMN user_id BIGINT NULL;
   ALTER TABLE patients ADD COLUMN email VARCHAR(255) NULL;
   ALTER TABLE patients ADD COLUMN password VARCHAR(255) NULL;

   -- Keep guardian relationship (optional)
   ALTER TABLE patients RENAME COLUMN client_id TO guardian_id;
   ALTER TABLE patients MODIFY guardian_id BIGINT NULL;

   -- Family relationships
   CREATE TABLE family_relationships (
       id BIGINT PRIMARY KEY,
       patient_id BIGINT,
       related_patient_id BIGINT,
       responsible_party_id BIGINT,
       relationship_type VARCHAR(50),
       can_view_records BOOLEAN,
       can_schedule BOOLEAN,
       can_consent BOOLEAN,
       created_at TIMESTAMP,
       updated_at TIMESTAMP
   );
   ```

2. **Authentication:**
   ```php
   // Multiple authentication paths
   config/auth.php:
   'guards' => [
       'patient' => [...],
       'guardian' => [...],
       'user' => [...],  // Staff
   ]
   ```

---

### Comparison Matrix

| Criteria | Option A (1:1) | Option B (Guardian) | Option C (Hybrid) |
|----------|----------------|---------------------|-------------------|
| **Simplicity** | ★★★★★ | ★★★★☆ | ★★☆☆☆ |
| **Pediatric Support** | ★☆☆☆☆ | ★★★★★ | ★★★★★ |
| **Adult Support** | ★★★★★ | ★★★☆☆ | ★★★★★ |
| **Family Relationships** | ★☆☆☆☆ | ★★★☆☆ | ★★★★★ |
| **Code Changes** | ★☆☆☆☆ | ★★★★★ | ★★★☆☆ |
| **Development Time** | ★☆☆☆☆ | ★★★★★ | ★★★☆☆ |
| **Flexibility** | ★★☆☆☆ | ★★★☆☆ | ★★★★★ |
| **Maintenance** | ★★★★★ | ★★★★☆ | ★★★☆☆ |
| **Future-Proof** | ★★☆☆☆ | ★★★☆☆ | ★★★★★ |
| **Migration Risk** | ★★☆☆☆ | ★★★★★ | ★★★☆☆ |

---

### Recommendation

**Option B: Guardian Model**

**Rationale:**
1. **Minimal Risk:** Preserves existing architecture, lowest migration risk
2. **Adequate Support:** Handles both pediatric and adult patients with minor additions
3. **Fastest to Market:** 6-8 weeks vs. 10-12 weeks (Option A) or 8-10 weeks (Option C)
4. **Familiar Pattern:** Development team already understands 1:N relationships
5. **Extensible:** Can evolve to Option C later if needed

**Implementation Path:**
- Phase 1: Rename Client → Guardian, update terminology
- Phase 2: Add self-managing flag for adult patients
- Phase 3: Add patient self-authentication (optional)
- Phase 4: Extend with family relationships if needed

**Migration to Other Options:**
- To Option A: Merge guardian fields into patient, drop guardian table
- To Option C: Add family relationships table, enable patient authentication

---

## ADR-002: Multi-Tenant vs. Single-Tenant

**Status:** Proposed
**Date:** 2025-11-16
**Decision Maker:** [TBD]

### Context

Current codebase is multi-tenant SaaS (multiple organizations, each with branches). User requested "single-tenant" human hospital.

### Current Architecture

```
Organisation (Tenant)
└── Branches
    └── Users, Patients, Reservations, etc.
```

- Tenant identification via subdomain
- Data scoped to organisation_id
- Multi-tenancy via `Organisationable` trait

### Decision Options

#### Option A: Remove Multi-Tenancy

**Changes:**
- Remove organisation_id from all tables
- Remove Organisationable trait
- Simplify queries (no scoping)
- Single database, single organization

**Pros:**
- Simpler codebase
- Better performance
- Easier to understand
- No tenant isolation complexity

**Cons:**
- Cannot expand to multi-tenant later (major rework)
- Loses valuable SaaS architecture
- Limited scalability

**Effort:** 4-6 weeks

---

#### Option B: Keep Multi-Tenancy (Default Single) ⭐ RECOMMENDED

**Changes:**
- Keep multi-tenant architecture
- Deploy as single organization
- Disable organization creation
- Hide organization selection from UI

**Pros:**
- Keep valuable architecture
- Can expand to multi-location later
- Branches still useful (multiple hospital locations)
- Minimal code changes
- Future-proof

**Cons:**
- Slightly more complex than necessary
- Organization_id still in database (but unused)

**Effort:** 1-2 days

---

### Recommendation

**Option B: Keep Multi-Tenancy (Single-Tenant Deployment)**

**Rationale:**
1. Hospital may have multiple locations (branches useful)
2. Architecture is already built
3. Minimal effort to deploy as single-tenant
4. Future expansion possible
5. No downside to keeping it

**Implementation:**
```php
// config/app.php
'force_single_tenant' => true,

// In providers
if (config('app.force_single_tenant')) {
    $defaultOrganisation = Organisation::first();
    // Set as global default
}

// Hide from UI
if (!config('app.force_single_tenant')) {
    // Show organization switcher
}
```

---

## ADR-003: Croatian Fiscalization

**Status:** Proposed
**Date:** 2025-11-16
**Decision Maker:** [TBD]

### Context

Current system has Croatian tax fiscalization (ZKI, JIR codes, QR codes). Need to decide: keep, modify, or remove.

### Decision Options

#### Option A: Remove (Non-Croatia Deployment)

**If deploying outside Croatia:**
- Remove fiscalization columns
- Remove FiscalisationService
- Remove `nticaric/fiskalizacija` package
- Update invoice PDFs

**Effort:** 1-2 days

---

#### Option B: Keep & Update (Croatia Deployment)

**If deploying in Croatia:**
- Keep fiscalization
- May need updates for healthcare billing
- Different tax rates for medical services
- Integrate with insurance

**Effort:** 1-2 weeks

---

#### Option C: Replace (Other Country)

**If deploying in US:**
- Remove Croatian fiscalization
- Add insurance claim submission (EDI)
- Much more complex

**Effort:** See RISK-003 (12-16 weeks)

---

### Recommendation

**Pending:** Need to know target deployment region.

**If unknown:** Keep Croatian fiscalization, can remove later (1-2 days work).

---

## ADR-004: Insurance Module Scope

**Status:** Proposed
**Date:** 2025-11-16
**Decision Maker:** [TBD]

### Context

Human healthcare typically involves insurance billing. Three levels of implementation possible.

### Decision Options

#### Option A: No Insurance (Cash-Only Practice)

**Implementation:**
- No insurance tables
- Simple invoicing (like current)
- Patient pays full amount

**Pros:**
- Simplest
- Fastest to implement
- No insurance complexity

**Cons:**
- Limits patient base
- Not typical for most practices

**Effort:** 0 weeks (no changes)

---

#### Option B: Basic Insurance Info Only ⭐ RECOMMENDED FOR MVP

**Implementation:**
- Store insurance plan details
- Manual claim submission
- Track patient responsibility
- Basic insurance information

**Pros:**
- Moderate complexity
- Useful for patient records
- Allows insurance verification
- No EDI integration needed

**Cons:**
- No automated claim submission
- Manual billing workflow

**Effort:** 2-3 weeks

---

#### Option C: Full Revenue Cycle Management

**Implementation:**
- Electronic claim submission (EDI 837)
- Eligibility verification
- Pre-authorization tracking
- Automated posting (EDI 835)
- Denial management
- Patient statements

**Pros:**
- Complete solution
- Automated workflows
- Professional billing

**Cons:**
- Very complex (12-16 weeks)
- Expensive (clearinghouse fees)
- Requires billing expertise

**Effort:** 12-16 weeks

---

### Recommendation

**Phase 1: Option B (Basic Insurance Info)**
**Phase 2: Option C (Full RCM) - If needed**

**Rationale:**
- Get to market faster
- Validate product-market fit
- Add full billing later if demand exists
- Can outsource billing initially

---

## ADR-005: Medical Documentation Standard

**Status:** Proposed
**Date:** 2025-11-16

### Context

Current medical documents are free-text. Human healthcare benefits from structured documentation.

### Decision Options

#### Option A: Keep Free-Text

**Implementation:**
- Simple text area
- Unstructured notes

**Pros:**
- Flexible
- Fast to document
- No template constraints

**Cons:**
- Inconsistent documentation
- Hard to extract data
- Not billable
- No structured diagnosis

---

#### Option B: Semi-Structured (SOAP) ⭐ RECOMMENDED

**Implementation:**
- Subjective (chief complaint, HPI)
- Objective (vitals, exam)
- Assessment (diagnosis with ICD-10)
- Plan (treatment, prescriptions)

**Pros:**
- Standardized format
- Supports billing
- Structured data extraction
- Industry standard

**Cons:**
- Requires fields/templates
- Slightly more complex

**Effort:** 2-3 weeks

---

#### Option C: Fully Template-Driven

**Implementation:**
- Specialty-specific templates
- Structured forms
- Checkboxes/dropdowns
- Minimal free-text

**Pros:**
- Highly structured
- Easy data extraction
- Quality assurance
- Billing optimization

**Cons:**
- Rigid
- Time-consuming to document
- Many templates needed

**Effort:** 6-8 weeks

---

### Recommendation

**Option B: Semi-Structured (SOAP)**

**Rationale:**
- Balance of structure and flexibility
- Industry standard
- Supports billing
- Reasonable implementation effort

---

## ADR-006: Authentication Strategy

**Status:** Proposed
**Date:** 2025-11-16

### Context

Need to determine authentication requirements for portal.

### Decision Options

#### Option A: Simple Username/Password

**Current state:**
- Email and password
- No 2FA
- No special security

**Pros:**
- Simple
- Easy to use

**Cons:**
- Not secure enough for PHI
- HIPAA recommends 2FA

---

#### Option B: Username/Password + Optional 2FA

**Implementation:**
- Email/password login
- Optional 2FA (user can enable)
- SMS or authenticator app

**Pros:**
- Secure
- User choice
- Industry standard

**Cons:**
- Slightly more complex

**Effort:** 1-2 weeks

---

#### Option C: Mandatory 2FA + Identity Verification ⭐ RECOMMENDED

**Implementation:**
- Email/password login
- Mandatory 2FA
- Identity verification at signup
- Regular re-verification

**Pros:**
- Most secure
- HIPAA compliant
- Best practice

**Cons:**
- User friction
- Support burden

**Effort:** 2-3 weeks

---

### Recommendation

**Option C: Mandatory 2FA**

**Rationale:**
- HIPAA best practice
- Industry trend
- Protects patient data
- Worth the effort

---

## Summary of Recommendations

| ADR | Decision | Rationale | Effort |
|-----|----------|-----------|--------|
| ADR-001 | **Option B: Guardian Model** | Lowest risk, supports all patients | 6-8 weeks |
| ADR-002 | **Option B: Keep Multi-Tenancy** | Future-proof, minimal effort | 1-2 days |
| ADR-003 | **Pending** | Need deployment region | TBD |
| ADR-004 | **Option B: Basic Insurance** | MVP approach, iterate later | 2-3 weeks |
| ADR-005 | **Option B: SOAP Format** | Standard, structured, billable | 2-3 weeks |
| ADR-006 | **Option C: Mandatory 2FA** | Security best practice | 2-3 weeks |

**Total Estimated Effort:** 13-19 weeks (3-5 months)

---

## Decision Log

| Date | ADR | Status | Decided By | Notes |
|------|-----|--------|------------|-------|
| 2025-11-16 | All | Proposed | - | Awaiting stakeholder review |

---

**Document Status:** Complete
**Last Updated:** 2025-11-16
**Maintained By:** Development Team
**Next Action:** Review with stakeholders and make final decisions
