<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Modules\MetrologicalSupervision\Models\ShipModel;

class VesselProfileController extends BaseController
{
    protected $shipModel;

    public function __construct()
    {
        $this->shipModel = new ShipModel();
    }

    public function index()
    {
        $data = [
            'page' => [
                'title' => 'Vessel Profile',
                'heading' => 'Vessel Profile'
            ]
        ];

        // Get all vessels for the form
        $data['vessels'] = $this->shipModel->findAll();

        return view('Modules/MetrologicalSupervision/Views/VesselProfile', $data);
    }

    public function saveProfile()
    {
        // Handle saving vessel profile data
        $id = $this->request->getPost('id');
        $data = [
            'name' => $this->request->getPost('name'),
            'imo_number' => $this->request->getPost('imo_number'),
            'flag' => $this->request->getPost('flag'),
            // Add other fields as needed
        ];

        if ($id) {
            // Update existing vessel
            $this->shipModel->update($id, $data);
            return $this->response->setJSON([
                'status' => 1,
                'msg' => 'Vessel profile updated successfully'
            ]);
        } else {
            // Create new vessel
            $this->shipModel->insert($data);
            return $this->response->setJSON([
                'status' => 1,
                'msg' => 'Vessel profile created successfully'
            ]);
        }
    }
}