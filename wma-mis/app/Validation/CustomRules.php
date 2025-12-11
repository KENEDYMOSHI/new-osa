<?php

namespace App\Validation;


class CustomRules
{
    public $login = [
       
        'email' => [
            'label' =>  'Auth.email',
            'rules' => 'required|max_length[254]|valid_email',
        ],
        'password' => [
            'label' =>  'Auth.password',
            'rules' => 'required',
        ],
    ];

    // Rule is to validate password upper case characters
    public function includeUpperCase(string $str, string $fields, array $data)
    {


        if (preg_match('/[A-Z]/', $data['password'])) {
            return true;
        } else {

            return false;
        }
    }
    // Rule is to validate password lower case characters
    public function includeLowerCase(string $str, string $fields, array $data)
    {


        if (preg_match('/[a-z]/', $data['password'])) {
            return true;
        } else {

            return false;
        }
    }
    // Rule is to validate password lower case characters
    public function includeNumber(string $str, string $fields, array $data)
    {


        if (preg_match('/[0-9]/', $data['password'])) {
            return true;
        } else {

            return false;
        }
    }
    // Rule is to validate password lower case characters
    public function includeSpecialChars(string $str, string $fields, array $data)
    {


        if (preg_match('/[!?@#$%^&*()\-_=+{};:,<.>]/', $data['password'])) {
            return true;
        } else {

            return false;
        }
    }
  

    public function isValidEmail(string $str, string $fields, array $data)
    {
        $email = $data['email'];
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return true;
        } else {
            return false;
        }
    }
}
