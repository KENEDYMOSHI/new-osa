<?php

// namespace App\Controllers;

// use App\Models\ProfileModel;

// class AccountController extends BaseController
// {

    
//     public $profileModel;
   
//     public $appRequest;
//     public function __construct()
//     {
//         $this->appRequest = service('request');
//         $this->profileModel = new ProfileModel();
     
//         helper(['form', 'url', 'array', 'date']);
//     }

//     public function getVariable($var)
//     {
//         return $this->appRequest->getVar($var, FILTER_SANITIZE_SPECIAL_CHARS);
//     }
 


//     public function activateAccount($hash)
    
//     {
//         // var_dump($hash);
//         // exit;
//         $data = [];
//         $data['validation'] = null;
//         $data['page'] = [
//             'title' => 'Account Activation',
//         ];
//         if ($this->appRequest->getMethod() == 'POST') {
//             $rules = [


//                 "password"=> "required|min_length[8]|max_length[20]|includeUpperCase[password]|includeLowerCase[password]|includeNumber[password]|includeSpecialChars[password]",
//                 "confirm-password" => "required|matches[password]",
//             ];


           

//             $errors = [
//                 'password' => [
//                     'includeUpperCase' => 'Password Must Contain  Uppercase Latter',
//                     'includeLowerCase' => 'Password Must Contain  Lowercase Latter',
//                     'includeNumber' => 'Password Must Contain  Number',
//                     'includeSpecialChars' => 'Password Must Contain  Special Character',
//                 ]
//             ];


//             if ($this->validate($rules,$errors)) {

//                 $password = $this->appRequest->getVar('password');
//                 $userData = [
//                     'password' => password_hash($password, PASSWORD_DEFAULT),
//                     'status' => 'active'
//                 ];
//                 // print_r($userData);
//                 // exit;


//                 $request = $this->profileModel->savePassword($hash, $userData);
//                 if ($request) {
//                     return redirect()->to('/login');
//                 }
//             } else {
//                 $data['validation'] = $this->validator;
//             }
//         }
//         return view('Pages/Auth/ActivationPage', $data);
//     }
// }
