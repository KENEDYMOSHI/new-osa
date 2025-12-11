<?php

namespace App\Controllers;

use App\Models\CertificateModel;
use App\Models\ManagerModel;
use App\Models\ProfileModel;
use CodeIgniter\Shield\Models\UserModel;

class Profile extends BaseController
{

    protected $session;
    protected $managerModel;
    protected $profileModel;
    protected $uniqueId;
    protected $managerId;
   
    protected $collectionCenter;
    protected $userModel;
    protected $user;
    public function __construct()
    {
        $this->profileModel = new ProfileModel();
        $this->managerModel = new ManagerModel();
        $this->userModel = new UserModel();
        $this->session = session();
        $this->uniqueId =  auth()->user()->unique_id;
        $this->collectionCenter = auth()->user()->collection_center;
        $this->user = auth()->user();
        helper(['form', 'url', 'array', 'date']);
    }

    public function getVariable($var)
    {
        return $this->request->getVar($var, FILTER_SANITIZE_SPECIAL_CHARS);
    }
    public function index()
    {



        $uniqueId = $this->uniqueId;
        $data['profile'] = $this->profileModel->getLoggedUserData($uniqueId);

        $data['tasks'] = $this->managerModel->getMyTask($uniqueId);
        $data['user'] = auth()->user();

        $data['page'] = [
            'title' => 'User Profile',
            'heading' => 'User Profile',
        ];

        // ==============================

        $rules = [
            'avatar' => 'uploaded[avatar]|max_size[avatar,1024]|ext_in[avatar,png,jpeg,jpg]',
        ];

        if ($this->request->getMethod() == 'post') {
            if ($this->validate($rules)) {
                $file = $this->request->getFile('avatar');
                if ($file->isValid() && !$file->hasMoved()) {
                    $randomName = $file->getRandomName();
                    if ($file->move(WRITEPATH . 'uploads/avatars/', $randomName)) {

                        $path = 'writable/uploads/avatars/' .   $randomName;

                        $upload = $this->profileModel->updateAvatar($path, $this->uniqueId);
                        if ($upload) {
                            $this->session->setFlashdata('Success', 'Profile Picture Updated');
                            return redirect()->to(current_url());
                        } else {
                            $this->session->setFlashdata('error', 'Fail to update profile picture');
                            return redirect()->to(current_url());
                        }
                    }
                } else {

                    $this->session->setFlashdata('error', $file->getErrorString() . '' . $file->getError());
                }
            } else {
                $data['validation'] = $this->validator;
            }
        }
        $signature = (new CertificateModel())->getSignPath($this->uniqueId);

        // Ensure $signature->file is not null or empty before calling file_exists.
        $data['sign'] = (!empty($signature->file) && file_exists($signature->file)) 
            ? $signature->file 
            : 'sign/default.png';
        return view('Pages/profile/userProfile', $data);
    }
    public function changePassword()
    {
        $data = [];
        $data['validation'] = null;
        $data['page'] = [
            'title' => 'Change Password',
            'heading' => 'Change Password',
        ];





        $data['pass'] = 222;

        if ($this->request->getMethod() == 'post') {
            $rules = [

                'oldPassword' => 'required|min_length[6]|max_length[15]',
                'password' => 'required|min_length[6]|max_length[20]|includeUpperCase[password]|includeLowerCase[password]|includeNumber[password]|includeSpecialChars[password]',
                'confirmNewPassword' => 'required|matches[password]',
            ];



            $errors = [
                'password' => [
                    'includeUpperCase' => 'Password Must Contain  Uppercase Latter',
                    'includeLowerCase' => 'Password Must Contain  Lowercase Latter',
                    'includeNumber' => 'Password Must Contain  Number',
                    'includeSpecialChars' => 'Password Must Contain  Special Character',
                ]
            ];




            if ($this->validate($rules,  $errors)) {
                $oldPassword = $this->getVariable('oldPassword');
                $newPassword = $this->getVariable('password');

                $result = auth()->check([
                    'email'    => $this->user->email,
                    'password' => $oldPassword,
                ]);

                if ($result->isOK()) {

                    $request = $this->profileModel->updatePassword($this->user->id, password_hash($newPassword, PASSWORD_DEFAULT));

                    if ($request) {

                        $this->session->setFlashData('Success', 'Password Updated Successfully');
                        return redirect()->to('dashboard');
                    }
                } else {
                    $this->session->setFlashData('error', 'Invalid Old Password');
                }


                // exit;
            } else {
                $data['validation'] = $this->validator;
            }
        }
        return view('Pages/Auth/changePassword', $data);
    }

    public function confirmTask($id)
    {

        //$id  = $this->request->getVar('id');
        $uniqueId = $this->uniqueId;
        $taskData = $this->managerModel->getMyTask($uniqueId);

        $this->managerModel->confirmTask($id);
        $this->session->setFlashdata('Success', 'Task Confirmed');
        // return redirect()->to('/listBulkStorageTanks');

        return redirect()->to('/profile');
    }

