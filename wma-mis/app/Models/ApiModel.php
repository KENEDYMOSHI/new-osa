<?php

namespace App\Models;

use CodeIgniter\Model;

class ApiModel extends Model
{
    protected $db;
    protected $possTable;
    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->possTable = $this->db->table('pos');
    }
    //registering a new pos to the system
    public function registerPos($data)
    {
        return $this->possTable->insert($data);
    }
    //editing pos details
    public function getAllDevices()
    {
        return $this->possTable->select()->get()->getResult();
    }
    //editing pos details
    public function findPos($id)
    {
        return $this->possTable->select()->where(['id'  => $id])->get()->getRow();
    }
    //update any poss details depends on params
    public function updatePos($id, $data)
    {
        return $this->possTable->where(['id'  => $id])->set($data)->update();
    }
    //delete Pos which is no longer in service
    public function deletePos($id)
    {
        return $this->possTable->where(['id'  => $id])->delete();
    }
    public function verifyPos($deviceId)
    {
        $device = $this->possTable->select()->where(['deviceId'  => $deviceId])->get()->getRow();
        if (!empty($device)) {
            return true;
        } else {
            return false;
        }
    }
}
