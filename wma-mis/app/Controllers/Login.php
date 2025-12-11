<?php

namespace App\Controllers;

use App\Models\UsersModel;
use App\Models\ProfileModel;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\Shield\Controllers\LoginController as ShieldLogin;
use PHPUnit\TextUI\Output\Printer;

class Login extends ShieldLogin
{
    public $loginModel;
    public $session;
    public $request;
    public $userModel;

    public function __construct()
    {

        $this->session = session();
        $this->userModel = new UsersModel();
        helper(['form', 'url', 'array', 'text', 'bill']);
    }

    public function index()
    {
        if (auth()->loggedIn()) {
            return redirect()->to('dashboard');
        }


        $data = [];
        $data['validation'] = null;
        $data['page'] = [
            'title' => 'Log In',
        ];


        return view('Pages/Auth/UserLogin', $data);
    }

    public static function checkPhoneNumber($user)
    {
        $phoneNumber = $user->phone_number;
        if (empty($phoneNumber)) {
            return false;
        } else {
            return true;
        }
    }
    public function loginAction(): RedirectResponse
    {
        try {



            $rules = $this->getValidationRules();

         

            // if (!$this->validate($rules)) {
            //     return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            //     Printer($credentials);
            //     exit;
            // }

            $credentials             = $this->request->getPost(setting('Auth.validFields'));
            $credentials             = array_filter($credentials);
            $credentials['password'] = $this->request->getPost('password');
            $remember                = (bool) $this->request->getPost('remember');

            /** @var Session $authenticator */
            $authenticator = auth('session')->getAuthenticator();

          

            $result = $authenticator->remember($remember)->attempt($credentials);

            $user = auth()->user();
            // Check if the user's account is activated before logging in
            if ($result->isOK()) {
                //  $user->forcePasswordReset();
                //   Printer($user->phone_number);
                //   exit;
                // if (!Login::checkPhoneNumber($user)) {
                //     return redirect()->to('updateMobile/'.$user->unique_id);
                // }

                // Printer($user);
                // exit;
              
                if ($user->requiresPasswordReset()) {
                    // Redirect the user to the password reset page
                    return redirect()->to('password/reset')->with('notice', 'You are required to reset your password.');
                }

                // printer($user);
                if (!$user->active) {
                    auth('session')->logout();
                    return redirect()->back()->withInput()->with('error', 'Your account is not  active contact administrator.');
                }
            }

           

            // exit;

            // Attempt to login
            if (!$result->isOK()) {

                $systemUser = $this->userModel->getUserByEmail($this->request->getPost('email'));



                $maxAttempts = 5;
                $attempts = session()->get('loginAttempts') ? session()->get('loginAttempts') + 1 : 1;
                session()->set('loginAttempts', $attempts);

                if ($attempts >= $maxAttempts) {
                    session()->remove('loginAttempts'); // Reset attempts after max is reached

                    if (!$systemUser) {
                        return redirect()->back()->withInput()->with('error', 'No account found with that email.');
                    }
                    $systemUser->deactivate();
                    return redirect()->to('login')->with('error', 'You have reached the maximum number of login attempts. Please contact administrator.');
                }
                return redirect()->back()->withInput()->with('error', 'Invalid Credentials');
            }
            // If an action has been defined for login, start it up.
            // if ($authenticator->hasAction()) {
            //     return redirect()->route('auth-action-show')->withCookies();
            // }

            $profileModel = new ProfileModel();
            $sessionId = randomString();
            session()->set('sessionId', $sessionId);
            $logData = [
                'name' => $user->first_name . ' ' . $user->last_name,
                'region' => str_replace('Wakala Wa Vipimo', '', centerName()),
                'email' => $user->email,
                'sessionId' =>  $sessionId,
                'loginTime' => date('Y-m-d H:i:s'),
                'ipAddress' => $this->request->getIPAddress(),
                'userAgent' => $this->request->getUserAgent(),
            ];
            $profileModel->createLog($logData);
            //return redirect()->to(setting('Auth.redirects')['login']);
            return redirect()->to('billCreation');
            // if (!Login::checkPhoneNumber($user)) {
            //         return redirect()->to('updateMobile/'.$user->unique_id);
            //        // return redirect()->to('fill');
            // }else{
            //     return redirect()->to('dashboard');
            // }


            // Validate here first, since some things,
            // like the password, can only be validated properly here.
            // session()->destroy();

        } catch (\Exception $e) {
            // Handle the exception and redirect to the login page with an error message
            return redirect()->to('login');
        }
    }


    public function updateMobile($hash)

    {
        try {
            // var_dump($hash);
            // exit;
            
            if (auth()->loggedIn() && !empty(auth()->user()->phone_number)) {
                return redirect()->to('/dashboard');
            }
            // else{
            //     return redirect()->to('/login');  
            // }


            $data = [];
            $data['validation'] = null;
            // $contact = $contact;
            $data['page'] = [
                'title'   => 'Update Mobile Number',
                'heading' => 'Update Mobile Number'
            ];
            if ($this->request->getMethod() == 'POST') {
                $rules = [

                    "mobileNumber"   => "required|min_length[10]|max_length[10]"
                ];

                $messages = [
                    'mobileNumber' => [
                        'required'   => 'Mobile number is required',
                        'min_length' => 'Mobile number must be exactly 10 digits',
                        'max_length' => 'Mobile number must be exactly 10 digits',

                    ]
                ];


                if ($this->validate($rules, $messages)) {

                    $mobileNumber = $this->request->getVar('mobileNumber');

                    // echo  $mobileNumber;
                    // echo  $hash;

                    // exit;
                    $request = (new ProfileModel())->updatePhoneNumber($mobileNumber, $hash);

                    if ($request) {

                        return redirect()->to('dashboard');
                    } else {
                        echo "text";
                    }
                } else {
                    $data['validation'] = $this->validator;
                }
            }
            return view('Pages/Auth/MobileNumber', $data);
        } catch (\Throwable $th) {
            print_r($th->getMessage());
            // session()->setFlashdata($th->getMessage());
            //  return redirect()->to("updateMobile/$hash");
        }
    }
}