    public function managerProfile()
    {


        $uniqueId = $this->uniqueId;
        $data['profile'] = $this->profileModel->getLoggedUserData($uniqueId);
       
        // $data['tasks'] = $this->managerModel->getMyTask($uniqueId);

        $data['page'] = [
            'title' => 'User Profile',
            'heading' => 'User Profile',
        ];

        // ==============================

        $rules = [
            'avatar' => 'uploaded[avatar]|max_size[avatar,1024]|ext_in[avatar,png,jpeg,jpg]',
        ];

        if ($this->request->getMethod() == 'post') {
            if ($this->validate($rules)) {
                $file = $this->request->getFile('avatar');
                if ($file->isValid() && !$file->hasMoved()) {
                    $randomName = $file->getRandomName();
                    if ($file->move(FCPATH . 'public/uploads/avatars/', $randomName)) {

                        $path = base_url() . '/public/uploads/avatars/' . $randomName;
                        $upload = $this->profileModel->updateAvatar($path,  $this->uniqueId);
                        if ($upload) {
                            $this->session->setFlashdata('Success', 'Profile Picture Updated');
                            return redirect()->to(current_url());
                        } else {
                            $this->session->setFlashdata('error', 'Fail to update profile picture');
                            return redirect()->to(current_url());
                        }
                    }
                } else {

                    $this->session->setFlashdata('error', $file->getErrorString() . '' . $file->getError());
                }
            } else {
                $data['validation'] = $this->validator;
            }
        }
        return view('Pages/profile/userprofile', $data);
    }

    public function directorProfile()
    {


        // $director = $this->director;
        $data['profile'] = $this->profileModel->getLoggedUserData($this->uniqueId);
    
        $data['user'] = auth()->user();

        $data['page'] = [
            'title' => 'User Profile',
            'heading' => 'User Profile',
        ];

        // ==============================

        $rules = [
            'avatar' => 'uploaded[avatar]|max_size[avatar,1024]|ext_in[avatar,png,jpeg,jpg]',
        ];

        if ($this->request->getMethod() == 'post') {
            if ($this->validate($rules)) {
                $file = $this->request->getFile('avatar');
                if ($file->isValid() && !$file->hasMoved()) {
                    $randomName = $file->getRandomName();
                    if ($file->move(FCPATH . 'public/uploads/avatars/', $randomName)) {

                        $path = base_url() . '/public/uploads/avatars/' . $randomName;
                        $upload = $this->profileModel->updateAvatar($path,  $this->uniqueId);
                        if ($upload) {
                            $this->session->setFlashdata('Success', 'Profile Picture Updated');
                            return redirect()->to(current_url());
                        } else {
                            $this->session->setFlashdata('error', 'Fail to update profile picture');
                            return redirect()->to(current_url());
                        }
                    }
                } else {

                    $this->session->setFlashdata('error', $file->getErrorString() . '' . $file->getError());
                }
            } else {
                $data['validation'] = $this->validator;
            }
        }
        return view('Pages/profile/userprofile', $data);
    }

    public function avatar()
    {

        $rules = [
            'avatar' => 'uploaded[avatar]|max_size[avatar,1024]|ext_in[avatar,png,jpeg,jpg]',
        ];

        if ($this->request->getMethod() == 'post') {
            if ($this->validate($rules)) {
                $file = $this->request->getFile('avatar');
                if ($file->isValid() && !$file->hasMoved()) {
                    $newName = $file->getRandomName();
                    $file->move(WRITEPATH . 'uploads/avatars/', $newName);

                    echo "file uploaded";
                } else {
                    $file->getErrorString() . '' . $file->getError();
                }
            } else {
                $data['validation'] = $this->validator;
            }
        }

        return view('Pages/profile/userprofile', $data);
    }
    public function logout()
    {
        
        $sessionId = session()->get('sessionId');
        $this->profileModel->updateLog(['logoutTime' => date('d-m-Y H:i:s')],$sessionId);
        auth()->logout();
        return redirect()->to('/');
    }

    public function destroySession()
    {
        if (!auth()->loggedIn()) {
            return redirect()->to('/');
        } 
        
        $sessionId = session()->get('sessionId');
        $this->profileModel->updateLog(['logoutTime' => date('d-m-Y H:i:s')],$sessionId);
        auth()->logout();
        return  $this->response->setJSON([
          'status' => 1,
          'loggedOut' => 'ok',
        ]);
    }
  


    public function activateAccount($hash)
    {
        $data = [];
        $data['validation'] = null;
        $data['page'] = [
            'title' => 'Account Activation',
        ];
        if ($this->request->getMethod() == 'post') {
            $rules = [


                "password"        => "required|min_length[6]|max_length[20]|includeUpperCase[password]|includeLowerCase[password]|includeNumber[password]|includeSpecialChars[password]",
                "confirm-password" => "required|matches[password]",
            ];


            $errors = [
                'password' => [
                    'includeUpperCase' => 'Password Must Contain  Uppercase Latter',
                    'includeLowerCase' => 'Password Must Contain  Lowercase Latter',
                    'includeNumber' => 'Password Must Contain  Number',
                    'includeSpecialChars' => 'Password Must Contain  Special Character',
                ]
            ];


            if ($this->validate($rules, $errors)) {

                $password = $this->request->getVar('password');
                $userData = [
                    'password' => password_hash($password, PASSWORD_DEFAULT),
                    'status' => 'active'
                ];
                // print_r($userData);
                // exit;


                $request = $this->profileModel->savePassword($hash, $userData);
                if ($request) {
                    return redirect()->to('/');
                }
            } else {
                $data['validation'] = $this->validator;
            }
        }

        return view('Pages/Auth/ActivationPage', $data);
    }

    public function getCenterDetails()
    {
    }
}
