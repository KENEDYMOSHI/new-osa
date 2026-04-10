<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use App\Models\EquipmentRegistrationModel;

class BusinessEquipmentController extends ResourceController
{
    protected $modelName = EquipmentRegistrationModel::class;
    protected $format    = 'json';

    public function index()
    {
        $userUuid = $this->request->user->uuid ?? null;
        if (!$userUuid) {
            return $this->failUnauthorized('User not authenticated');
        }

        $equipments = $this->model->where('user_uuid', $userUuid)->orderBy('created_at', 'DESC')->findAll();
        
        // decode JSON string back to array/object for the API response
        foreach($equipments as &$q) {
            if(is_string($q['equipment_data'])) {
                $q['equipment_data'] = json_decode($q['equipment_data'], true);
            }
        }

        return $this->respond($equipments);
    }

    public function create()
    {
        $userUuid = $this->request->user->uuid ?? null;
        if (!$userUuid) {
            return $this->failUnauthorized('User not authenticated');
        }

        $serviceTypeKey = $this->request->getPost('serviceTypeKey');
        $serviceTypeLabel = $this->request->getPost('serviceTypeLabel');
        $category = $this->request->getPost('category');
        $itemsJson = $this->request->getPost('items');
        
        if (!$serviceTypeKey || !$itemsJson) {
            return $this->failValidationErrors('Missing required fields (serviceTypeKey, items)');
        }

        $items = json_decode($itemsJson, true);
        if (!is_array($items) || count($items) === 0) {
            return $this->failValidationErrors('Items array is empty or invalid.');
        }

        $optionalFields = ['stickerNumber', 'sealNumber', 'serialNumber', 'lastCalibrationDate', 'nextCalibrationDate'];

        $savedCount = 0;
        
        foreach ($items as $index => $itemData) {
            // Check if ANY of the optional fields is missing or empty
            $isDraft = false;
            foreach ($optionalFields as $opt) {
                if (!isset($itemData[$opt]) || trim($itemData[$opt]) === '') {
                    $isDraft = true;
                    break;
                }
            }

            // Handle file uploads for this itemIndex
            // Payload field: `files[0][inspectionChart]`
            $uploadedFiles = $this->request->getFiles();
            if (isset($uploadedFiles['files'][$index])) {
                foreach ($uploadedFiles['files'][$index] as $fieldKey => $file) {
                    if ($file->isValid() && !$file->hasMoved()) {
                        $newName = $file->getRandomName();
                        // Put in public/uploads/equipments
                        $file->move(FCPATH . 'uploads/equipments', $newName);
                        // Store the web-accessible path in the JSON!
                        $itemData[$fieldKey] = 'uploads/equipments/' . $newName;
                    }
                }
            }

            $registration = [
                'user_uuid' => $userUuid,
                'service_type_key' => $serviceTypeKey,
                'service_type_label' => $serviceTypeLabel,
                'category' => $category,
                'equipment_data' => json_encode($itemData),
                'status' => $isDraft ? 'draft' : 'pending',
                // If it becomes pending immediately, we set submitted_at
                'submitted_at' => $isDraft ? null : date('Y-m-d H:i:s')
            ];

            $this->model->insert($registration);
            $savedCount++;
        }

        return $this->respondCreated([
            'status' => 201,
            'message' => "Successfully registered $savedCount equipment item(s)"
        ]);
    }

    public function show($id = null)
    {
        $userUuid = $this->request->user->uuid ?? null;
        $equipment = $this->model->find($id);

        if (!$equipment || $equipment['user_uuid'] !== $userUuid) {
            return $this->failNotFound('Equipment not found or unauthorized');
        }

        if(is_string($equipment['equipment_data'])) {
            $equipment['equipment_data'] = json_decode($equipment['equipment_data'], true);
        }

        return $this->respond($equipment);
    }

    // We can implement update and delete identically.
    public function update($id = null)
    {
        return $this->fail('Not implemented yet');
    }

    public function delete($id = null)
    {
        $userUuid = $this->request->user->uuid ?? null;
        $equipment = $this->model->find($id);

        if (!$equipment || $equipment['user_uuid'] !== $userUuid) {
            return $this->failNotFound('Equipment not found or unauthorized');
        }
        
        if ($equipment['status'] !== 'draft') {
            return $this->failValidationErrors('Can only delete draft equipments');
        }

        $this->model->delete($id);
        return $this->respondDeleted(['id' => $id]);
    }
}
