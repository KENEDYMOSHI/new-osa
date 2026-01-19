<?php
// backend/view_data.php

require_once 'vendor/autoload.php';

// Adjust path as needed for your setup
$appConfig = config('App');

use App\Models\PatternType;
use App\Models\InstrumentCategory;

$patternTypeModel = new PatternType();
$categoryModel = new InstrumentCategory();

echo "Pattern Types:\n";
foreach ($patternTypeModel->findAll() as $type) {
    echo "ID: {$type['id']}, Name: {$type['name']}\n";
}

echo "\nInstrument Categories:\n";
foreach ($categoryModel->findAll() as $cat) {
    echo "ID: {$cat['id']}, Name: {$cat['name']}, PatternTypeID: {$cat['pattern_type_id']}\n";
}
