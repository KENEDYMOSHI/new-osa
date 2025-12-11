<?php

namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\Shield\Entities\User;

class AdminModel extends Model
{
    public $db;
    public $usersTable;
    public $center;
    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->usersTable = $this->db->table('users');
        $this->center = $this->db->table('collectioncenter');
    }

    public function getLoggedUserData($id)
    {
        $builder = $this->usersTable;
        $builder->where('unique_id', $id);
        $result = $builder->get();
        if (count($result->getResultArray()) == 1) {
            return $result->getRow();
        } else {
            return false;
        }
    }



    public function updateAvatar($path, $id)
    {

        return $this->usersTable
            ->where(['unique_id' => $id])
            ->set(['avatar' => $path])
            ->update();
    }
    public function updatePassword($id, $password)
    {

        return $this->usersTable
            ->where(['unique_id' => $id])
            ->set(['password' => $password])
            ->update();
    }

    public function createUser($data)
    {
        return $this->usersTable->insert($data);
    }

    public function getAllUsers()
    {
        return $this->usersTable
            ->select('users.*, centerName')
            ->join('collectioncenter', 'collectioncenter.centerNumber = users.collection_center')
            ->get()
            ->getResult(User::class);
    }
    
    public function getUsers($id): User
    {
        $user = $this->usersTable->select()
        ->where(['unique_id' => $id])
        ->join('collectioncenter', 'collectioncenter.centerNumber = users.collection_center')
        ->get()
        ->getRowArray();

        return new User($user);
    }

    public function changeStatus($id, $status)
    {
        return $this->usersTable
            ->where(['unique_id' => $id])
            ->set(['status' => $status])
            ->update();
    }
    public function activateAccount($id)
    {
        return $this->usersTable
            ->where(['unique_id' => $id])
            ->set(['status' => 'active'])
            ->update();
    }
    public function deactivateAccount($id)
    {
        return $this->usersTable
            ->where(['unique_id' => $id])
            ->set(['status' => 'inactive'])
            ->update();
    }

    public function updateUser($id, $data)
    {

        return $this->usersTable
            ->where(['unique_id' => $id])
            ->set($data)
            ->update();
    }

    public function getCollectionCenters()
    {
        return $this->center->select()->get()->getResult();
    }
    public function getCollectionCenterName($centerNumber)
    {
        return $this->center->select(
            'centerNumber
          centerName, 
          CONCAT(mobileNumber," ", telNnumber," ",fax," "address," ",mail) as contacts
         '

        )->where(['centerNumber' => $centerNumber])->get()->getRow();
    }
}
