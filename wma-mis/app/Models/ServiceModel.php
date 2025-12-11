<?php

namespace App\Models;

use CodeIgniter\Model;

class ServiceModel extends Model
{
    protected $serviceTable;
    protected $users;
    protected $db;
    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->serviceTable = $this->db->table('service_request');
        $this->users = $this->db->table('service_users');
    }

    //=================creating user account====================
    public function createAccount($data)
    {
        return  $this->users->insert($data);
    }
    public function saveServiceFormData($data)
    {
        return  $this->serviceTable->insertBatch($data);
    }

    public function getUser($hash)
    {
        return $this->users->select()->where(['hash' => $hash])->get()->getRow();
    }
    public function getServiceRequests($hash)
    {
        return $this->serviceTable->select()->where(['user_id' => $hash])->orderBy('id','DESC')->get()->getResult();
    }
    public function getSingleRequest($id)
    {
        return $this->serviceTable->select()->where(['id' => $id])->get()->getRow();
    }
    public function confirmServiceRequests($id)
    {
        return $this->serviceTable
        ->where(['id' => $id])
        ->update(['status' => '1']);
    }
    public function getServiceRequestsInRegion($region)
    {
        return $this->serviceTable->select()
        ->where(['region' => $region])
        ->orWhere(['district' => $region])
        ->orderBy('id','DESC')
        ->get()
        ->getResult();
    }

    //=================log the user in====================
    public function login()
    {
    }

    public function verifyEmail($email)
    {
        $builder = $this->users;
        $builder->select('hash,status,password,region');
        $builder->where('email', $email);
        $result =  $builder->get();

        if (count($result->getResultArray()) == 1) {
            return $result->getRowArray();
        }
    }
    public function verifyApplicantEmail($email)
    {
        return $this->users->select()->where('email', $email)->get()->getRow();

        
    }

    //=================Activating user account====================
    public function activateACCount($hash)
    {
        return $this->users->where(['hash' => $hash])->update(['status' =>'Active']);
    }
    public function updatePassword($hash,$password)
    {
        return $this->users->where(['hash' => $hash])->update(['password' =>$password]);
    }
}
