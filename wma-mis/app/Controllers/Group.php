<?php

namespace App\Controllers;


class Group extends BaseController
{

        public function __construct()
        {
        }
        public function multicheck()
        {
                $this->load->view('multicheck_insert');
                if (isset($_POST['save'])) {
                        $user_id = 1; //Pass the userid here
                        $checkbox = $_POST['check'];
                        for ($i = 0; $i < count($checkbox); $i++) {
                                $category_id = $checkbox[$i];
                                $this->Crud_model->multisave($user_id, $category_id); //Call the modal

                        }
                        echo "Data added successfully!";
                }
        }
}