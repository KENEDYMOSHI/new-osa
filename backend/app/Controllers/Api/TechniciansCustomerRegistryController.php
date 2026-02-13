<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Shield\Models\UserModel;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Models\PractitionerPersonalInfoModel;
use App\Models\PractitionerBusinessInfoModel;

class TechniciansCustomerRegistryController extends ResourceController
{
    use ResponseTrait;

    public function getProfile()
    {
        $user = $this->getUserFromToken();
        if (!$user) {
            return $this->failUnauthorized('Invalid or missing token');
        }

        try {
            $db = \Config\Database::connect();
            $userRecord = $db->table('users')->where('id', $user->id)->get()->getRow();
            
            if (!$userRecord || !isset($userRecord->uuid)) {
                return $this->failNotFound('User profile not found');
            }

            $uuid = $userRecord->uuid;

            $personalInfoModel = new PractitionerPersonalInfoModel();
            $personalInfo = $personalInfoModel->where('user_uuid', $uuid)->first();

            $businessInfoModel = new PractitionerBusinessInfoModel();
            $businessInfo = $businessInfoModel->where('user_uuid', $uuid)->first();

            $licenseModel = new \App\Models\LicenseModel();
            // Assuming applicant_id in licenses table corresponds to user_uuid
            $license = $licenseModel->where('applicant_id', $uuid)
                                    ->orderBy('created_at', 'DESC')
                                    ->first();

            return $this->respond([
                'personal' => $personalInfo,
                'business' => $businessInfo,
                'license'  => $license
            ]);
        } catch (\Exception $e) {
            return $this->failServerError($e->getMessage());
        }
    }

    public function index()
    {
        $user = $this->getUserFromToken();
        if (!$user) {
            return $this->failUnauthorized('Invalid token');
        }

        $model = new \App\Models\TechniciansRegistryModel();
        $data = $model->getRegistryByUser($user->id);
        return $this->respond($data);
    }

    public function create()
    {
        $user = $this->getUserFromToken();
        if (!$user) {
            return $this->failUnauthorized('Invalid token');
        }

        $data = $this->request->getJSON(true);
        if (!$data) {
            return $this->fail('No data provided');
        }

        $data['user_id'] = $user->id;

        $model = new \App\Models\TechniciansRegistryModel();
        
        if ($model->save($data)) {
            return $this->respondCreated(['message' => 'Record created successfully', 'id' => $model->getInsertID(), 'data' => $data]);
        } else {
            return $this->failValidationErrors($model->errors());
        }
    }

    public function update($id = null)
    {
        $user = $this->getUserFromToken();
        if (!$user) {
            return $this->failUnauthorized('Invalid token');
        }

        $model = new \App\Models\TechniciansRegistryModel();
        $record = $model->find($id);

        if (!$record) {
            return $this->failNotFound('Record not found');
        }

        if ($record['user_id'] != $user->id) {
            return $this->failForbidden('You do not have permission to update this record');
        }

        $data = $this->request->getJSON(true);
        // Ensure ID is not overwritten in a way that causes issues, though update() handles it.
        
        if ($model->update($id, $data)) {
             // Return the updated data so the frontend can use it
            return $this->respond(['message' => 'Record updated successfully', 'data' => array_merge($record, $data)]);
        } else {
            return $this->failValidationErrors($model->errors());
        }
    }

    public function delete($id = null)
    {
        $user = $this->getUserFromToken();
        if (!$user) {
            return $this->failUnauthorized('Invalid token');
        }

        $model = new \App\Models\TechniciansRegistryModel();
        $record = $model->find($id);

        if (!$record) {
            return $this->failNotFound('Record not found');
        }

        if ($record['user_id'] != $user->id) {
            return $this->failForbidden('You do not have permission to delete this record');
        }

        if ($model->delete($id)) {
            return $this->respondDeleted(['message' => 'Record deleted successfully']);
        } else {
            return $this->fail('Failed to delete record');
        }
    }

    private function getUserFromToken()
    {
        $header = $this->request->getHeaderLine('Authorization');
        if (empty($header)) return null;

        if (preg_match('/Bearer\s(\S+)/', $header, $matches)) {
            $token = $matches[1];
        } else {
            return null;
        }

        try {
            $key = getenv('JWT_SECRET') ?: 'your_secret_key_here';
            $decoded = JWT::decode($token, new Key($key, 'HS256'));
            $users = model(UserModel::class);
            return $users->findById($decoded->uid);
        } catch (\Exception $e) {
            return null;
        }
    }
}
