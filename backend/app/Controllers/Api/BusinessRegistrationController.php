<?php

namespace App\Controllers\Api;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\ResourceController;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Models\BusinessOwnerInfoModel;

class BusinessRegistrationController extends ResourceController
{
    use ResponseTrait;

    private function getUserFromToken()
    {
        $header = $this->request->getHeaderLine('Authorization');
        if (empty($header)) return null;

        if (!preg_match('/Bearer\s(\S+)/', $header, $matches)) return null;

        try {
            $key = getenv('JWT_SECRET') ?: 'your_secret_key_here';
            $decoded = JWT::decode($matches[1], new Key($key, 'HS256'));

            if (isset($decoded->table) && $decoded->table === 'business_users') {
                $db = \Config\Database::connect();
                return $db->table('business_users')->where('id', $decoded->uid)->get()->getRow();
            }

            return null;
        } catch (\Exception $e) {
            log_message('error', 'BusinessRegistrationController token error: ' . $e->getMessage());
            return null;
        }
    }

    public function uploadLogo()
    {
        $user = $this->getUserFromToken();

        if (!$user) {
            return $this->failUnauthorized('Unauthorized');
        }

        $file = $this->request->getFile('logo');

        if (!$file || !$file->isValid()) {
            return $this->failValidationErrors(['logo' => 'No valid file uploaded']);
        }

        $validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
        if (!in_array($file->getMimeType(), $validTypes)) {
            return $this->failValidationErrors(['logo' => 'Only JPEG, PNG, and WebP images are allowed']);
        }

        if ($file->getSize() > 1 * 1024 * 1024) {
            return $this->failValidationErrors(['logo' => 'File size must be less than 1MB']);
        }

        try {
            $uploadPath = FCPATH . 'uploads/business-logos/';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }

            $model = new BusinessOwnerInfoModel();
            $existing = $model->where('user_uuid', $user->uuid)->first();

            if (!$existing) {
                return $this->failNotFound('Business info not found');
            }

            # Delete old logo if exists
            if (!empty($existing->business_logo)) {
                $oldPath = str_replace(base_url(), FCPATH, $existing->business_logo);
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }

            $newName = $user->uuid . '_logo_' . time() . '.' . $file->getExtension();
            $file->move($uploadPath, $newName);

            $logoUrl = base_url('uploads/business-logos/' . $newName);

            $model->where('user_uuid', $user->uuid)->set(['business_logo' => $logoUrl])->update();

            return $this->respond([
                'message' => 'Business logo uploaded successfully',
                'logo_url' => $logoUrl,
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Upload business logo error: ' . $e->getMessage());
            return $this->failServerError('Failed to upload logo');
        }
    }

    public function removeLogo()
    {
        $user = $this->getUserFromToken();

        if (!$user) {
            return $this->failUnauthorized('Unauthorized');
        }

        try {
            $model = new BusinessOwnerInfoModel();
            $existing = $model->where('user_uuid', $user->uuid)->first();

            if (!$existing) {
                return $this->failNotFound('Business info not found');
            }

            if (!empty($existing->business_logo)) {
                $oldPath = str_replace(base_url(), FCPATH, $existing->business_logo);
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }

            $model->where('user_uuid', $user->uuid)->set(['business_logo' => null])->update();

            return $this->respond(['message' => 'Business logo removed successfully']);
        } catch (\Exception $e) {
            log_message('error', 'Remove business logo error: ' . $e->getMessage());
            return $this->failServerError('Failed to remove logo');
        }
    }
}
