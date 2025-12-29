<?php

// Load CodeIgniter's database configuration manually if needed, or just use raw PDO
// Since we are in the app structure, we can try to bootstrap minimal CI or just use raw PDO with credentials from .env
// Let's use raw PDO to be safe and avoid CI framework dependencies that might trigger the Locale error.

// $envFile = __DIR__ . '/.env';
// $env = parse_ini_file($envFile);

$host = '127.0.0.1';
$user = 'root';
$pass = ''; // Empty password from .env
$dbName = 'vessel_discharge'; 

// if (file_exists($envFile)) { ... } // Skip .env parsing since it failed and we have values

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbName", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Connected to database: $dbName\n";

    // 1. Add 'currency' and 'deleted_at' column to license_types if not exists
    echo "Checking 'currency' and 'deleted_at' columns in 'license_types'...\n";
    try {
        $stmt = $pdo->query("SHOW COLUMNS FROM license_types LIKE 'currency'");
        if ($stmt->rowCount() == 0) {
            $pdo->exec("ALTER TABLE license_types ADD COLUMN currency VARCHAR(10) DEFAULT 'TZS' AFTER fee");
            echo "Added 'currency' column.\n";
        }

        $stmt = $pdo->query("SHOW COLUMNS FROM license_types LIKE 'deleted_at'");
        if ($stmt->rowCount() == 0) {
            $pdo->exec("ALTER TABLE license_types ADD COLUMN deleted_at DATETIME NULL DEFAULT NULL");
            echo "Added 'deleted_at' column.\n";
        }

    } catch (PDOException $e) {
        // Table might not exist, creating it
        echo "Table 'license_types' might not exist. Creating it...\n";
        $pdo->exec("CREATE TABLE IF NOT EXISTS license_types (
            id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            description TEXT,
            fee DECIMAL(15,2) NOT NULL,
            currency VARCHAR(10) DEFAULT 'TZS',
            created_at DATETIME,
            updated_at DATETIME,
            deleted_at DATETIME
        )");
        echo "Created 'license_types' table.\n";
    }

    // 2. Create 'application_type_fees' table if not exists
    echo "Checking 'application_type_fees' table...\n";
    $pdo->exec("CREATE TABLE IF NOT EXISTS application_type_fees (
        id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        application_type VARCHAR(100) NOT NULL,
        nationality VARCHAR(50) NOT NULL,
        amount DECIMAL(15,2) NOT NULL,
        created_at DATETIME,
        updated_at DATETIME
    )");
    echo "Checked/Created 'application_type_fees' table.\n";

    // 3. Seed Data
    echo "Seeding data...\n";

    // License Types
    $licenseTypes = [
        ['name' => 'Class A License', 'description' => 'License for large scale operations', 'fee' => 150000.00, 'currency' => 'TZS'],
        ['name' => 'Class B License', 'description' => 'License for medium scale operations', 'fee' => 100000.00, 'currency' => 'TZS'],
        ['name' => 'Class C License', 'description' => 'License for small scale operations', 'fee' => 50000.00, 'currency' => 'TZS'],
        ['name' => 'Weighbridge Operator License', 'description' => 'License to operate a weighbridge', 'fee' => 200000.00, 'currency' => 'TZS'],
    ];

    $stmtInsertLicense = $pdo->prepare("INSERT INTO license_types (name, description, fee, currency, created_at, updated_at) VALUES (:name, :description, :fee, :currency, NOW(), NOW())");
    $stmtCheckLicense = $pdo->prepare("SELECT COUNT(*) FROM license_types WHERE name = :name");

    foreach ($licenseTypes as $lt) {
        $stmtCheckLicense->execute([':name' => $lt['name']]);
        if ($stmtCheckLicense->fetchColumn() == 0) {
            $stmtInsertLicense->execute($lt);
            echo "Inserted License: {$lt['name']}\n";
        }
    }

    // Application Fees
    $appFees = [
        ['application_type' => 'New License', 'nationality' => 'Citizen', 'amount' => 10000.00],
        ['application_type' => 'New License', 'nationality' => 'Non-Citizen', 'amount' => 50000.00],
        ['application_type' => 'Renew License', 'nationality' => 'Citizen', 'amount' => 5000.00],
        ['application_type' => 'Renew License', 'nationality' => 'Non-Citizen', 'amount' => 25000.00],
    ];

    $stmtInsertFee = $pdo->prepare("INSERT INTO application_type_fees (application_type, nationality, amount, created_at, updated_at) VALUES (:type, :nat, :amount, NOW(), NOW())");
    $stmtCheckFee = $pdo->prepare("SELECT COUNT(*) FROM application_type_fees WHERE application_type = :type AND nationality = :nat");

    foreach ($appFees as $af) {
         $stmtCheckFee->execute([':type' => $af['application_type'], ':nat' => $af['nationality']]);
         if ($stmtCheckFee->fetchColumn() == 0) {
             $stmtInsertFee->execute([
                 ':type' => $af['application_type'],
                 ':nat' => $af['nationality'],
                 ':amount' => $af['amount']
             ]);
             echo "Inserted Fee: {$af['application_type']} - {$af['nationality']}\n";
         }
    }

    echo "Done!\n";

} catch (PDOException $e) {
    echo "Database Error: " . $e->getMessage() . "\n";
    exit(1);
}
