<?php

namespace App\Controllers;

use App\Models\ApplicationTypeFeeModel;
use App\Models\LicenseTypeModel;
use App\Models\OsaSupportModel;

class LicenseSettingController extends BaseController
{
    protected $feeModel;
    protected $licenseTypeModel;
    protected $supportModel;

    public function __construct()
    {
        $this->feeModel = new ApplicationTypeFeeModel();
        $this->licenseTypeModel = new LicenseTypeModel();
        $this->supportModel = new OsaSupportModel();
    }

    public function index()
    {
        $data = [
            'page' => [
                'heading' => 'License Setting',
                'title' => 'License Setting - WMA-MIS'
            ]
        ];

        return view('Pages/licenseSetting', $data);
    }

    // ==================== APPLICATION FEES ====================

    public function getFees()
    {
        try {
            $fees = $this->feeModel->orderBy('created_at', 'DESC')->findAll();
            return $this->response->setJSON($fees);
        } catch (\Exception $e) {
            return $this->response->setJSON([]);
        }
    }

    public function addFee()
    {
        try {
            $data = [
                'application_type' => $this->request->getPost('application_type') ?: $this->request->getJsonVar('application_type'),
                'nationality' => $this->request->getPost('nationality') ?: $this->request->getJsonVar('nationality'),
                'amount' => $this->request->getPost('amount') ?: $this->request->getJsonVar('amount'),
            ];

            if ($this->feeModel->insert($data)) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Fee added successfully'
                ]);
            } else {
                return $this->response->setStatusCode(400)->setJSON([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'messages' => $this->feeModel->errors()
                ]);
            }
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Database error: ' . $e->getMessage()
            ]);
        }
    }

    public function updateFee($id = null)
    {
        try {
             $id = $id ?: $this->request->getPost('fee_id'); // Handle both URL segment and form data if needed

            if (!$id) {
                return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => 'ID required']);
            }

            $input = $this->request->getJSON(true);
            // Fallback to POST if JSON is empty (though view sends JSON)
            $data = $input ?: [
                'application_type' => $this->request->getPost('application_type'),
                'nationality' => $this->request->getPost('nationality'),
                'amount' => $this->request->getPost('amount'),
            ];

            if ($this->feeModel->update($id, $data)) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Fee updated successfully'
                ]);
            } else {
                return $this->response->setStatusCode(400)->setJSON([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'messages' => $this->feeModel->errors()
                ]);
            }
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function deleteFee($id)
    {
        try {
            if ($this->feeModel->delete($id)) {
                return $this->response->setJSON(['status' => 'success', 'message' => 'Fee deleted successfully']);
            } else {
                return $this->response->setStatusCode(500)->setJSON(['status' => 'error', 'message' => 'Failed to delete fee']);
            }
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    // ==================== LICENSE TYPES ====================

    public function getLicenseTypes()
    {
        try {
            $types = $this->licenseTypeModel->orderBy('created_at', 'DESC')->findAll();
            return $this->response->setJSON($types);
        } catch (\Exception $e) {
            return $this->response->setJSON([]);
        }
    }

    public function addLicenseType()
    {
        try {
            $data = [
                'name' => $this->request->getPost('name') ?: $this->request->getJsonVar('name'),
                'description' => $this->request->getPost('description') ?: $this->request->getJsonVar('description'),
                'fee' => $this->request->getPost('fee') ?: $this->request->getJsonVar('fee'),
                'selected_instruments' => $this->request->getPost('selected_instruments') ?: $this->request->getJsonVar('selected_instruments'),
                'criteria' => $this->request->getPost('criteria') ?: $this->request->getJsonVar('criteria'),
            ];

            if ($this->licenseTypeModel->insert($data)) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'License Type added successfully'
                ]);
            } else {
                return $this->response->setStatusCode(400)->setJSON([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'messages' => $this->licenseTypeModel->errors()
                ]);
            }
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function updateLicenseType($id = null)
    {
        try {
             $id = $id ?: $this->request->getPost('id');

             if (!$id) {
                return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => 'ID required']);
            }

            $input = $this->request->getJSON(true);
            $data = $input ?: [
                'name' => $this->request->getPost('name'),
                'description' => $this->request->getPost('description'),
                'fee' => $this->request->getPost('fee'),
                'selected_instruments' => $this->request->getPost('selected_instruments'),
                'criteria' => $this->request->getPost('criteria'),
            ];

            if ($this->licenseTypeModel->update($id, $data)) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'License Type updated successfully'
                ]);
            } else {
                return $this->response->setStatusCode(400)->setJSON([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'messages' => $this->licenseTypeModel->errors()
                ]);
            }
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function deleteLicenseType($id)
    {
        try {
            if ($this->licenseTypeModel->delete($id)) {
                return $this->response->setJSON(['status' => 'success', 'message' => 'License Type deleted successfully']);
            } else {
                return $this->response->setStatusCode(500)->setJSON(['status' => 'error', 'message' => 'Failed to delete license type']);
            }
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function runMigration()
    {
        // Keep existing migration logic for emergency use
        echo "Please use command line for migrations.";
    }

    // ==================== SUPPORT SETTINGS ====================

    public function getSupportDetails()
    {
        try {
            $details = $this->supportModel->first();
            return $this->response->setJSON($details ?: []);
        } catch (\Exception $e) {
            return $this->response->setJSON([]);
        }
    }

    public function saveSupportDetails()
    {
        try {
            $data = $this->request->getJSON(true);
            
            if (empty($data)) {
                return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => 'No data provided']);
            }

            // Always update the first row as it's a single config table
            $existing = $this->supportModel->first();
            
            if ($existing) {
                $this->supportModel->update($existing['id'], $data);
            } else {
                $this->supportModel->insert($data);
            }

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Support details saved successfully'
            ]);

        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }
}
