<?php
// Add missing columns to application_type_fees table
$mysqli = new mysqli('127.0.0.1', 'root', 'root', 'osa_app', 3306);

if ($mysqli->connect_error) {
    die('Connection failed: ' . $mysqli->connect_error);
}

echo "Connected to database: osa_app\n\n";

// Check if deleted_at column exists
$result = $mysqli->query("SHOW COLUMNS FROM application_type_fees LIKE 'deleted_at'");
if ($result->num_rows == 0) {
    echo "Adding deleted_at column...\n";
    $mysqli->query("ALTER TABLE application_type_fees ADD COLUMN deleted_at DATETIME NULL AFTER updated_at");
    echo "✓ deleted_at column added\n";
} else {
    echo "✓ deleted_at column already exists\n";
}

// Check if created_at column exists
$result = $mysqli->query("SHOW COLUMNS FROM application_type_fees LIKE 'created_at'");
if ($result->num_rows == 0) {
    echo "Adding created_at column...\n";
    $mysqli->query("ALTER TABLE application_type_fees ADD COLUMN created_at DATETIME NULL AFTER amount");
    echo "✓ created_at column added\n";
} else {
    echo "✓ created_at column already exists\n";
}

// Check if updated_at column exists
$result = $mysqli->query("SHOW COLUMNS FROM application_type_fees LIKE 'updated_at'");
if ($result->num_rows == 0) {
    echo "Adding updated_at column...\n";
    $mysqli->query("ALTER TABLE application_type_fees ADD COLUMN updated_at DATETIME NULL AFTER created_at");
    echo "✓ updated_at column added\n";
} else {
    echo "✓ updated_at column already exists\n";
}

echo "\nDone!\n";
$mysqli->close();
