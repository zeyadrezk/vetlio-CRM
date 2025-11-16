# ❓ Questions to Resolve Before Development

Please answer these questions before starting development to ensure proper implementation.

---

## 1. Region & Compliance

**Q1.1**: Which country/region will Coodely Hospital operate in?
- [ ] United States (HIPAA required)
- [ ] European Union (GDPR required)
- [ ] Other: ___________

**Q1.2**: Do you need HIPAA compliance features?
- [ ] Yes (adds audit logging, encryption requirements)
- [ ] No
- [ ] Not sure (we'll include them to be safe)

**Q1.3**: Do you need GDPR compliance features?
- [ ] Yes (adds right to be forgotten, consent management)
- [ ] No
- [ ] Not sure (we'll include them to be safe)

---

## 2. Fiscalization & Tax

**Q2.1**: Do you want to keep the Croatian fiscalization system?
- [ ] Yes, we operate in Croatia
- [ ] No, remove it
- [ ] Replace with another country's system: ___________

**Q2.2**: What tax system should invoicing support?
- [ ] US tax system
- [ ] EU VAT system
- [ ] Croatian fiscalization (existing)
- [ ] None (simple invoicing only)
- [ ] Other: ___________

---

## 3. Insurance & Billing

**Q3.1**: Is insurance claim processing a priority for Phase 1?
- [ ] Yes, critical (implement in Phase 1)
- [ ] Medium priority (Phase 2)
- [ ] Low priority (future enhancement)

**Q3.2**: How many insurance providers per patient?
- [ ] One (primary only)
- [ ] Two (primary + secondary)
- [ ] Multiple (unlimited)

**Q3.3**: Payment methods needed?
- [ ] Cash
- [ ] Credit/Debit Card
- [ ] Bank Transfer
- [ ] Insurance Direct Billing
- [ ] Payment Plans/Installments
- [ ] Other: ___________

---

## 4. Prescription & Pharmacy

**Q4.1**: Do you need full pharmacy/medication inventory management?
- [ ] Yes (track medication stock, expiry, etc.)
- [ ] No (just prescription records)

**Q4.2**: Do you need controlled substance tracking (DEA compliance)?
- [ ] Yes (US-based, required)
- [ ] No

**Q4.3**: Should prescriptions be printable/emailable?
- [ ] Yes (critical)
- [ ] Future enhancement

---

## 5. Lab & Diagnostics

**Q5.1**: Will lab tests be managed internally or externally?
- [ ] Internal lab (enter results in system)
- [ ] External lab (receive results via file upload)
- [ ] Both

**Q5.2**: Do you need integration with external lab systems (HL7/FHIR)?
- [ ] Yes (specify system: ___________)
- [ ] No (manual entry is fine)
- [ ] Future enhancement

---

## 6. Multi-Tenancy & Scalability

**Q6.1**: Deployment model for Phase 1?
- [ ] Single hospital/clinic (one database)
- [ ] Multi-tenant from start (multiple hospitals, one database)
- [ ] Start single-tenant, prepare for multi-tenant later

**Q6.2**: Expected number of concurrent users?
- [ ] < 50 users
- [ ] 50-100 users
- [ ] 100-500 users
- [ ] 500+ users

**Q6.3**: Expected patient database size?
- [ ] < 10,000 patients
- [ ] 10,000 - 100,000 patients
- [ ] 100,000+ patients

---

## 7. Deployment & Infrastructure

**Q7.1**: Do you already have hosting/infrastructure?
- [ ] Yes (provide details: ___________)
- [ ] No, need recommendations
- [ ] Cloud (AWS/DigitalOcean/etc.)
- [ ] Self-hosted

**Q7.2**: What domain will you use?
- [ ] Example: `coodely.com`
- [ ] Example: `hospital.coodely.com`
- [ ] Not decided yet

**Q7.3**: Email service for notifications?
- [ ] SMTP (provide server details)
- [ ] SendGrid
- [ ] AWS SES
- [ ] Mailgun
- [ ] Other: ___________

---

## 8. Features Priority

**Q8.1**: Rank these features by priority (1 = highest):

- [ ] ____ Patient Management
- [ ] ____ Appointment Scheduling
- [ ] ____ Medical Records (SOAP notes)
- [ ] ____ Prescriptions
- [ ] ____ Lab Tests & Results
- [ ] ____ Billing & Invoicing
- [ ] ____ Patient Portal
- [ ] ____ Reporting & Analytics
- [ ] ____ Inventory Management (pharmacy, supplies)

**Q8.2**: Must-have features for Phase 1 launch?
- List: ___________________________________________

**Q8.3**: Nice-to-have features (can be Phase 2)?
- List: ___________________________________________

---

## 9. User Roles & Workflow

**Q9.1**: Which user roles do you need? (check all)
- [ ] Hospital Administrator
- [ ] Doctor/Physician
- [ ] Nurse
- [ ] Receptionist
- [ ] Lab Technician
- [ ] Pharmacist
- [ ] Radiologist
- [ ] Other: ___________

**Q9.2**: Will doctors have specializations?
- [ ] Yes (Cardiology, Pediatrics, etc.)
- [ ] No (general practice only)

**Q9.3**: Do you need department-based access control?
- [ ] Yes (doctors only see their department's patients)
- [ ] No (all staff see all patients)

---

## 10. Patient Portal

**Q10.1**: Should patients be able to book appointments online?
- [ ] Yes (with approval workflow)
- [ ] Yes (instant booking)
- [ ] No (staff only)

**Q10.2**: Should patients see lab results immediately or after doctor approval?
- [ ] Immediately
- [ ] After doctor approval
- [ ] Configurable per test type

**Q10.3**: Should patients be able to request prescription refills?
- [ ] Yes
- [ ] No

---

## 11. Reporting & Analytics

**Q11.1**: What reports are most important? (rank 1-5)
- [ ] ____ Revenue reports (daily/monthly/yearly)
- [ ] ____ Patient statistics (demographics, visit frequency)
- [ ] ____ Doctor performance (patients seen, revenue)
- [ ] ____ Appointment analytics (no-shows, cancellations)
- [ ] ____ Inventory reports (medication stock)

**Q11.2**: Export formats needed?
- [ ] PDF
- [ ] Excel
- [ ] CSV
- [ ] All of the above

---

## 12. Timeline & Budget

**Q12.1**: Is the 12-week timeline acceptable?
- [ ] Yes
- [ ] Need faster: ____ weeks
- [ ] Can extend: ____ weeks

**Q12.2**: When do you want to launch Phase 1?
- Target date: ___________

**Q12.3**: Budget for development?
- [ ] Under $50,000
- [ ] $50,000 - $100,000
- [ ] $100,000+
- [ ] Prefer not to disclose

---

## 13. Web Builder (Phase 9 - Future)

**Q13.1**: Priority for public website builder?
- [ ] High (essential for business)
- [ ] Medium (nice to have)
- [ ] Low (not important)

**Q13.2**: What pages are most important for public website?
- [ ] Home page
- [ ] Services/Departments
- [ ] Doctor profiles
- [ ] Appointment booking
- [ ] Blog/News
- [ ] Contact form
- [ ] Other: ___________

---

## 14. Integration & APIs

**Q14.1**: Do you need mobile app (iOS/Android) in future?
- [ ] Yes (Phase 3-4)
- [ ] Maybe
- [ ] No

**Q14.2**: Do you need API access for third-party integrations?
- [ ] Yes (specify use case: ___________)
- [ ] No
- [ ] Future consideration

---

## Submit Your Answers

Please provide answers to these questions via:
- Email
- Project management tool
- Video call discussion
- Document upload

**Deadline**: Before Phase 1 starts (Week 2)

---

**Note**: These answers will guide implementation decisions and ensure the system meets your specific needs. Unanswered questions will use reasonable defaults.

**Document Version**: 1.0
