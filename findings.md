# Equipment Registration — Backend Data Strategy

## 1. Current State of Affairs

### What we have today

| Layer | What exists | Notes |
|-------|-------------|-------|
| **Frontend** | 27 service-type forms, config-driven wizard, `service-types.ts` constants | All fields are defined in `equipment-form-configs.ts` |
| **Backend framework** | CodeIgniter 4, MySQL (`new_osa` DB), JWT auth | Business users in `business_users` table, linked via `uuid` |
| **Existing equipment-like tables** | `fuel_pump_applications`, `weighing_instruments`, `capacity_measure_instruments`, `meter_instruments` | These are for **Pattern Approval** (different module), NOT for business equipment registration |
| **Business tables** | `business_users`, `business_owner_infos`, `business_contact_infos` | Business identity already established |

### The core question

> *We have 27 different service types, each with its own unique set of fields. How do we persist this data?*

---

## 2. The Two Architectural Options

### Option A: One Table Per Service Type (27 tables)

```
equipment_fuel_pumps         → pumpName, serialNumber, product, stickerNumber, sealNumber, ...
equipment_weighbridges       → weighbridgeName, location, maxCapacity, minCapacity, ...
equipment_fixed_storage_tanks → tankNumber, product, tankCapacity, ...
... (× 27)
```

**Pros:**
- Each table has strongly typed, named columns
- Easy SQL queries: `SELECT * FROM equipment_fuel_pumps WHERE user_uuid = ?`
- Standard relational approach

**Cons:**
- 27 tables to create, 27 models, 27 separate migration files
- Adding a new service type = new migration + model + controller method
- Many tables will have very similar columns (serialNumber, stickerNumber, sealNumber, status appear in almost all)
- Hard to query across types (e.g., "show me ALL equipment for this business")

---

### Option B: Single Registration Table + JSON Details (⭐ Recommended)

```
equipment_registrations      → id, user_uuid, service_type_key, equipment_data (JSON), status, ...
equipment_documents          → id, registration_id, field_key, file_path, ...
```

**Pros:**
- Only 2 tables for ALL 27 service types
- Since each registration represents exactly **one equipment item**, querying, tracking lifecycles, and updating a single piece of equipment is incredibly straightforward. No nested arrays needed for updates.
- Adding a new service type = zero database changes (just add a frontend config)
- Single query to list all equipment: `SELECT * FROM equipment_registrations WHERE user_uuid = ?`
- The JSON `equipment_data` column stores the service-specific fields — validated by the frontend config
- MySQL 8+ has native JSON functions (`JSON_EXTRACT`, `JSON_SEARCH`) for querying inside JSON

**Cons:**
- Can't enforce column-level NOT NULL in the DB for service-specific fields (validation moves to app layer)

---

## 3. Recommended Schema — Option B

### Why Option B wins for this project

1. **Your frontend is already config-driven.** The `EQUIPMENT_FORM_CONFIGS` object defines all 27 forms. The backend should mirror this philosophy — the config IS the schema.
2. **27 tables is unsustainable.** Each new service type the WMA adds would require a database migration, a new PHP Model, and controller changes. With Option B, it's zero backend work.
3. **Cross-type queries are essential.** The "My Equipments" page needs to show ALL equipment types in a single list. With separate tables, you'd need 27 UNION queries or a complex aggregation.
4. **Your existing codebase uses this pattern.** The `pattern_application_instruments` table already stores instrument-specific data in a similar flexible way.
5. **One Item Per Registration.** By storing exactly one equipment item per row, the data model perfectly aligns with individual equipment lifecycles (each equipment item will get verified, calibrated, or rejected independently over time).

---

## 4. Table Designs

### Table 1: `equipment_registrations` (The main table)

One row per individual equipment item. Even if a business owner registers "3 Fuel Pumps" in one go via the wizard, the backend will split them and treat this as **3 separate rows** here.

