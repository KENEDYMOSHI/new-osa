<?php

namespace App\Controllers\Api;

use Config\Services;
use App\Models\ApiModel;
use App\Models\ProfileModel;
use App\Libraries\ApiLibrary;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\ResourceController;

class ApiAuthController extends ResourceController
{
    use ResponseTrait;
    protected $helpers = ['setting', 'bill'];

    public function __construct(

        protected $apiLibrary = new ApiLibrary()

    ) {
    }

    public function getVariable($var)
    {
        return $this->request->getVar($var, FILTER_SANITIZE_SPECIAL_CHARS);
    }
    public function login()
    {
        try {
            $deviceId = $this->getVariable('deviceId');
            //$posOkay = (new ApiModel())->verifyPos($deviceId);
            $posOkay = true;
            if ($posOkay) {
                //get validation rules
                $rules = setting('Validation.login');

                if (!$this->validate($rules)) {
                    return  $this->response->setJSON([
                        'status' => 0,
                        'data' => [
                            'errors' => $this->validator->getErrors(),
                        ]
                    ]);
                }

                $credentials = [
                    'email' => $this->getVariable('email'),
                    'password' => $this->getVariable('password'),
                ];

                if (auth()->loggedIn()) {
                    auth()->logout();
                }

                $loginAttempt = auth()->attempt($credentials);



                // Check if the user's account is activated before logging in
                if ($loginAttempt->isOK()) {
                    $user = auth()->user();

                    if (!$user->active) {
                        auth()->logout();

                        return $this->response->setJSON([
                            'status' => 0,
                            'data' => [
                                'msg' => 'Your account is not  Activated'
                            ]
                        ])->setStatusCode(401);
                    } else {
                        // Generate token and return to client
                        $token = auth()->user()->generateAccessToken('api-token')->raw_token;

                        $profileModel = new ProfileModel();
                        $minutes = rand(10, 60);

                        $logData = [
                            'name' => $user->first_name . ' ' . $user->last_name,
                            'region' => str_replace('Wakala Wa Vipimo', '', centerName()),
                            'email' => $user->email,
                            'sessionId' =>  $token,
                            'loginTime' => date('d-m-Y H:i:s'),
                            'logoutTime' => date("Y-m-d H:i:s", strtotime("+$minutes minutes", strtotime(date("Y-m-d H:i:s")))),
                            'ipAddress' => $this->request->getIPAddress(),
                            'userAgent' => $this->request->getUserAgent(),
                        ];
                        $profileModel->createLog($logData);

                        return $this->response->setJSON([
                            'status' => 1,
                            'data' => [
                                'token' => $token
                            ]
                        ]);
                    }
                } else {

                    return $this->response
                        ->setJSON([
                            'status' => 0,
                            'data' => [
                                'msg' => $loginAttempt->reason()
                            ]
                        ])
                        ->setStatusCode(401);
                }
            } else {
                auth()->logout();

                return $this->response->setJSON([
                    'status' => 0,
                    'data' => [
                        'msg' => 'Service Deactivated Contact ICT Support Team'
                    ]
                ])->setStatusCode(401);
            }
        } catch (\Throwable $th) {
            $response = [
                'status' => 0,
                'msg' => $th->getMessage(),
                'trace' => $th->getTrace(),
            ];
            return $this->response->setJSON($response)->setStatusCode(500);
        }
    }






    public function exit()
    {
        // $authHeader = Services::request()->getHeaderLine('Authorization');
        $authHeader = $this->request->getHeaderLine('Authorization');
        $token = str_replace('Bearer ', '', $authHeader);

        if (auth()->check(['token' => $token])) {

            return $this->response->setJSON([
                'status' => 1,
                'data' => $token,
                // 'header' => $authHeader
            ]);
            // Valid token
        } else {
            return $this->response->setJSON([
                'status' => 0,
                'message' => 'INVALID TOKEN',
                // 'header' => $authHeader
            ]);
        }
    }


    public function logout()
    {
        $authHeader = $this->request->getHeaderLine('Authorization');
        $token = str_replace('Bearer ', '', $authHeader);
        $profileModel = new ProfileModel();
        $profileModel->updateLog(['logoutTime' => date('d-m-Y H:i:s')], $token);
        auth()->logout();
        auth()->user()->revokeAllAccessTokens();
        return redirect()->to('api/noAuth');
    }

    /**
     * Return the editable properties of a resource object
     *
     * @return mixed
     */
    public function noAuth()
    {
        return  $this->response->setJSON([
            'status' => 401,
            'data' => [
                'error' => 'Unauthorized',
            ]
        ])->setStatusCode(401);
    }
}
