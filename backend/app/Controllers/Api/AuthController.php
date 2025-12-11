<?php

namespace App\Controllers\Api;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\Shield\Models\UserModel;
use App\Models\PractitionerModel;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthController extends ResourceController
{
    use ResponseTrait;

    private function getUserFromToken()
    {
        $header = $this->request->getHeaderLine('Authorization');
        if (empty($header)) {
            log_message('error', 'Auth: No Authorization header found.');
            return null;
        }

        if (preg_match('/Bearer\s(\S+)/', $header, $matches)) {
            $token = $matches[1];
        } else {
            log_message('error', 'Auth: Invalid header format: ' . $header);
            return null;
        }

        try {
            $key = getenv('JWT_SECRET') ?: 'your_secret_key_here';
            $decoded = JWT::decode($token, new Key($key, 'HS256'));
            
            $users = model(UserModel::class);
            $user = $users->findById($decoded->uid);
            
            if (!$user) {
                log_message('error', 'Auth: User not found for ID: ' . $decoded->uid);
            }
            
            return $user;
        } catch (\Firebase\JWT\ExpiredException $e) {
            log_message('error', 'Auth: Token expired: ' . $e->getMessage());
            return null;
        } catch (\Exception $e) {
            log_message('error', 'Auth: Token decoding failed: ' . $e->getMessage());
            return null;
        }
    }

    public function register()
    {
        $data = $this->request->getJSON(true); // Get as associative array

        $rules = [
            'contactSecurity.email' => [
                'rules' => 'required|valid_email|is_unique[auth_identities.secret]',
                'errors' => [
                    'is_unique' => 'This email address is already registered.'
                ]
            ],
            'contactSecurity.password' => 'required|min_length[8]',
            'personalInfo.nationality' => 'required',
            'personalInfo.identityNumber' => 'required',
            'personalInfo.phoneNumber' => 'required',
        ];

        $validation = \Config\Services::validation();
        $validation->setRules($rules);

        if (!$validation->run($data)) {
            return $this->failValidationErrors($validation->getErrors());
        }

        // Manual check for confirm password
        if (!isset($data['contactSecurity']['confirmPassword']) || 
            $data['contactSecurity']['password'] !== $data['contactSecurity']['confirmPassword']) {
             return $this->failValidationErrors(['confirmPassword' => 'Passwords do not match']);
        }

        // 1. Create User (Shield)
        $users = model(UserModel::class);
        $uuid = strtoupper(md5(uniqid(rand(), true))); // Uppercase 32 char hash

        $user = new \CodeIgniter\Shield\Entities\User([
            'username' => $data['personalInfo']['firstName'] . ' ' . $data['personalInfo']['lastName'],
            'email'    => $data['contactSecurity']['email'],
            'password' => $data['contactSecurity']['password'],
        ]);

        try {
            $users->save($user);
            $user = $users->findById($users->getInsertID());

            // Update UUID
            $db = \Config\Database::connect();
            $db->table('users')->where('id', $user->id)->update(['uuid' => $uuid]);

            // Activate user immediately
            $user->activate();

            // 2. Create Practitioner Personal Info
            $personalInfoModel = new \App\Models\PractitionerPersonalInfoModel();
            $personalInfoData = [
                'user_uuid' => $uuid, // Use UUID
                'nationality' => $data['personalInfo']['nationality'],
                'identity_number' => $data['personalInfo']['identityNumber'],
                'first_name' => $data['personalInfo']['firstName'],
                'second_name' => $data['personalInfo']['secondName'],
                'last_name' => $data['personalInfo']['lastName'],
                'gender' => $data['personalInfo']['gender'],
                'dob' => $data['personalInfo']['dateOfBirth'],
                'region' => $data['personalInfo']['region'],
                'district' => $data['personalInfo']['district'],
                'town' => $data['personalInfo']['town'],
                'street' => $data['personalInfo']['street'],
                'phone' => $data['personalInfo']['phoneNumber'],
            ];
            $personalInfoModel->insert($personalInfoData);

            // 3. Create Practitioner Business Info
            $businessInfoModel = new \App\Models\PractitionerBusinessInfoModel();
            $businessInfoData = [
                'user_uuid' => $uuid, // Use UUID
                'tin' => $data['businessInfo']['tin'],
                'company_name' => $data['businessInfo']['companyName'],
                'company_email' => $data['businessInfo']['companyEmail'],
                'company_phone' => $data['businessInfo']['companyPhone'],
                'brela_number' => $data['businessInfo']['brelaNumber'],
                'bus_region' => $data['businessInfo']['region'],
                'bus_district' => $data['businessInfo']['district'],
                'bus_town' => $data['businessInfo']['town'],
                'postal_code' => $data['businessInfo']['postalCode'],
                'bus_street' => $data['businessInfo']['street'],
            ];
            $businessInfoModel->insert($businessInfoData);

            // 4. Generate JWT
            $key = getenv('JWT_SECRET') ?: 'your_secret_key_here';
            $payload = [
                'iss' => 'localhost',
                'aud' => 'localhost',
                'iat' => time(),
                'exp' => time() + 3600, // 1 hour
                'uid' => $user->id,
                'uuid' => $uuid,
                'email' => $user->email
            ];

            $token = JWT::encode($payload, $key, 'HS256');

            return $this->respondCreated([
                'message' => 'User registered successfully',
                'token' => $token
            ]);
        } catch (\Exception $e) {
            return $this->failServerError($e->getMessage());
        }
    }

    public function login()
    {
        $rules = [
            'email' => 'required|valid_email',
            'password' => 'required'
        ];

        if (!$this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $data = $this->request->getJSON(true);

        // Authenticate using Shield
        $credentials = [
            'email'    => $data['email'],
            'password' => $data['password']
        ];

        $auth = service('auth');
        if ($auth->attempt($credentials)) {
            $user = $auth->user();

            // Generate JWT
            $key = getenv('JWT_SECRET') ?: 'your_secret_key_here';
            $payload = [
                'iss' => 'localhost',
                'aud' => 'localhost',
                'iat' => time(),
                'exp' => time() + 3600, // 1 hour
                'uid' => $user->id,
                'email' => $user->email
            ];

            $token = JWT::encode($payload, $key, 'HS256');

            return $this->respond([
                'message' => 'Login successful',
                'token' => $token,
                'user' => [
                    'id' => $user->id,
                    'username' => $user->username,
                    'email' => $user->email
                ]
            ]);
        } else {
            return $this->failUnauthorized('Invalid login credentials');
        }
    }

    public function me()
    {
        try {
            $user = $this->getUserFromToken();

            if (!$user) {
                return $this->failUnauthorized('User not found or invalid token');
            }

            // Get UUID from users table
            $db = \Config\Database::connect();
            $userRecord = $db->table('users')->where('id', $user->id)->get()->getRow();
            
            if (!$userRecord) {
                return $this->failNotFound('User record not found in database');
            }

            // Check if uuid column exists or is set
            if (!isset($userRecord->uuid)) {
                // Fallback or error? Let's return null UUID for now to avoid crash
                $uuid = null;
            } else {
                $uuid = $userRecord->uuid;
            }

            // Fetch Personal Info
            $personalInfo = null;
            $businessInfo = null;

            if ($uuid) {
                $personalInfoModel = new \App\Models\PractitionerPersonalInfoModel();
                $personalInfo = $personalInfoModel->where('user_uuid', $uuid)->first();

                // Fetch Business Info
                $businessInfoModel = new \App\Models\PractitionerBusinessInfoModel();
                $businessInfo = $businessInfoModel->where('user_uuid', $uuid)->first();
            }

            return $this->respond([
                'user' => [
                    'id' => $user->id,
                    'username' => $user->username,
                    'email' => $user->email,
                    'uuid' => $uuid
                ],
                'personalInfo' => $personalInfo,
                'businessInfo' => $businessInfo
            ]);
        } catch (\Exception $e) {
            return $this->failServerError('Server Error: ' . $e->getMessage());
        }
    }

    public function updatePersonalProfile()
    {
        $data = $this->request->getJSON(true);
        $user = $this->getUserFromToken();
        
        if (!$user) {
            return $this->failUnauthorized('User not found or invalid token');
        }

        // Get UUID
        $db = \Config\Database::connect();
        $userRecord = $db->table('users')->where('id', $user->id)->get()->getRow();
        $uuid = $userRecord->uuid;

        $personalInfoModel = new \App\Models\PractitionerPersonalInfoModel();
        $existing = $personalInfoModel->where('user_uuid', $uuid)->first();

        if ($existing) {
            $personalInfoModel->update($existing->id, $data);
        } else {
            $data['user_uuid'] = $uuid;
            $personalInfoModel->insert($data);
        }

        return $this->respond(['message' => 'Personal profile updated successfully']);
    }

    public function updateBusinessProfile()
    {
        $data = $this->request->getJSON(true);
        $user = $this->getUserFromToken();

        if (!$user) {
            return $this->failUnauthorized('User not found or invalid token');
        }

        // Get UUID
        $db = \Config\Database::connect();
        $userRecord = $db->table('users')->where('id', $user->id)->get()->getRow();
        $uuid = $userRecord->uuid;

        $businessInfoModel = new \App\Models\PractitionerBusinessInfoModel();
        $existing = $businessInfoModel->where('user_uuid', $uuid)->first();

        // Map frontend camelCase to backend snake_case
        $updateData = [
            'company_name'  => $data['companyName'] ?? ($existing->company_name ?? null),
            'brela_number'  => $data['brelaNumber'] ?? ($existing->brela_number ?? null),
            'company_email' => $data['companyEmail'] ?? ($existing->company_email ?? null),
            'company_phone' => $data['companyPhone'] ?? ($existing->company_phone ?? null),
            'bus_region'    => $data['region'] ?? ($existing->bus_region ?? null),
            'bus_district'  => $data['district'] ?? ($existing->bus_district ?? null),
            'bus_town'      => $data['town'] ?? ($existing->bus_town ?? null),
            'postal_code'   => $data['postalCode'] ?? ($existing->postal_code ?? null),
            'bus_street'    => $data['street'] ?? ($existing->bus_street ?? null),
            'tin'           => $data['tin'] ?? ($existing->tin ?? null),
        ];

        // Remove null values if you don't want to overwrite with null (optional, depending on requirement)
        // For now, we keep them as null if not provided and not existing, or update if provided.
        // Actually, the ?? logic above preserves existing if not provided in $data.

        if ($existing) {
            $businessInfoModel->update($existing->id, $updateData);
        } else {
            $updateData['user_uuid'] = $uuid;
            $businessInfoModel->insert($updateData);
        }

        return $this->respond(['message' => 'Business profile updated successfully']);
    }

    public function changePassword()
    {
        $data = $this->request->getJSON(true);
        $user = $this->getUserFromToken();

        if (!$user) {
            return $this->failUnauthorized('User not found or invalid token');
        }

        // Validation Rules
        $rules = [
            'currentPassword' => 'required',
            'newPassword'     => 'required|min_length[8]',
            'confirmPassword' => 'required|matches[newPassword]'
        ];

        $validation = \Config\Services::validation();
        $validation->setRules($rules);

        if (!$validation->run($data)) {
            return $this->failValidationErrors($validation->getErrors());
        }

        // Verify current password
        $credentials = [
            'email'    => $user->email,
            'password' => $data['currentPassword']
        ];

        $debugPath = WRITEPATH . 'logs/debug_auth.txt';
        $debugMsg = date('Y-m-d H:i:s') . " - Attempting auth for User ID: " . $user->id . ", Email: " . $user->email . "\n";
        file_put_contents($debugPath, $debugMsg, FILE_APPEND);
        
        $auth = service('auth');
        // Attempt to validate credentials
        $result = $auth->attempt($credentials);
        
        if (!$result) {
             file_put_contents($debugPath, "Auth attempt failed.\n", FILE_APPEND);
             return $this->failValidationErrors([
                 'currentPassword' => 'Incorrect current password for ' . $user->email . '. Received len: ' . strlen($data['currentPassword'])
             ]);
        }

        file_put_contents($debugPath, "Auth attempt successful.\n", FILE_APPEND);

        // Update password
        $users = model(UserModel::class);
        $userEntity = $users->findById($user->id);

        if (!$userEntity) {
             return $this->failNotFound('User not found');
        }
        
        $userEntity->fill([
            'password' => $data['newPassword']
        ]);
        $users->save($userEntity);

        return $this->respond(['message' => 'Password changed successfully']);
    }
}
