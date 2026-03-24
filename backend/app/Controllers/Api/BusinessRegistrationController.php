<?php

namespace App\Controllers\Api;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\ResourceController;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Models\BusinessOwnerInfoModel;
use App\Models\BusinessContactInfoModel;

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

    public function getProfile()
    {
        $user = $this->getUserFromToken();

        if (!$user) {
            return $this->failUnauthorized('Unauthorized');
        }

        try {
            $ownerInfo   = (new BusinessOwnerInfoModel())->where('user_uuid', $user->uuid)->first();
            $contactInfo = (new BusinessContactInfoModel())->where('user_uuid', $user->uuid)->first();

            return $this->respond([
                'user' => [
                    'id'           => $user->id,
                    'uuid'         => $user->uuid,
                    'email'        => $user->email,
                    'phone_number' => $user->phone_number,
                    'created_at'   => $user->created_at,
                    'last_active'  => $user->last_active,
                ],
                'businessOwnerInfo'  => $ownerInfo,
                'businessContactInfo'=> $contactInfo,
            ]);
        } catch (\Exception $e) {
            log_message('error', 'BusinessRegistrationController::getProfile error: ' . $e->getMessage());
            return $this->failServerError('Failed to load profile');
        }
    }

    public function updateBusinessInfo()
    {
        $user = $this->getUserFromToken();

        if (!$user) {
            return $this->failUnauthorized('Unauthorized');
        }

        $data = $this->request->getJSON(true);

        try {
            $ownerModel = new BusinessOwnerInfoModel();
            $existing   = $ownerModel->where('user_uuid', $user->uuid)->first();

            if (!$existing) {
                return $this->failNotFound('Business info not found');
            }

            # Only allow updates when pending
            if (($existing->verification_status ?? 'pending') !== 'pending') {
                return $this->fail('Business info can only be edited while verification is pending', 403);
            }

            $ownerData = [
                'company_name'            => $data['companyName']           ?? $existing->company_name,
                'business_type'           => $data['businessType']          ?? $existing->business_type,
                'tin'                     => $data['tin']                   ?? $existing->tin,
                'business_license_number' => $data['businessLicenseNumber'] ?? $existing->business_license_number,
                'postal_address'          => $data['postalAddress']         ?? $existing->postal_address,
                'office_phone_number'     => $data['officePhoneNumber']     ?? $existing->office_phone_number,
                'region'                  => $data['region']                ?? $existing->region,
                'district'                => $data['district']              ?? $existing->district,
                'ward'                    => $data['ward']                  ?? $existing->ward,
                'postal_code'             => $data['postalCode']            ?? $existing->postal_code,
                'owner_first_name'        => $data['ownerFirstName']        ?? $existing->owner_first_name,
                'owner_second_name'       => $data['ownerSecondName']       ?? $existing->owner_second_name,
                'owner_last_name'         => $data['ownerLastName']         ?? $existing->owner_last_name,
                'owner_phone_number'      => $data['ownerPhoneNumber']      ?? $existing->owner_phone_number,
                'owner_email_address'     => $data['ownerEmailAddress']     ?? $existing->owner_email_address,
                'owner_postal_address'    => $data['ownerPostalAddress']    ?? $existing->owner_postal_address,
            ];

            $ownerModel->update($existing->id, $ownerData);

            # Update contact info if provided
            if (isset($data['contactFirstName']) || isset($data['contactLastName']) || isset($data['contactPhone'])) {
                $contactModel   = new BusinessContactInfoModel();
                $existingContact = $contactModel->where('user_uuid', $user->uuid)->first();

                $contactData = [
                    'first_name'               => $data['contactFirstName']        ?? ($existingContact->first_name ?? null),
                    'second_name'              => $data['contactSecondName']       ?? ($existingContact->second_name ?? null),
                    'last_name'                => $data['contactLastName']         ?? ($existingContact->last_name ?? null),
                    'designation'              => $data['contactDesignation']      ?? ($existingContact->designation ?? null),
                    'phone_number'             => $data['contactPhone']            ?? ($existingContact->phone_number ?? null),
                    'alternative_phone_number' => $data['contactAlternativePhone'] ?? ($existingContact->alternative_phone_number ?? null),
                    'email_address'            => $data['contactEmail']            ?? ($existingContact->email_address ?? null),
                ];

                if ($existingContact) {
                    $contactModel->update($existingContact->id, $contactData);
                } else {
                    $contactData['user_uuid'] = $user->uuid;
                    $contactModel->insert($contactData);
                }
            }

            return $this->respond(['message' => 'Business information updated successfully']);
        } catch (\Exception $e) {
            log_message('error', 'updateBusinessInfo error: ' . $e->getMessage());
            return $this->failServerError('Failed to update business information');
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
