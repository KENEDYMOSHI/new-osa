<?php

namespace App\Controllers;

class TestController extends BaseController
{
    public function dbTest()
    {
        $db = \Config\Database::connect();
        
        $result = [
            'database' => $db->database,
            'hostname' => $db->hostname,
            'username' => $db->username,
            'port' => $db->port,
        ];
        
        // Try to query the item
        $itemModel = new \App\Models\LicenseApplicationItemModel();
        $item = $itemModel->find('26f013d2-167f-4375-820d-8b7f925dee47');
        
        $result['item_found'] = $item ? 'YES' : 'NO';
        $result['item_data'] = $item;
        
        return $this->response->setJSON($result);
    }
}
