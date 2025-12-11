<?php

namespace App\Models;


use CodeIgniter\Shield\Entities\User;
use CodeIgniter\Shield\Models\UserModel;



class UsersModel extends UserModel
{
  protected $createdField = 'created';
  protected $allowedFields  = [
    'username',
    'first_name',
    'last_name',
    'collection_center',
    'unique_id',
    'status',
    'status_message',
    'active',
    'last_active',
    'deleted_at',
  ];


  public function getUserByEmail($email): User
  {
    $db = \Config\Database::connect();
    $user = $db->table('users')
      ->select('users.*, auth_identities.secret')
      ->join('auth_identities', 'users.id = auth_identities.user_id')
      ->where('auth_identities.secret', $email)
      ->get()
      ->getRowArray();

    return new User($user);
  }

  public function storeToken($data)
  {
    $tokenExist = $this->db->table('activation_tokens')->where('userId', $data['userId'])->get()->getRow();
    if ($tokenExist) {
      $this->db->table('activation_tokens')->where('userId', $data['userId'])->delete();
    }
    $query = $this->db->table('activation_tokens')->insert($data);
    return $query;
  }
  public function getUserToken($token)
  {
    $query = $this->db->table('activation_tokens')->where('token', $token)->get()->getRow();
    return $query;
  }

  public function deleteToken($token)
  {
    $query = $this->db->table('activation_tokens')->where('token', $token)->delete();
    return $query; 
  }
}
