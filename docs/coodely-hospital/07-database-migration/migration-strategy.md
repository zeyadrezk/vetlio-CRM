# 🔄 Database Migration Strategy

## Overview

Transform Vetlio veterinary database schema to Coodely Hospital healthcare schema while preserving data integrity.

---

## Migration Sequence

### Step 1: Backup
```bash
mysqldump -u root -p vetlio > vetlio_backup_$(date +%Y%m%d_%H%M%S).sql
php artisan db:backup
```

### Step 2: Create New Tables (Healthcare entities)
- blood_types
- vital_signs
- diagnoses
- prescriptions
- medications
- lab_tests, lab_results
- allergies
- immunizations
- insurance_providers

### Step 3: Rename Existing Tables
```sql
-- Rename core tables
RENAME TABLE clients TO patients;
RENAME TABLE reservations TO appointments;
RENAME TABLE branches TO departments;
RENAME TABLE medical_documents TO medical_records;
```

### Step 4: Add New Columns
```sql
ALTER TABLE patients 
  ADD COLUMN blood_type_id INT AFTER gender_id,
  ADD COLUMN emergency_contact_name VARCHAR(255),
  ADD COLUMN emergency_contact_phone VARCHAR(50),
  ADD COLUMN marital_status VARCHAR(20),
  ADD COLUMN occupation VARCHAR(255);
```

### Step 5: Remove Veterinary Columns
```sql
ALTER TABLE patients
  DROP COLUMN species_id,
  DROP COLUMN breed_id,
  DROP COLUMN dangerous,
  DROP COLUMN dangerous_note;
```

### Step 6: Update Foreign Keys
```sql
-- Update all references to clients → patients
ALTER TABLE medical_records 
  CHANGE client_id patient_id INT;

ALTER TABLE invoices
  CHANGE client_id patient_id INT;

-- Update all references to reservations → appointments
ALTER TABLE medical_records
  CHANGE reservation_id appointment_id INT;
```

### Step 7: Drop Obsolete Tables
```sql
DROP TABLE IF EXISTS species;
DROP TABLE IF EXISTS breeds;
-- Keep patients table was for animals - DELETE
DROP TABLE IF EXISTS patients; -- (the old animals table)
```

---

## Data Transformation

### Transform Patient Data
```php
// Migrate allergies column to allergies table
DB::table('clients')->whereNotNull('allergies')->each(function ($client) {
    DB::table('allergies')->insert([
        'patient_id' => $client->id,
        'allergen' => $client->allergies,
        'allergy_type' => 'Unknown',
        'severity' => 'Unspecified',
        'hospital_id' => $client->organisation_id,
        'created_at' => now(),
    ]);
});
```

### Transform Appointment Statuses
```php
// Map veterinary statuses to healthcare
DB::table('appointments')->update([
    'status_id' => DB::raw('
        CASE 
            WHEN status_id = 1 THEN 1  -- Ordered → Scheduled
            WHEN status_id = 2 THEN 2  -- WaitingRoom → CheckedIn
            WHEN status_id = 3 THEN 3  -- InProcess → InProgress
            WHEN status_id = 4 THEN 4  -- Completed → Completed
        END
    ')
]);
```

---

## Rollback Plan

If migration fails:

1. **Restore from backup**:
```bash
mysql -u root -p vetlio < vetlio_backup_YYYYMMDD_HHMMSS.sql
```

2. **Rollback migrations**:
```bash
php artisan migrate:rollback --step=20
```

3. **Verify data integrity**:
```bash
php artisan test --filter=DatabaseIntegrity
```

---

## Testing Strategy

### Pre-Migration Checklist
- [ ] Full database backup created
- [ ] Backup verified (can restore)
- [ ] All migrations tested on staging
- [ ] Data transformation scripts tested
- [ ] Foreign key integrity verified

### Post-Migration Verification
- [ ] Table count matches expected
- [ ] Row counts match (e.g., clients count = patients count)
- [ ] Foreign keys intact
- [ ] No orphaned records
- [ ] Sample data queries work
- [ ] Application loads without errors

### Integration Tests
```php
public function test_patient_data_preserved_after_migration()
{
    $clientCount = DB::table('clients')->count();
    
    Artisan::call('migrate:fresh --seed');
    
    $patientCount = DB::table('patients')->count();
    
    $this->assertEquals($clientCount, $patientCount);
}
```

---

## Production Migration Checklist

**Week Before:**
- [ ] Announce maintenance window
- [ ] Create full backup
- [ ] Test on staging with production data copy
- [ ] All tests pass
- [ ] Rollback scripts ready

**Day Before:**
- [ ] Final backup
- [ ] Disable cron jobs
- [ ] Maintenance mode ON
- [ ] Export critical data

**During Migration:**
- [ ] Enable maintenance mode: `php artisan down`
- [ ] Run migrations: `php artisan migrate --force`
- [ ] Run data transformations
- [ ] Verify data integrity
- [ ] Run smoke tests
- [ ] Clear caches: `php artisan optimize:clear`

**Post-Migration:**
- [ ] Test critical journeys
- [ ] Check logs for errors
- [ ] Verify foreign keys
- [ ] Maintenance mode OFF: `php artisan up`
- [ ] Re-enable cron jobs
- [ ] Monitor for 24 hours

---

## Migration Scripts Location

All migration files will be in:
```
database/migrations/2025_01_01_XXXXXX_*.php
```

Ordered by dependency:
1. Create new lookup tables (blood_types, etc.)
2. Create new entity tables (vital_signs, prescriptions, etc.)
3. Rename existing tables
4. Add new columns
5. Remove old columns
6. Update foreign keys
7. Drop obsolete tables

---

**Document Version**: 1.0
**For detailed migration scripts, see**: [migration-scripts.md](./migration-scripts.md)
