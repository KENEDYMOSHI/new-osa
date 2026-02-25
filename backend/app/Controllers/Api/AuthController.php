<?php

namespace App\Controllers\Api;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\Shield\Models\UserModel;
use App\Models\PractitionerModel;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Libraries\SmsLibrary;
use App\Models\PractitionerPersonalInfoModel;

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

        // Check if phone number already exists
        $personalInfoModel = new \App\Models\PractitionerPersonalInfoModel();
        $existingPhone = $personalInfoModel->where('phone', $data['personalInfo']['phoneNumber'])->first();
        if ($existingPhone) {
             return $this->failValidationErrors(['personalInfo.phoneNumber' => 'This phone number is already registered within the system.']);
        }

        // Manual check for confirm password
        if (!isset($data['contactSecurity']['confirmPassword']) || 
            $data['contactSecurity']['password'] !== $data['contactSecurity']['confirmPassword']) {
             return $this->failValidationErrors(['confirmPassword' => 'Passwords do not match']);
        }

        // Determine User Type and Target Table
        $userType = isset($data['registrationType']) && $data['registrationType'] === 'pattern_approval' 
            ? 'pattern_approval' 
            : (isset($data['registrationType']) && $data['registrationType'] === 'customer' ? 'customer' : 'practitioner');
            
        $uuid = strtoupper(md5(uniqid(rand(), true))); // Uppercase 32 char hash
        $baseUsername = $data['personalInfo']['firstName'] . ' ' . $data['personalInfo']['lastName'];
        $uniqueUsername = $baseUsername . '_' . time();
        $db = \Config\Database::connect();

        // === Auto-resolve collection_center from region name ===
        $region = $data['personalInfo']['region'] ?? null;
        $resolvedCenterNumber = null;
        if ($region) {
            $centerRow = $db->table('collectioncenter')
                ->where('centerName', $region)
                ->get()->getRow();
            $resolvedCenterNumber = $centerRow->centerNumber ?? null;
        }
        
        try {
            if ($userType == 'pattern_approval' || $userType == 'customer') {
                // 1. Create User (Bypass Shield, Custom Tables)
                $tableName = $userType == 'pattern_approval' ? 'pattern_users' : 'customer_users';
                
                $db->table($tableName)->insert([
                    'uuid' => $uuid,
                    'username' => $uniqueUsername,
                    'email' => $data['contactSecurity']['email'],
                    'password_hash' => password_hash($data['contactSecurity']['password'], PASSWORD_BCRYPT),
                    'user_type' => $userType,
                    'registration_type' => $userType,
                    'phone_number' => $data['personalInfo']['phoneNumber'] ?? null,
                    'region' => $region,
                    'collection_center' => $resolvedCenterNumber,
                    'active' => 1,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
                
                $userId = $db->insertID();
            } else {
                // 1. Create User (Shield - License / Practitioner)
                $users = model(UserModel::class);
                $user = new \CodeIgniter\Shield\Entities\User([
                    'username' => $uniqueUsername,
                    'email'    => $data['contactSecurity']['email'],
                    'password' => $data['contactSecurity']['password'],
                ]);
                
                $users->save($user);
                $user = $users->findById($users->getInsertID());
                $userId = $user->id;

                $db->table('license_users')->where('id', $user->id)->update([
                    'uuid' => $uuid,
                    'user_type' => $userType,
                    'phone_number' => $data['personalInfo']['phoneNumber'] ?? null,
                    'region' => $region,
                    'collection_center' => $resolvedCenterNumber,
                ]);

                // Activate user immediately
                $user->activate();
                $users->save($user);
            }

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
                'ward' => $data['personalInfo']['ward'],
                'street' => $data['personalInfo']['street'],
                'phone' => $data['personalInfo']['phoneNumber'],
            ];
            $personalInfoModel->insert($personalInfoData);

            // 3. Create Practitioner Business Info
            $businessInfoModel = new \App\Models\PractitionerBusinessInfoModel();
            $businessInfoData = [
                'user_uuid' => $uuid, // Use UUID
                'tin' => $data['businessInfo']['tin'],
                'company_name' => $data['businessInfo']['companyName'] ?? null,
                'company_email' => $data['businessInfo']['companyEmail'] ?? null,
                'company_phone' => $data['businessInfo']['companyPhone'] ?? null,
                'brela_number' => $data['businessInfo']['brelaNumber'] ?? null,
                'bus_region' => $data['businessInfo']['region'],
                'bus_district' => $data['businessInfo']['district'],
                'bus_ward' => $data['businessInfo']['ward'],
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
                'uid' => $userId,
                'uuid' => $uuid,
                'email' => $data['contactSecurity']['email']
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

    public function checkPhone()
    {
        $phone = $this->request->getVar('phone');

        if (!$phone) {
            return $this->failValidationError('Phone number is required');
        }

        $personalInfoModel = new \App\Models\PractitionerPersonalInfoModel();
        $exists = $personalInfoModel->where('phone', $phone)->first();

        if ($exists) {
            return $this->respond(['exists' => true, 'message' => 'Phone number already registered']);
        }

        return $this->respond(['exists' => false]);
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
        $db = \Config\Database::connect();
        $key = getenv('JWT_SECRET') ?: 'your_secret_key_here';

        // === Step 1: Check pattern_users and customer_users first (custom tables) ===
        foreach (['pattern_users', 'customer_users'] as $tableName) {
            $customUser = $db->table($tableName)
                ->where('email', $data['email'])
                ->get()->getRow();

            if ($customUser) {
                // Verify hashed password
                if (!password_verify($data['password'], $customUser->password_hash)) {
                    log_message('info', 'Failed login for ' . $tableName . ': ' . $data['email']);
                    return $this->failUnauthorized('Invalid login credentials');
                }

                // ✅ Check if account is active
                if ((int)$customUser->active === 0) {
                    return $this->respond([
                        'status'  => 403,
                        'error'   => 'Account deactivated',
                        'message' => 'Your account has been deactivated. Please reset your password to regain access.'
                    ], 403);
                }

                // Generate JWT with the custom table's ID
                $payload = [
                    'iss'       => 'localhost',
                    'aud'       => 'localhost',
                    'iat'       => time(),
                    'exp'       => time() + 3600,
                    'uid'       => $customUser->id,
                    'uuid'      => $customUser->uuid,
                    'email'     => $customUser->email,
                    'user_type' => $customUser->user_type,
                    'table'     => $tableName
                ];

                $token = JWT::encode($payload, $key, 'HS256');

                log_message('info', 'Successful login (' . $tableName . ') for: ' . $data['email']);

                return $this->respond([
                    'message' => 'Login successful',
                    'token'   => $token,
                    'user'    => [
                        'id'        => $customUser->id,
                        'username'  => $customUser->username,
                        'email'     => $customUser->email,
                        'user_type' => $customUser->user_type
                    ]
                ]);
            }
        }

        // === Step 2: Fall back to license_users via Shield ===
        $users = model(UserModel::class);
        $user = $users->findByCredentials(['email' => $data['email']]);

        if (!$user) {
            return $this->failUnauthorized('Invalid login credentials');
        }

        // CRITICAL FIX: Manually verify password instead of using Shield's attempt()
        $identity = $db->table('auth_identities')
            ->where('user_id', $user->id)
            ->where('type', 'email_password')
            ->get()
            ->getRow();

        if (!$identity) {
            log_message('error', 'No password identity found for user: ' . $user->id);
            return $this->failUnauthorized('Invalid login credentials');
        }

        // Verify password using password_verify (bcrypt)
        if (!password_verify($data['password'], $identity->secret2)) {
            log_message('info', 'Failed login attempt for email: ' . $data['email']);
            return $this->failUnauthorized('Invalid login credentials');
        }

        // ✅ Check if license_users account is active
        $licenseUser = $db->table('license_users')->where('id', $user->id)->get()->getRow();
        if ($licenseUser && (int)$licenseUser->active === 0) {
            return $this->respond([
                'status'  => 403,
                'error'   => 'Account deactivated',
                'message' => 'Your account has been deactivated. Please reset your password to regain access.'
            ], 403);
        }

        // Password is correct - generate JWT
        $payload = [
            'iss'   => 'localhost',
            'aud'   => 'localhost',
            'iat'   => time(),
            'exp'   => time() + 3600,
            'uid'   => $user->id,
            'email' => $user->email
        ];

        $token = JWT::encode($payload, $key, 'HS256');

        // Get user_type from database
        $userRecord = $db->table('license_users')->where('id', $user->id)->get()->getRow();
        $userType = $userRecord->user_type ?? 'practitioner';

        log_message('info', 'Successful login for email: ' . $data['email']);

        return $this->respond([
            'message' => 'Login successful',
            'token'   => $token,
            'user'    => [
                'id'        => $user->id,
                'username'  => $user->username,
                'email'     => $user->email,
                'user_type' => $userType
            ]
        ]);
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
            $userRecord = $db->table('license_users')->where('id', $user->id)->get()->getRow();
            
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

            // Fetch Licenses
            $licenses = [];
            if (isset($user->id)) {
                 $db = \Config\Database::connect();
                 $builder = $db->table('license_applications');
                 // Select relevant fields, especially license_type from items
                 // Join with licenses table to get actual issue_date and expiry_date
                 $builder->select('
                    license_applications.id as app_id,
                    license_applications.created_at,
                    license_applications.valid_from,
                    license_applications.valid_to,
                    license_applications.license_number,
                    license_application_items.license_type,
                    license_application_items.status,
                    licenses.issue_date,
                    licenses.expiry_date as license_expiry_date
                 ');
                 $builder->join('license_application_items', 'license_application_items.application_id = license_applications.id');
                 $builder->join('licenses', 'licenses.application_id = license_applications.id', 'left');
                 $builder->where('license_applications.user_id', $user->id);
                 $builder->orderBy('license_applications.created_at', 'DESC');
                 $licenses = $builder->get()->getResult();
            }

            return $this->respond([
                'user' => [
                    'id' => $user->id,
                    'username' => $user->username,
                    'email' => $user->email,
                    'uuid' => $uuid
                ],
                'personalInfo' => $personalInfo,
                'businessInfo' => $businessInfo,
                'licenses' => $licenses
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
        $userRecord = $db->table('license_users')->where('id', $user->id)->get()->getRow();
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
        $userRecord = $db->table('license_users')->where('id', $user->id)->get()->getRow();
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
            'bus_ward'      => $data['ward'] ?? ($existing->bus_ward ?? null),
            'postal_code'   => $data['postalCode'] ?? ($existing->postal_code ?? null),
            'bus_street'    => $data['street'] ?? ($existing->bus_street ?? null),
            'tin'           => $data['tin'] ?? ($existing->tin ?? null),
            'seal_number'   => $data['sealNumber'] ?? ($existing->seal_number ?? null),
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

    public function uploadPicture()
    {
        $user = $this->getUserFromToken();

        if (!$user) {
            return $this->failUnauthorized('User not found or invalid token');
        }

        // Get UUID
        $db = \Config\Database::connect();
        $userRecord = $db->table('license_users')->where('id', $user->id)->get()->getRow();
        $uuid = $userRecord->uuid;

        // Validate file upload
        $file = $this->request->getFile('picture');
        
        if (!$file) {
            return $this->failValidationErrors(['picture' => 'No file uploaded']);
        }

        if (!$file->isValid()) {
            return $this->failValidationErrors(['picture' => 'Invalid file upload']);
        }

        // Validate file type
        $validTypes = ['image/jpeg', 'image/jpg', 'image/png'];
        if (!in_array($file->getMimeType(), $validTypes)) {
            return $this->failValidationErrors(['picture' => 'Only JPEG and PNG images are allowed']);
        }

        // Validate file size (max 2MB)
        $maxSize = 2 * 1024 * 1024; // 2MB
        if ($file->getSize() > $maxSize) {
            return $this->failValidationErrors(['picture' => 'File size must be less than 2MB']);
        }

        try {
            // Create upload directory if it doesn't exist
            $uploadPath = FCPATH . 'uploads/pictures/';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }

            // Generate unique filename
            $newName = $uuid . '_' . time() . '.' . $file->getExtension();
            
            // Move file to upload directory
            $file->move($uploadPath, $newName);

            // Update database
            $photoUrl = base_url('uploads/pictures/' . $newName);
            
            $personalInfoModel = new \App\Models\PractitionerPersonalInfoModel();
            $existing = $personalInfoModel->where('user_uuid', $uuid)->first();

            if ($existing) {
                // Delete old photo if exists
                if (!empty($existing->picture)) {
                    $oldPhotoPath = str_replace(base_url(), FCPATH, $existing->picture);
                    if (file_exists($oldPhotoPath)) {
                        unlink($oldPhotoPath);
                    }
                }
                
                // Update the picture field
                $db->table('practitioner_personal_infos')
                   ->where('id', $existing->id)
                   ->update(['picture' => $photoUrl]);
            } else {
                return $this->failNotFound('Personal info not found');
            }

            return $this->respond([
                'message' => 'Profile picture uploaded successfully',
                'picture_url' => $photoUrl
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Upload picture error: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            return $this->failServerError('Failed to upload photo: ' . $e->getMessage());
        }
    }

    public function forgotPassword()
    {
        $rules = [
            'phone' => 'required'
        ];

        if (!$this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $phone = $this->request->getVar('phone');

        // Normalize phone number (assuming format in DB matches or normalized)
        // 1. Find user by phone in PersonalInfo
        $personalInfoModel = new PractitionerPersonalInfoModel();
        // Try exact match or loose match? Let's assume exact first or simple cleanup
        // DB usually stores 255...
        $info = $personalInfoModel->where('phone', $phone)->first();

        if (!$info) {
             // Return success even if not found to prevent enumeration? 
             // User prompt: "verify that the phone number exists" implies explicit check.
             return $this->failNotFound('Phone number not registered in the system.');
        }

        // 2. Get User ID
        $db = \Config\Database::connect();
        // Handle $info as object (CodeIgniter Model return type)
        $uuid = is_array($info) ? $info['user_uuid'] : $info->user_uuid;
        
        $userRecord = $db->table('license_users')->where('uuid', $uuid)->get()->getRow();

        if (!$userRecord) {
             return $this->failNotFound('Linked user account not found.');
        }

        // 3. Generate Token (OTP)
        $otp = (string) rand(100000, 999999);
        $expiresAt = date('Y-m-d H:i:s', strtotime('+15 minutes'));

        // 4. Save to DB
        $db->table('password_resets')->insert([
            'user_id' => $userRecord->id,
            'token' => $otp,
            'expires_at' => $expiresAt,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        // 5. Send SMS
        $smsLib = new SmsLibrary();
        $message = "Your WMA-MIS Password Reset OTP is: " . $otp . ". Valid for 15 minutes.";
        $smsLib->sendSms($phone, $message);

        return $this->respond(['message' => 'OTP sent successfully to your phone number.']);
    }

    public function verifyResetOtp()
    {
        $rules = [
            'phone' => 'required',
            'otp' => 'required'
        ];

        if (!$this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $phone = $this->request->getVar('phone');
        $otp = $this->request->getVar('otp');

        $user = $this->getUserByPhone($phone);
        if (!$user) {
             return $this->failNotFound('Invalid phone number');
        }

        if ($this->validateOtp($user->id, $otp)) {
             return $this->respond(['valid' => true, 'message' => 'OTP is valid']);
        } else {
             return $this->fail('Invalid or expired OTP');
        }
    }

    public function resetPassword()
    {
        $rules = [
            'phone' => 'required',
            'otp' => 'required',
            'newPassword' => 'required|min_length[8]',
            'confirmPassword' => 'required|matches[newPassword]'
        ];

        if (!$this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $data = $this->request->getJSON(true) ?? $this->request->getVar(); // Handle both json and form
        // getVar returns object or array? request->getVar can fail for JSON body if not set up correctly in CI4 sometimes?
        // safer:
        $phone = $data['phone'] ?? $this->request->getVar('phone');
        $otp = $data['otp'] ?? $this->request->getVar('otp');
        $newPassword = $data['newPassword'] ?? $this->request->getVar('newPassword');

        $user = $this->getUserByPhone($phone);
        if (!$user) {
             return $this->failNotFound('Invalid phone number');
        }

        // Verify OTP again
        if (!$this->validateOtp($user->id, $otp)) {
             return $this->fail('Invalid or expired session/OTP');
        }

        // Check against current password (if possible to check hash without plain text?)
        // CI Shield users table stores hash.
        // We can't easily check "not same as previous" without verifying the hash of the new password against the old hash?
        // No, we can't 'verify' a new password string against an old hash unless we hash it and compare?
        // Shield uses VerifyPassword?
        // Actually, we can check if password_verify($newPassword, $user->password_hash).
        // Shield User Entity: $user->password_hash
        // Let's use auth service helper or standard password_verify.
        
        // Fetch full User Entity for Shield
        $usersModel = model(UserModel::class);
        $userEntity = $usersModel->findById($user->id);

        // Check if same (Shield uses specific hashing, but let's assume standard PHP verify works on the hash stored)
        // If Shield uses distinct hashing service, we should use that.
        // For now, simpler: Update logic.
        
        // Update Password
        $userEntity->fill([
            'password' => $newPassword
        ]);
        $usersModel->save($userEntity);

        // ✅ Re-activate account on successful password reset
        $db->table('license_users')->where('id', $user->id)->update([
            'active'     => 1,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        // Mark OTP as used
        $db->table('password_resets')
           ->where('user_id', $user->id)
           ->where('token', $otp)
           ->update(['used' => 1]);

        return $this->respond(['message' => 'Password has been changed successfully. Your account has been re-activated.']);
    }

    private function getUserByPhone($phone)
    {
        $personalInfoModel = new PractitionerPersonalInfoModel();
        $info = $personalInfoModel->where('phone', $phone)->first();
        if (!$info) return null;

        $db = \Config\Database::connect();
        $uuid = is_array($info) ? $info['user_uuid'] : $info->user_uuid;
        return $db->table('license_users')->where('uuid', $uuid)->get()->getRow();
    }

    private function validateOtp($userId, $otp)
    {
        $db = \Config\Database::connect();
        $record = $db->table('password_resets')
                     ->where('user_id', $userId)
                     ->where('token', $otp)
                     ->where('used', 0)
                     ->where('expires_at >=', date('Y-m-d H:i:s'))
                     ->orderBy('created_at', 'DESC')
                     ->get()->getRow();
        
        return $record != null;
    }
}