```sql
CREATE TABLE `equipment_registrations` (
  `id`               INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_uuid`        VARCHAR(32)  NOT NULL,                   -- FK → business_users.uuid
  `registration_no`  VARCHAR(50)  DEFAULT NULL,               -- Auto: EQR-2026-00001 (Unique per item)
  `service_type_key` VARCHAR(50)  NOT NULL,                   -- 'fuel-pump', 'weighbridge', etc.
  `service_type_label` VARCHAR(255) NOT NULL,                 -- 'Fuel Pump', 'Weighbridge', etc.
  `category`         VARCHAR(20)  NOT NULL,                   -- 'petroleum','weighing','length','metering','other'
  `equipment_data`   JSON         NOT NULL,                   -- All service-specific fields for this item
  `status`           ENUM('draft','pending','verified','rejected') NOT NULL DEFAULT 'draft',
  `submitted_at`     DATETIME     DEFAULT NULL,
  `verified_at`      DATETIME     DEFAULT NULL,
  `verifier_id`      INT          DEFAULT NULL,
  `verifier_notes`   TEXT         DEFAULT NULL,
  `created_at`       DATETIME     DEFAULT CURRENT_TIMESTAMP,
  `updated_at`       DATETIME     DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user`     (`user_uuid`),
  KEY `idx_service`  (`service_type_key`),
  KEY `idx_status`   (`status`),
  KEY `idx_category` (`category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### Table 2: `equipment_documents` (File uploads)

One row per uploaded file. Keeps binary file references separate from JSON data block.

```sql
CREATE TABLE `equipment_documents` (
  `id`               INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `registration_id`  INT UNSIGNED NOT NULL,                   -- FK → equipment_registrations.id
  `field_key`        VARCHAR(50)  NOT NULL,                   -- Which form field this file belongs to
  `original_name`    VARCHAR(255) NOT NULL,
  `file_path`        VARCHAR(500) NOT NULL,                   -- Server path: uploads/equipment/...
  `mime_type`        VARCHAR(100) DEFAULT NULL,
  `file_size`        INT UNSIGNED DEFAULT NULL,               -- Bytes
  `created_at`       DATETIME     DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_registration` (`registration_id`),
  CONSTRAINT `fk_docs_registration`
    FOREIGN KEY (`registration_id`) REFERENCES `equipment_registrations`(`id`)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

## 5. How `equipment_data` JSON Works

When the frontend submits a **Fuel Pump** registration, the JSON stored in `equipment_registrations.equipment_data` would look like:

```json
{
  "pumpName": "Forecourt Pump FP-01",
  "serialNumber": "SN-12345",
  "product": "diesel",
  "stickerNumber": "STK-6789",
  "sealNumber": "SEAL-001",
  "sealSerialNumber": "SS-001",
  "pumpType": "electronic",
  "nozzleCount": "4",
  "status": "verified",
  "inspectionReport": "All nozzles pass accuracy test…",
  "verificationDate": "2026-03-15",
  "nextVerificationDate": "2027-03-15"
}
```

### Querying inside JSON (MySQL 8+)

```sql
-- Find all fuel pumps with product = diesel
SELECT *
FROM equipment_registrations 
WHERE service_type_key = 'fuel-pump'
  AND JSON_UNQUOTE(JSON_EXTRACT(equipment_data, '$.product')) = 'diesel';

-- Find ALL equipment for a business owner  
SELECT service_type_label, status, category, equipment_data
FROM equipment_registrations 
WHERE user_uuid = 'abc123'
ORDER BY created_at DESC;
```

---

## 6. API Design

### Endpoints to create

| Method | Route | Purpose |
|--------|-------|---------|
| `POST`   | `/api/business/equipments` | Submit a new equipment registration (can handle an array of items) |
| `GET`    | `/api/business/equipments` | List all registrations for current user |
| `GET`    | `/api/business/equipments/:id` | Get registration detail |
| `PUT`    | `/api/business/equipments/:id` | Update a specific registration |
| `DELETE` | `/api/business/equipments/:id` | Delete a draft registration |
| `POST`   | `/api/business/equipments/:id/documents` | Upload a file for a specific equipment registration |

### POST payload example (frontend sends this)

```json
{
  "serviceTypeKey": "fuel-pump",
  "serviceTypeLabel": "Fuel Pump",
  "category": "petroleum",
  "items": [
    {
      "pumpName": "Forecourt Pump FP-01",
      "serialNumber": "SN-12345",
      "product": "diesel",
      ...
    },
    {
      "pumpName": "Forecourt Pump FP-02",
      "serialNumber": "SN-12346",
      "product": "petrol",
      ...
    }
  ]
}
```

