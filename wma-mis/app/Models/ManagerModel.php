<?php
//SELECT officers_group.officer_id,users.first_name,users.email, officers_group.group_name FROM officers_group INNER JOIN users ON officers_group.officer_id=users.unique_id
namespace App\Models;

use CodeIgniter\Model;


class ManagerModel extends Model
{
  public $db;
  public $taskGroup;
  public $tasks;
  public $officerGroup;
  public $usersTable;
  public function __construct()
  {
    $this->db = \Config\Database::connect();
    $this->taskGroup = $this->db->table('task_group');
    $this->tasks = $this->db->table('tasks');
    $this->officerGroup = $this->db->table('officers_group');
    //$this->officerGroup = $this->db->table('officers_group');
    $this->usersTable = $this->db->table('users');
  }

  // ================Select all users(officers)==============
  public function getAllOfficers($params)
  {

    return $this->usersTable
    ->select('users.*')
    ->join('auth_groups_users', 'auth_groups_users.user_id = users.id')
    // ->join('groups', 'groups.id = users_groups.group_id')
    ->where($params)
    ->get()
    ->getResult();
  }
  public function getUsersByGroup($groupName)
  {
      $users = $this->db->table('users')
          ->select('users.*')
          ->join('users_groups', 'users_groups.user_id = users.id')
          ->join('groups', 'groups.id = users_groups.group_id')
          ->where('groups.name', $groupName)
          ->get()
          ->getResult();

      return $users;
  }
  // ================Select all Created Groups==============
  public function getAllGroups($id)
  {

    return $this->taskGroup
      ->where(['unique_id' => $id])
      ->get()
      ->getResult();
  }




  // ================Crete a group and add it to a database==============
  public function saveGroupData($data)
  {


   return $this->officerGroup->insertBatch($data);
   
  }

  // ================Add an officer to  a  selected group==============
  public function addOfficerToGroup($data)
  {
   return $this->officerGroup->insert($data);
   
  }
  // ================Assigning A task  to a group==============
  public function assignTaskToGroup($data)
  {
   return $this->tasks->insert($data);
    
  }

  public function getGroups($id)
  {

    return $this->officerGroup
      ->select('group_name')
      ->where(['unique_id' => $id])
      ->get()
      ->getResultArray();
  }
  public function getGroupAndOfficers()
  {

    return $this->officerGroup
      ->select('tasks.activity,tasks.description,task.confirmation,tasks.the_group,tasks.region,tasks.district,tasks.ward,tasks.created_at,first_name,last_name,email,avatar')
      // ->where(['users.unique_id' => '7530d45f1b8e519ff4828e528f4c2a37'])
      ->join('users', 'users.unique_id = officers_group.officer_id')
      ->join('tasks', 'tasks.the_group = officers_group.group_name')
      ->get()
      ->getResultArray();
  }
  public function getAllTasks($id)
  {

    return $this->officerGroup
      ->select(
        '
     
      tasks.activity,
      tasks.description,
      tasks.the_group,
      tasks.region,
      tasks.district,
      tasks.ward,
      tasks.confirmation,
      
      tasks.created_at,
      first_name,
      last_name,
      avatar'
      )
      ->where(['tasks.unique_id' => $id])
      ->join('users', 'users.unique_id = officers_group.officer_id')
      ->join('tasks', 'tasks.the_group = officers_group.group_name')
      ->get()
      ->getResultArray();
  }

  public function confirmTask($id)
  {
    return $this->tasks
      ->set(['confirmation' => 1])
      ->where(['id' => $id])
      ->update();
  }


  public function getMyTask($id)
  {

    return $this->officerGroup
      ->select(
        '
      tasks.id,
      tasks.activity,
      tasks.description,
      tasks.the_group,
      tasks.region,
      tasks.district,
      tasks.confirmation,
      tasks.ward,
      tasks.created_at,
      first_name,
      last_name,
      avatar'
      )
      ->where(['users.unique_id' => $id])
      ->join('users', 'users.unique_id = officers_group.officer_id')
      ->join('tasks', 'tasks.the_group = officers_group.group_name')
      ->get()
      ->getResultArray();
  }

  public function showGroupMembers($group)
  {
    return $this->officerGroup
      ->select()
      ->where(['group_name' => $group])
      // ->where(['group_name' => $group])
      // ->join('users', 'users.unique_id = officers_group.officer_id')
      ->get()
      ->getResultArray();
  }

  public function deleteRecord($id)
  {
    $this->groupTable
      ->where(['id' => $id])
      ->delete();
  }


  public function editRecord($id)
  {
    return $this->groupTable
      ->where(['id' => $id])
      ->get()
      ->getRow();
  }

  public function updateGroup($data, $id)
  {


    return $this->groupTable
      ->set($data)
      ->where(['id' => $id])
      ->update();
  }


  // ==============================
}