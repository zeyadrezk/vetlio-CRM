# Translation Implementation Plan for Vetlio CRM

## 📊 Project Inventory

### Total Files Requiring Translation
- **181 Filament PHP files**
- **18 Resource classes**
- **120+ Pages and Widgets**
- **Multiple Tables, Forms, Schemas, and Actions**

---

## 📂 Complete Resource Breakdown

### **1. Admin Panel Resources (2)**
- `AdminResource.php` - Admin user management
- `OrganisationResource.php` - Multi-tenant organizations

### **2. App Panel Resources (16)**

#### Main Resources (6)
1. **ClientResource** - Client management
   - Forms: ClientForm.php
   - Tables: ClientsTable.php
   - Pages: View, Edit, Create, List
   - Sub-pages: Notes, Documents, Invoices, Patients, Payments, Reminders, Reservations, Tasks, ItemsToPay
   - Widgets: ClientStats
   - Actions: SendEmailAction

2. **PatientResource** - Patient (animal) management
   - Forms: PatientForm.php
   - Tables: PatientsTable.php
   - Infolists: PatientInfolist.php
   - Pages: View, Edit, Create, List
   - Sub-pages: Documents, MedicalDocuments, Reminders, Reservations

3. **InvoiceResource** - Invoice management
   - Forms: InvoiceForm.php
   - Tables: InvoicesTable.php
   - Infolists: InvoiceInfolist.php
   - Pages: View, Edit, Create, List, NotPayedInvoices
   - Sub-pages: Notes, Payments, Reminders, Tasks
   - Actions: SendInvoiceAction, FiscalizeAction, CancelInvoiceAction

4. **ReservationResource** - Appointment/reservation management
   - Forms: ReservationForm.php
   - Tables: ReservationsTable.php
   - Infolists: ReservationInfolist.php
   - Pages: View, Edit, Create, List, DoctorReservations

5. **MedicalDocumentResource** - Medical records
   - Forms: MedicalDocumentForm.php
   - Tables: MedicalDocumentsTable.php
   - Infolists: MedicalDocumentInfolist.php
   - Pages: View, Edit, Create, List
   - Sub-pages: PastItems, Tasks, UploadDocuments

6. **PaymentResource** - Payment tracking
   - Forms: PaymentForm.php
   - Tables: PaymentsTable.php
   - Infolists: PaymentInfolist.php
   - Pages: View, Edit, Create, List

7. **TaskResource** - Task management
   - Forms: TaskForm.php
   - Tables: TasksTable.php
   - Pages: Edit, Create, List

#### Setup Cluster Resources (9)
8. **BranchResource** - Branch/location management
9. **UserResource** - User management
10. **ServiceResource** - Service catalog
11. **ServiceGroupResource** - Service grouping
12. **RoomResource** - Room management
13. **BankResource** - Bank information
14. **PriceListResource** - Pricing management
15. **EmailTemplateResource** - Email templates

### **3. Portal Panel Resources (1)**
16. **PatientResource** (Portal version) - Client-facing patient management

### **4. Standalone Pages (12)**
- Dashboard (App, Admin, Portal)
- Calendar
- AppointmentRequests
- ConfirmAppointmentArrival (Public)
- Settings pages: Organisation, InvoiceSettings, FiscalisationSettings

### **5. Widgets (8)**
- StatsOverview (App & Admin)
- CalendarWidget
- AppointmentsTodayWidget
- RevenueChart
- ClientStats (App & Portal)
- OrganisationCreatedByMonth

### **6. Shared Components**
- ItemsToSelectTable
- CreatedAtColumn, UpdatedAtColumn
- PhoneField, EmailField
- Various Actions (SendEmail, Fiscalize, Cancel, etc.)

---

## 🎯 Translation Strategy

### Phase 1: Foundation (Week 1) ✅ COMPLETED
- [x] Set up language files structure
- [x] Configure Filament panels for multi-language
- [x] Create common translations (fields, actions, messages)
- [x] Create navigation translations
- [x] Create basic resource translations (Clients, Patients, Invoices, Reservations)
- [x] Document translation guide

### Phase 2: Core Resources (Week 2-3)
**Translate main application resources:**

#### Week 2: High-Priority Resources
1. **MedicalDocumentResource** (3 days)
   - Medical document fields and forms
   - Medical item fields
   - Document types and statuses
   - Past items, tasks, upload documents pages

2. **PaymentResource** (2 days)
   - Payment fields and forms
   - Payment method translations
   - Payment statuses

3. **TaskResource** (1 day)
   - Task fields
   - Task statuses (created, in_progress, completed)
   - Priority levels

4. **Shared Components** (1 day)
   - SendEmailAction (already done)
   - FiscalizeAction
   - CancelInvoiceAction
   - ItemsToSelectTable

