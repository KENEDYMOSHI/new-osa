<?php

namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\Shield\Entities\User;

class ProfileModel extends Model
{
    protected $db;
    protected $usersTable;
    protected $center;
    protected $authIdentities;
    protected $logs;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->usersTable = $this->db->table('users');
        $this->authIdentities = $this->db->table('auth_identities');
        $this->center = $this->db->table('collectioncenter');
        $this->logs = $this->db->table('audit_log');
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

    public function getUser($id): User
    {
        $user = $this->usersTable->select('users.*,centerName,centerNumber')
            ->where(['unique_id' => $id])
            ->join('collectioncenter', 'collectioncenter.centerNumber = users.collection_center')
            ->get()
            ->getRowArray();

        return new User($user);
    }


    public function getAdmin($id)
    {
        return $this->usersTable->select('position')->where(['position' => 'SuperAdmin'])->where(['unique_id' => $id])->get()->getRow();
    }

    public function updateAvatar($filePath, $id)
    {

        return $this->usersTable
            ->set('avatar', $filePath)
            ->where(['unique_id' => $id])
            ->update();
    }
    public function updatePassword($id, $password)
    {

        return $this->authIdentities
            ->where(['user_id' => $id])
            ->set(['secret2' => $password])
            ->update();
    }
    public function savePassword($secret, $data)
    {

        return $this->authIdentities
            ->where(['secret' => $secret])
            ->set($data)
            ->update();
    }
    public function checkEmail($email)
    {

        return $this->usersTable
            ->select('email')
            ->where(['email' => $email])
            ->get()
            ->getRow();
    }


    public function getCollectionCenters()
    {
        return $this->center->select()->get()->getResult();
    }

    public function findCollectionCenter($CenterNumber)
    {
        return $this->center->select(
            'centerNumber,
          centerName,
          collectionCenterCode, 
          address,
          mobileNumber,
          telNumber,
          CONCAT("Mob: ",mobileNumber," Tel: ",telNumber," Fax: ",fax," Address: ",address," Email:  ",mail) as contacts
         '

        )->where(['CenterNumber' => $CenterNumber])->get()->getRow();
    }

    public function createLog($data)
    {
        return $this->logs->insert($data);
    }
    public function activityLogs()
    {
        return $this->logs->select()->where('DATE(loginTime)', date('Y-m-d'))->limit(100)->orderBy('loginTime', 'DESC')->get()->getResult();
    }

    public function updateLog($data, $sessionId)
    {
        return $this->logs->where(['sessionId' => $sessionId])->set($data)->update();
    }

    public function updatePhoneNumber($phoneNumber, $id)
    {
        return $this->usersTable->where(['unique_id' => $id])->set(['phone_number' => $phoneNumber])->update();
    }
}
