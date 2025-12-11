<?php

namespace App\Controllers;

use \CodeIgniter\Validation\Rules;

class FileUpload extends BaseController
{

        public function __construct()
        {
                helper(['form', 'url', 'array']);
        }

        public function index()
        {
                $data = [];
                $rules = [
                        'avatar' => 'uploaded[avatar]|max_size[avatar,1024]|ext_in[avatar,png,jpeg,jpg]'
                ];

                if ($this->request->getMethod() == 'POST') {
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


                return view('upload', $data);
        }
}