#### Week 3: Setup Cluster Resources
5. **ServiceResource & ServiceGroupResource** (2 days)
   - Service names and descriptions
   - Service groups
   - Pricing fields

6. **BranchResource** (1 day)
   - Branch information fields
   - Address fields
   - Contact information

7. **UserResource** (2 days)
   - User fields
   - Role translations
   - Permission labels

8. **RoomResource** (1 day)
   - Room names and types
   - Capacity fields

9. **BankResource** (1 day)
   - Bank information fields

10. **PriceListResource** (2 days)
    - Price list fields
    - Price items

11. **EmailTemplateResource** (1 day)
    - Template fields (already partially done)
    - Template types

### Phase 3: Admin & Settings (Week 4)
12. **AdminResource** (1 day)
    - Admin user fields

13. **OrganisationResource** (2 days)
    - Organisation fields
    - Settings fields
    - Multi-tenant labels

14. **Settings Pages** (2 days)
    - Organisation settings
    - Invoice settings
    - Fiscalisation settings

15. **Admin Widgets** (1 day)
    - Stats overview
    - Organisation charts

### Phase 4: Portal & Public (Week 5)
16. **Portal PatientResource** (1 day)
    - Client-facing patient fields

17. **Portal Pages** (2 days)
    - Portal dashboard
    - Appointments page
    - Invoices page
    - Medical documents page

18. **Portal Widgets** (1 day)
    - Client stats widget

19. **Public Pages** (1 day)
    - Appointment arrival confirmation

### Phase 5: Pages & Widgets (Week 6)
20. **Dashboard Pages** (2 days)
    - App dashboard
    - Admin dashboard
    - Portal dashboard

21. **Calendar & Appointments** (2 days)
    - Calendar widget
    - Calendar page
    - Appointment requests page
    - Appointments today widget

22. **App Widgets** (1 day)
    - Revenue chart
    - Stats overview

### Phase 6: Client Sub-Pages (Week 7)
23. **Client Relation Pages** (3 days)
    - ClientNotes
    - ClientDocuments
    - ClientInvoices
    - ClientPatients
    - ClientPayments
    - ClientReminders
    - ClientReservations
    - ClientTasks
    - ClientItemsToPay

### Phase 7: Invoice/Payment Sub-Pages (Week 7)
24. **Invoice Relation Pages** (2 days)
    - InvoiceNotes
    - InvoicePayments
    - InvoiceReminders
    - InvoiceTasks

### Phase 8: Patient Sub-Pages (Week 7)
25. **Patient Relation Pages** (1 day)
    - PatientDocuments
    - PatientMedicalDocuments
    - PatientReminders
    - PatientReservations

### Phase 9: Medical Document Sub-Pages (Week 7)
26. **MedicalDocument Relation Pages** (1 day)
    - MedicalDocumentPastItems
    - MedicalDocumentTasks
    - MedicalDocumentUploadDocuments

### Phase 10: Validation & Testing (Week 8)
27. **Quality Assurance** (5 days)
    - Test all translated resources in English
    - Test all translated resources in Arabic
    - Verify RTL layout correctness
    - Fix missing translations
    - Validate consistency across resources

---

## 📋 Translation File Structure

### Completed ✅
```
lang/
├── en/
│   ├── common.php
│   ├── navigation.php
│   ├── filament.php
│   ├── auth.php, validation.php, etc.
│   └── resources/
│       ├── clients.php
│       ├── patients.php
│       ├── invoices.php
│       ├── reservations.php
│       └── email.php
└── ar/ (mirror structure)
```

### To Be Created
```
lang/
├── en/resources/
│   ├── medical_documents.php
│   ├── payments.php
│   ├── tasks.php
│   ├── services.php
│   ├── service_groups.php
│   ├── branches.php
│   ├── users.php
│   ├── rooms.php
│   ├── banks.php
│   ├── price_lists.php
│   ├── email_templates.php
│   ├── admins.php
│   ├── organisations.php
│   ├── calendar.php
│   ├── appointments.php
│   ├── dashboard.php
│   └── widgets.php
└── ar/ (mirror structure)
```

---

## 🔄 Implementation Pattern

### For Each Resource
1. **Create translation file** in `lang/en/resources/{resource}.php`
2. **Create Arabic translation** in `lang/ar/resources/{resource}.php`
3. **Update Resource class** to use translations:
   ```php
   public static function getNavigationLabel(): string
   {
       return __('resources/{resource}.navigation');
   }
   ```
4. **Update Table columns** to use translations
5. **Update Form fields** to use translations
6. **Update Actions** to use translations
7. **Update Pages** to use translations
8. **Test in both languages**

