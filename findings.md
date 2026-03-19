# Registration System Findings & Suggestions

## 1. Existing Backend Tables

### Practitioner Registration (License Application)
Uses **3 tables**:

| Table | Purpose |
|-------|---------|
| `license_users` | Auth/login (Shield-based) - id, uuid, username, email, password_hash, user_type, phone_number, region, active |
| `practitioner_personal_infos` | Personal data linked by `user_uuid` - nationality, identity_number, first_name, second_name, last_name, gender, dob, region, district, ward, street, phone |
| `practitioner_business_infos` | Business data linked by `user_uuid` - tin, company_name, company_email, company_phone, brela_number, bus_region, bus_district, bus_ward, postal_code, bus_street |

### Pattern Approval Registration
Uses **2 tables** (reuses practitioner info tables):

| Table | Purpose |
|-------|---------|
| `pattern_users` | Auth/login (custom, not Shield) - id, uuid, username, email, password_hash, user_type='pattern', registration_type, status, active |
| `practitioner_personal_infos` | Same table as practitioner, linked by `user_uuid` |
| `practitioner_business_infos` | Same table as practitioner, linked by `user_uuid` |

**Key insight:** Pattern approval users share the same personal/business info tables as practitioners. The only difference is the user table (`pattern_users` vs `license_users`).

---

## 2. Business Owner Registration - Current Backend State

The `customer_users` table **already exists** (created in migration `2026-02-25-082814_CreatePatternCustomerUsers.php`):

| Column | Type | Notes |
|--------|------|-------|
| id | INT(11) | Primary key, auto-increment |
| uuid | VARCHAR(32) | Nullable |
| username | VARCHAR(30) | Nullable, unique |
| email | VARCHAR(255) | Nullable, unique |
| password_hash | VARCHAR(255) | Not null |
| user_type | VARCHAR(50) | Default: 'customer' |
| registration_type | VARCHAR(50) | Default: 'customer' |
| status | VARCHAR(255) | Nullable |
| status_message | VARCHAR(255) | Nullable |
| active | TINYINT(1) | Default: 0 |
| last_active | DATETIME | Nullable |
| created_at / updated_at / deleted_at | DATETIME | Nullable |

**The AuthController already handles `registrationType === 'customer'`** - it creates the user in `customer_users` and stores personal/business info in the shared `practitioner_personal_infos` and `practitioner_business_infos` tables.

---

## 3. Problem: Data Model Mismatch

The business owner registration form collects **different data** than what the shared practitioner info tables expect:

### Fields the frontend sends but backend tables DON'T have:

| Frontend Field | Missing From |
|----------------|-------------|
| `businessInfo.businessType` (private/government) | `practitioner_business_infos` |
| `businessInfo.businessLicenseNumber` | `practitioner_business_infos` |
| `businessInfo.officePhoneNumber` | `practitioner_business_infos` |
| `businessInfo.postalAddress` | `practitioner_business_infos` (has street, not postal address) |
| `ownershipDetails.*` (all fields) | No table exists |
| `contactPerson.designation` | `practitioner_personal_infos` |
| `contactPerson.alternativePhoneNumber` | `practitioner_personal_infos` |

### Fields the backend expects but frontend DOESN'T send:

| Backend Field | Missing From Frontend |
|---------------|----------------------|
| `nationality` | `businessInfo` form group |
| `identity_number` (NIDA/Passport) | `businessInfo` form group |
| `gender` | Not collected |
| `dob` | Not collected |
| `brela_number` | Not collected |

---

## 4. Suggestions

### Option A: Create Dedicated Business Owner Tables (Recommended)

Create new tables specific to business owner registration instead of reusing the practitioner tables. This is cleaner because business owners have a fundamentally different data model (company-centric vs person-centric).

**New tables needed:**

#### `customer_business_infos`
| Column | Type | Notes |
|--------|------|-------|
| id | INT(11) | PK, auto-increment |
| user_uuid | VARCHAR(32) | FK to customer_users.uuid |
| business_type | VARCHAR(50) | 'private' or 'government' |
| company_name | VARCHAR(255) | Required |
| tin | VARCHAR(9) | Required |
| business_license_number | VARCHAR(100) | Required |
| postal_address | VARCHAR(255) | Required |
| region | VARCHAR(100) | Required |
| district | VARCHAR(100) | Required |
| ward | VARCHAR(100) | Required |
| postal_code | VARCHAR(50) | Required |
| office_phone_number | VARCHAR(20) | Required |
| created_at | DATETIME | |
| updated_at | DATETIME | |

