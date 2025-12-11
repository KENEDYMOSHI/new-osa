<?php

namespace App\Controllers;

use App\Models\ContactModel;

class ContactController extends BaseController
{
    public function __construct()
    {
        helper(["url"]);
    }

    public function index()
    {
        // layout of add user form
        return view('contact_form');
    }

    public function create()
    {
        if ($this->request->getMethod() == "post") {

            $rules = [
                "name" => "required",
                "email" => "required|valid_email",
                "mobile" => "required"
            ];

            if (!$this->validate($rules)) {

                $response = [
                    'success' => false,
                    'msg' => "There are some validation errors",
                ];

                return $this->response->setJSON($response);
            } else {

                $userModel = new ContactModel();
                $req = service('request');

                $data = [
                    "name" => $req->getVar("name"),
                    "email" => $req->getVar("email"),
                    "mobile" => $req->getVar("mobile"),
                ];

                if ($userModel->saveData($data)) {

                    $response = [
                        'success' => true,
                        'msg' => "User created",
                    ];
                } else {
                    $response = [
                        'success' => true,
                        'msg' => "Failed to create user",
                    ];
                }

                return $this->response->setJSON($response);
            }
        }
    }
}