### Translation File Template
```php
<?php
return [
    'navigation' => 'Label',
    'singular' => 'singular',
    'plural' => 'plural',
    'navigation_group' => 'Group',

    'fields' => [
        'field_name' => 'Field Label',
    ],

    'tabs' => [
        'tab_name' => 'Tab Label',
    ],

    'pages' => [
        'page_name' => 'Page Title',
    ],

    'actions' => [
        'action_name' => 'Action Label',
    ],

    'filters' => [
        'filter_name' => 'Filter Label',
    ],

    'validation' => [
        'rule' => 'Validation message',
    ],

    'messages' => [
        'success' => 'Success message',
        'error' => 'Error message',
    ],
];
```

---

## 📊 Progress Tracking

### Completion Checklist

#### Phase 1: Foundation ✅
- [x] Language structure
- [x] Panel configuration
- [x] Common translations
- [x] Navigation
- [x] Basic resources (4/18)

#### Phase 2: Core Resources (0/10)
- [ ] MedicalDocumentResource
- [ ] PaymentResource
- [ ] TaskResource
- [ ] Shared components
- [ ] ServiceResource
- [ ] BranchResource
- [ ] UserResource
- [ ] RoomResource
- [ ] BankResource
- [ ] PriceListResource
- [ ] EmailTemplateResource

#### Phase 3: Admin & Settings (0/4)
- [ ] AdminResource
- [ ] OrganisationResource
- [ ] Settings pages
- [ ] Admin widgets

#### Phase 4: Portal & Public (0/4)
- [ ] Portal resources
- [ ] Portal pages
- [ ] Portal widgets
- [ ] Public pages

#### Phase 5: Pages & Widgets (0/3)
- [ ] Dashboard pages
- [ ] Calendar & appointments
- [ ] App widgets

#### Phase 6-9: Sub-Pages (0/15)
- [ ] Client sub-pages (9 pages)
- [ ] Invoice sub-pages (4 pages)
- [ ] Patient sub-pages (4 pages)
- [ ] MedicalDocument sub-pages (3 pages)

#### Phase 10: Testing (0/1)
- [ ] Quality assurance

**Overall Progress: 4/18 Resources (22%)**

---

## 🎯 Priority Matrix

### High Priority (User-Facing, Frequent Use)
1. ClientResource ✅
2. PatientResource ✅
3. ReservationResource ✅
4. InvoiceResource ✅
5. MedicalDocumentResource
6. PaymentResource
7. Dashboard
8. Calendar

### Medium Priority (Admin/Setup)
9. ServiceResource
10. UserResource
11. BranchResource
12. EmailTemplateResource
13. Settings pages

### Low Priority (Occasional Use)
14. TaskResource
15. RoomResource
16. BankResource
17. PriceListResource
18. AdminResource
19. OrganisationResource

---

## ✅ Quality Standards

### All Translations Must:
1. ✅ Have complete English version
2. ✅ Have complete Arabic version
3. ✅ Use consistent terminology across resources
4. ✅ Follow naming conventions in TRANSLATION_GUIDE.md
5. ✅ Test correctly in RTL (Arabic) mode
6. ✅ Include all field labels, actions, messages
7. ✅ Validate with actual UI testing
8. ✅ Document any special cases

---

## 📝 Notes

- **No Database Modifications**: All translations are UI-only
- **RTL Support**: Arabic automatically uses right-to-left layout
- **Consistency**: Use `common.php` for shared fields/actions
- **Testing**: Each resource must be tested in both languages
- **Documentation**: Update TRANSLATION_GUIDE.md as patterns emerge

---

## 🚀 Getting Started

1. Review current translation files in `lang/en/` and `lang/ar/`
2. Follow the template in TRANSLATION_GUIDE.md
3. Start with Phase 2, Week 2 tasks
4. Create translation files before modifying PHP code
5. Test each resource after translation
6. Commit progress regularly

---

## 📅 Estimated Timeline

- **Phase 1**: ✅ Completed (1 week)
- **Phase 2-3**: 4 weeks
- **Phase 4-5**: 2 weeks
- **Phase 6-9**: 1 week
- **Phase 10**: 1 week

**Total Estimated Time**: 9 weeks (2 months)

With dedicated effort, this could be compressed to 4-6 weeks.

---

## 🎓 Resources

- `TRANSLATION_GUIDE.md` - Implementation guide with examples
- `lang/en/resources/` - English translation examples
- `lang/ar/resources/` - Arabic translation examples
- Laravel Documentation: https://laravel.com/docs/localization
- Filament Documentation: https://filamentphp.com/docs/panels/installation#setting-up-multi-tenancy

---

**Last Updated**: 2025-11-14
**Version**: 1.0
**Status**: Phase 1 Complete, Ready for Phase 2
