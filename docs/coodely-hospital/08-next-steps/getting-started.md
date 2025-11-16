# 🚀 Getting Started - Coodely Hospital

## Quick Start Guide

This guide will help you begin transforming Vetlio into Coodely Hospital.

---

## Prerequisites

Before you begin, ensure you have:

- [x] PHP 8.3+ installed
- [x] Composer installed
- [x] Node.js 20+ and npm/pnpm
- [x] MySQL 8.0+ or MariaDB 10.6+
- [x] Redis (optional but recommended)
- [x] Git

---

## Step 1: Environment Setup (30 minutes)

### Clone and Setup Repository

```bash
# Clone Vetlio repository as base
git clone https://github.com/zeyadrezk/vetlio-CRM.git coodely-hospital
cd coodely-hospital

# Create new branch for transformation
git checkout -b feature/healthcare-transformation

# Install dependencies
composer install
npm install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### Configure Environment

Edit `.env` file:

```env
APP_NAME="Coodely Hospital"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=coodely_hospital
DB_USERNAME=root
DB_PASSWORD=your_password

# Optional: Redis for caching and queues
REDIS_CLIENT=phpredis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379

# Email configuration
MAIL_MAILER=log
MAIL_FROM_ADDRESS="noreply@coodely.com"
MAIL_FROM_NAME="${APP_NAME}"
```

### Create Database

```bash
# Create database
mysql -u root -p -e "CREATE DATABASE coodely_hospital CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Run existing migrations (Vetlio base)
php artisan migrate --seed

# Link storage
php artisan storage:link

# Build assets
npm run build
```

### Test Installation

```bash
# Start development server
php artisan serve

# Visit: http://localhost:8000/app/login
# Login: admin@org1.com / password (from Vetlio seeders)
```

---

## Step 2: Review Documentation (1-2 hours)

Read through the planning documents to understand the transformation:

1. **[Executive Summary](../00-overview/executive-summary.md)** - Project overview
2. **[Transformation Mapping](../01-transformation/transformation-mapping.md)** - Entity changes
3. **[Functional Requirements](../03-system-specification/functional-requirements.md)** - What to build
4. **[Implementation Roadmap](../05-implementation/roadmap.md)** - How to build it

---

## Step 3: Answer Key Questions (30 minutes)

Before starting development, answer these questions:

**See**: [Questions to Resolve](./questions.md)

Key decisions needed:
- Which country/region? (affects compliance)
- Keep fiscalization or remove?
- Insurance integration priority?
- Domain name for deployment?

---

## Step 4: Set Up Project Board (30 minutes)

Create a project management board (GitHub Projects, Jira, Trello, etc.):

### Suggested Board Structure

**Columns**:
1. Backlog
2. To Do (This Sprint)
3. In Progress
4. Code Review
5. Testing
6. Done

**Import Tasks From**:
- [Phase Breakdown](../05-implementation/phase-breakdown.md)

---

## Step 5: Begin Phase 1 - Database Migrations (Week 2)

### Create First Migration

```bash
# Create migration for blood types
php artisan make:migration create_blood_types_table

# Create migration for vital signs
php artisan make:migration create_vital_signs_table

# Create migration to rename clients to patients
php artisan make:migration rename_clients_to_patients_table
```

### Example Migration: Blood Types

```php
// database/migrations/2025_01_01_000001_create_blood_types_table.php

