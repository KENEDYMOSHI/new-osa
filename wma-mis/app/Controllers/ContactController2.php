<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\ContactModel;

class ContactController2 extends Controller
{
    public $email;
    public function __construct()
    {
        $this->email = \Config\Services::email(); 
    }
    public function index()
    {
        helper('form');
        $ContactModel = new ContactModel();
        $data['users'] = $ContactModel->getData();
        return view('contact_form-2', $data);
    }

    public function create()
    {




        $ContactModel = new ContactModel();
        $req = service('request');
        $hash = md5(str_shuffle('Hello123CryptoBoomHash7950#898'));
        $data  = [
            'name' => $req->getVar('name'),
            'email'  => $req->getVar('email'),
            'mobile'  => $req->getVar('mobile'),
            'hash' => $hash
      ];


        $request =  $ContactModel->saveData($data);
        $session = session();

        if ($request) {
            $to = $data['email'];
            $subject = 'Account activation WMA';
            $message = 'Hello' . ' ' . $data['name'] . '<br><br><br>' .
                "Your account has been created successfully,please click the link below to" . "activate your account<br><br><br>" .
                "<a href='" . base_url() . "/signup/activate/" . $hash . "'>Activate Now</a>";

            // ================Email configurations==============
            $this->email->setTo($to);
            $this->email->setFrom('purposemany@gmail.com', 'WMA-MIS');
            $this->email->setSubject($subject);
            $this->email->setMessage($message);
            if ($this->email->send()) {
                $session->setFlashdata('Success', 'Data Added Successfully');
                return redirect()->to('newForm');
            }else{
                $session->setFlashdata('error', 'Something Went');
            }
        }
    }

    public function sendEmail($id, $name, $mail)
    {

        $to = $mail;
        $subject = 'Account activation WMA';
        $message = 'Hello' . ' ' . $name . '<br><br><br>' .
        "Your account has been created successfully,please click the link below to" . "activate your account<br><br><br>" .
        "<a href='" . base_url() . "/signup/activate/" . $id . "'>Activate Now</a>";

        // ================Email configurations==============
        $this->email->setTo($to);
        $this->email->setFrom('purposemany@gmail.com', 'WMA-MIS');
        $this->email->setSubject($subject);
        $this->email->setMessage($message);
        $this->email->send();
        // if ($this->email->send()) {
        //     return $this->response->setJSON([
        //         'msg' => 'Email Sent',
        //         // 'token' => $token
        //     ]);
        // }
    }
    public function updateRecord()
    {
       
        
        $token = csrf_hash();
        $ContactModel = new ContactModel();
        $req = service('request');
        $id = $req->getVar('id');
        $name = $req->getVar('name');
        $email  = $req->getVar('email');
        $mobile  = $req->getVar('mobile');
        $data = [
            'name' => $name,
            'email'  => $email,
            'mobile'  => $mobile,
        ];

        $request =  $ContactModel->updateData($id, $data);
         if ($request) {

            $to = $email;
            $subject = 'UPDATING ACCOUNT';
            $message = 'Hello' . ' ' . $name . '<br><br><br>' .
            "Your account has been created successfully,please click the link below to" . "activate your account<br><br><br>" .
            "<a href='" . base_url() . "/signup/activate/" . $id . "'>Activate Now</a>";

            // ================Email configurations==============
            $this->email->setTo($to);
            $this->email->setFrom('purposemany@gmail.com', 'WMA-MIS');
            $this->email->setSubject($subject);
            $this->email->setMessage($message);
            $this->email->send();
            $this->email->send();
        // if () {
            // $ContactModel->updateData($id, $data);
            // $mail = 'email sent';
          
        // }

            return $this->response->setJSON([
                'msg' => 'Record Updated',
                'token' => $token,
                
            ]);
            
            
        }
    }

    public function editRecord()
    {
        $req = service('request');
        $token = csrf_hash();
        $ContactModel = new ContactModel();
        $id  =  $req->getVar('id');
        $data = $ContactModel->getRecord($id);
        return $this->response->setJSON([
            'data' => $data,
            'token' => $token
        ]);
    }

 
}