#### `customer_ownership_details`
| Column | Type | Notes |
|--------|------|-------|
| id | INT(11) | PK, auto-increment |
| user_uuid | VARCHAR(32) | FK to customer_users.uuid |
| first_name | VARCHAR(100) | Required for private |
| second_name | VARCHAR(100) | Required for private |
| last_name | VARCHAR(100) | Required for private |
| phone_number | VARCHAR(20) | Required for private |
| email_address | VARCHAR(255) | Required for private |
| postal_address | VARCHAR(255) | Required for private |
| created_at | DATETIME | |
| updated_at | DATETIME | |

#### `customer_contact_persons`
| Column | Type | Notes |
|--------|------|-------|
| id | INT(11) | PK, auto-increment |
| user_uuid | VARCHAR(32) | FK to customer_users.uuid |
| first_name | VARCHAR(100) | Required |
| second_name | VARCHAR(100) | Required |
| last_name | VARCHAR(100) | Required |
| designation | VARCHAR(100) | Required |
| phone_number | VARCHAR(20) | Required |
| alternative_phone_number | VARCHAR(20) | Optional |
| email_address | VARCHAR(255) | Required |
| created_at | DATETIME | |
| updated_at | DATETIME | |

### Option B: Extend Existing Tables (Not Recommended)

Add missing columns to `practitioner_personal_infos` and `practitioner_business_infos`. This would work but leads to:
- Many nullable columns that only apply to one registration type
- Confusing table names (practitioner tables holding customer data)
- Harder to maintain as requirements diverge

---

## 5. Backend Changes Needed (for Option A)

1. **New migration** to create the 3 tables above
2. **New models**: `CustomerBusinessInfoModel`, `CustomerOwnershipDetailModel`, `CustomerContactPersonModel`
3. **Update `AuthController::register()`** to handle `registrationType === 'customer'` differently:
   - Save to `customer_business_infos` instead of `practitioner_business_infos`
   - Save to `customer_ownership_details` (if private company)
   - Save to `customer_contact_persons`
4. **Update the frontend payload** to send `registrationType: 'business_owner'` (already does this)
5. **Add `phone_number` column** to `customer_users` table (currently missing, but the AuthController tries to insert it)

---

## 6. Frontend Payload Mapping (Current -> Backend)

```
Frontend                          -> Backend Table.Column
---------------------------------------------------------------
businessInfo.businessType         -> customer_business_infos.business_type
businessInfo.companyName          -> customer_business_infos.company_name
businessInfo.tin                  -> customer_business_infos.tin
businessInfo.businessLicenseNumber-> customer_business_infos.business_license_number
businessInfo.postalAddress        -> customer_business_infos.postal_address
businessInfo.region               -> customer_business_infos.region
businessInfo.district             -> customer_business_infos.district
businessInfo.ward                 -> customer_business_infos.ward
businessInfo.postalCode           -> customer_business_infos.postal_code
businessInfo.officePhoneNumber    -> customer_business_infos.office_phone_number

ownershipDetails.firstName        -> customer_ownership_details.first_name
ownershipDetails.secondName       -> customer_ownership_details.second_name
ownershipDetails.lastName         -> customer_ownership_details.last_name
ownershipDetails.phoneNumber      -> customer_ownership_details.phone_number
ownershipDetails.emailAddress     -> customer_ownership_details.email_address
ownershipDetails.postalAddress    -> customer_ownership_details.postal_address

contactPerson.firstName           -> customer_contact_persons.first_name
contactPerson.secondName          -> customer_contact_persons.second_name
contactPerson.lastName            -> customer_contact_persons.last_name
contactPerson.designation         -> customer_contact_persons.designation
contactPerson.phoneNumber         -> customer_contact_persons.phone_number
contactPerson.alternativePhoneNumber -> customer_contact_persons.alternative_phone_number
contactPerson.emailAddress        -> customer_contact_persons.email_address

contactPerson.emailAddress        -> customer_users.email
contactPerson.phoneNumber         -> customer_users.phone_number (needs column added)
security.password                 -> customer_users.password_hash
```