public function up(): void
{
    Schema::create('blood_types', function (Blueprint $table) {
        $table->id();
        $table->string('name', 10)->unique(); // A+, O-, etc.
        $table->string('description')->nullable();
        $table->timestamps();
    });
    
    // Seed blood types
    DB::table('blood_types')->insert([
        ['name' => 'A+', 'description' => 'A Positive'],
        ['name' => 'A-', 'description' => 'A Negative'],
        ['name' => 'B+', 'description' => 'B Positive'],
        ['name' => 'B-', 'description' => 'B Negative'],
        ['name' => 'AB+', 'description' => 'AB Positive'],
        ['name' => 'AB-', 'description' => 'AB Negative'],
        ['name' => 'O+', 'description' => 'O Positive'],
        ['name' => 'O-', 'description' => 'O Negative'],
    ]);
}
```

**See**: [Database Migration Strategy](../07-database-migration/migration-strategy.md) for complete guide

---

## Step 6: Create New Models (Week 3)

```bash
# Create healthcare-specific models
php artisan make:model VitalSign -m
php artisan make:model Allergy -m
php artisan make:model Prescription -m
php artisan make:model LabTest -m
php artisan make:model Diagnosis -m
```

### Example Model: VitalSign

```php
// app/Models/VitalSign.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VitalSign extends Model
{
    protected $fillable = [
        'patient_id',
        'blood_pressure_systolic',
        'blood_pressure_diastolic',
        'heart_rate',
        'temperature',
        'weight',
        'height',
        'oxygen_saturation',
        'recorded_at',
        'recorded_by',
    ];

    protected $casts = [
        'recorded_at' => 'datetime',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
```

---

## Step 7: Update Existing Models (Week 3)

### Update Patient Model (formerly Client)

```php
// app/Models/Patient.php

class Patient extends Authenticatable
{
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'date_of_birth',
        'gender_id',
        'blood_type_id', // NEW
        'emergency_contact_name', // NEW
        'emergency_contact_phone', // NEW
        'address',
        'city',
        'zip_code',
        'hospital_id',
    ];

    public function bloodType(): BelongsTo
    {
        return $this->belongsTo(BloodType::class);
    }

    public function allergies(): HasMany
    {
        return $this->hasMany(Allergy::class);
    }

    public function vitalSigns(): HasMany
    {
        return $this->hasMany(VitalSign::class);
    }
}
```

---

## Step 8: Apply Branding (Week 9)

### Update Filament Panel Colors

```php
// app/Providers/Filament/AppPanelProvider.php

->colors([
    'primary' => [
        50  => '#E8F4F8',
        100 => '#C5E5F0',
        200 => '#9FD4E7',
        300 => '#76C2DE',
        400 => '#56B5D7',
        500 => '#2BA8D1',  // Coodely Medical Blue
        600 => '#2398BE',
        700 => '#1B84A5',
        800 => '#14718C',
        900 => '#0B5468',
        950 => '#084157',
    ],
])
```

**See**: [Color Palette](../02-branding/color-palette.md) for complete color system

---

## Step 9: Testing

### Write Tests

```bash
# Create test
php artisan make:test PatientManagementTest

# Run tests
php artisan test

# With coverage
php artisan test --coverage
```

### Example Test

```php
public function test_can_create_patient_with_blood_type()
{
    $patient = Patient::factory()->create([
        'blood_type_id' => 1, // A+
    ]);

    $this->assertDatabaseHas('patients', [
        'id' => $patient->id,
        'blood_type_id' => 1,
    ]);
}
```

---

## Step 10: Deployment Preparation (Week 12)

### Production Checklist

- [ ] All tests passing
- [ ] Environment variables configured for production
- [ ] Database backup strategy in place
- [ ] SSL certificate installed
- [ ] Queue worker running
- [ ] Cron jobs scheduled
- [ ] Monitoring setup (logs, errors, uptime)
- [ ] Documentation complete

---

## Common Issues & Solutions

### Issue: Migration fails with foreign key error
**Solution**: Check migration order. Run migrations that create referenced tables first.

### Issue: "Class not found" error
**Solution**: Run `composer dump-autoload`

### Issue: Filament shows white screen
**Solution**: Clear cache: `php artisan optimize:clear`

### Issue: Assets not loading
**Solution**: Run `npm run build` and `php artisan storage:link`

---

## Getting Help

- Review [Transformation Mapping](../01-transformation/transformation-mapping.md)
- Check [Technical Design](../04-technical-design/TDD.md)
- Consult [Migration Strategy](../07-database-migration/migration-strategy.md)

---

## Daily Development Workflow

```bash
# 1. Pull latest changes
git pull origin feature/healthcare-transformation

# 2. Install any new dependencies
composer install && npm install

# 3. Run migrations
php artisan migrate

# 4. Clear caches
php artisan optimize:clear

# 5. Build assets (if frontend changes)
npm run dev  # or: npm run build

# 6. Run tests before committing
php artisan test

# 7. Commit and push
git add .
git commit -m "feat: implement vital signs module"
git push origin feature/healthcare-transformation
```

---

**You're ready to start building Coodely Hospital! 🏥**

**Next**: Proceed with [Phase 1: Database Migrations](../05-implementation/roadmap.md#phase-1)

**Document Version**: 1.0