### Backend saves it as

Instead of creating one registration with two items inside, the backend splits the array and creates **TWO independent records**. Each item acts as its own distinct equipment lifecycle unit:

```
equipment_registrations:
  row 1 => id=1, user_uuid='abc', service_type_key='fuel-pump', category='petroleum', equipment_data='{ "pumpName": "Forecourt Pump FP-01", ... }', status='pending'
  row 2 => id=2, user_uuid='abc', service_type_key='fuel-pump', category='petroleum', equipment_data='{ "pumpName": "Forecourt Pump FP-02", ... }', status='pending'
```

---

## 7. Backend Files to Create

### New Files

| File | Purpose |
|------|---------|
| `app/Models/EquipmentRegistrationModel.php` | Model for `equipment_registrations` |
| `app/Models/EquipmentDocumentModel.php` | Model for `equipment_documents` |
| `app/Controllers/Api/BusinessEquipmentController.php` | REST controller |
| `app/Database/Migrations/2026-04-10-xxx_CreateEquipmentTables.php` | Migration for all 2 tables |

### Route additions (`app/Config/Routes.php`)

```php
// Business Equipment Registration
$routes->group('business/equipments', ['filter' => 'auth'], function($routes) {
    $routes->get('', 'BusinessEquipmentController::index');
    $routes->post('', 'BusinessEquipmentController::create');
    $routes->get('(:num)', 'BusinessEquipmentController::show/$1');
    $routes->put('(:num)', 'BusinessEquipmentController::update/$1');
    $routes->delete('(:num)', 'BusinessEquipmentController::delete/$1');
    $routes->post('(:num)/documents', 'BusinessEquipmentController::uploadDocument/$1');
});
```

---

## 8. Entity Relationship Diagram

```mermaid
erDiagram
    BUSINESS_USERS ||--o{ EQUIPMENT_REGISTRATIONS : "owns"
    EQUIPMENT_REGISTRATIONS ||--o{ EQUIPMENT_DOCUMENTS : "has files"

    BUSINESS_USERS {
        int id PK
        varchar uuid UK
        varchar email
        varchar user_type
    }

    EQUIPMENT_REGISTRATIONS {
        int id PK
        varchar user_uuid FK
        varchar registration_no
        varchar service_type_key
        varchar service_type_label
        varchar category
        json equipment_data
        enum status
        datetime submitted_at
        datetime verified_at
        int verifier_id
        text verifier_notes
        datetime created_at
    }

    EQUIPMENT_DOCUMENTS {
        int id PK
        int registration_id FK
        varchar field_key
        varchar original_name
        varchar file_path
        varchar mime_type
        int file_size
    }
```

---

## 9. Validation Strategy

Since field-level validation can't be enforced by the DB schema (the fields are inside JSON), validation happens in **two layers**:

| Layer | What it validates |
|-------|-------------------|
| **Frontend** (already built) | Required fields, field types, formats — driven by `EQUIPMENT_FORM_CONFIGS` |
| **Backend controller** | 1. `service_type_key` must be one of the 27 known keys. 2. `items` array must not be empty. 3. Each item is extracted and its JSON is stored natively into `equipment_data`. 4. File uploads are validated for type/size. |

> [!TIP]
> Optionally, you can duplicate the field-key list on the backend as a PHP constant and validate that each item's JSON keys are a subset of the expected keys for that service type. This prevents junk data injection.

---

## 10. Summary

| Decision | Choice |
|----------|--------|
| **Architecture** | Single equipment table + JSON fields (Option B) |
| **Tables** | 2 tables: `equipment_registrations` (for equipments) and `equipment_documents` (for files) |
| **Item Granularity** | Each individual equipment item gets its own independent row in the single table |
| **Service type routing** | `service_type_key` column maps to the 27 frontend config keys |
| **Item data** | `equipment_data` JSON column — each service type stores its own field set natively |
| **Validation** | Frontend config-driven + backend key/type guards |
| **Scalability** | Adding service type #28 = **zero** database or backend code changes |
