<?php

namespace App\Controllers;

class TableCheck extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();
        $fields = $db->getFieldData('practitioner_personal_infos');
        echo "COLUMNS of practitioner_personal_infos:\n";
        foreach ($fields as $field) {
            echo $field->name . "\n";
        }
        exit;
    }
}
