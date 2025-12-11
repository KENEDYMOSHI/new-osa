<?php

namespace App\Models;

use CodeIgniter\Model;
use Config\Database;

class MiscellaneousModel extends Model
{
    public $db;
    public $contactsTable;
    private $transactionTable;
    private $regionTarget;
    private $activityTarget;
    private $backupTable;
    private $visitors;
    private $regions;
    private $districts;
    private $wards;
    private $postCode;
    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->contactsTable = $this->db->table('contacts');
        $this->backupTable = $this->db->table('backup');
        $this->transactionTable = $this->db->table('transactions');
        $this->regionTarget = $this->db->table('regional_targets');
        $this->activityTarget = $this->db->table('activity_targets');
        $this->visitors = $this->db->table('visitors');
        $this->regions = $this->db->table('region');
        $this->districts = $this->db->table('district');
        $this->wards = $this->db->table('ward');
        $this->postCode = $this->db->table('postcode');

    }

    public function fetchRegions()
    {
        return $this->regions->select('name')->get()->getResult();
         
    }
    public function fetchDistricts($region)
    {
        return $this->districts->select('name')->where(['region' => $region])->get()->getResult();
         
    }
    public function fetchWards($district)
    {
        return $this->wards->select('name')->where(['district' => $district])->get()->getResult();
         
    }
    public function fetchPostCodes($ward)
    {
        return $this->postCode->select('postcode')->where(['ward' => $ward])->get()->getRow();
         
    }






    public function getVisitors()
    {
        return $this->visitors
            ->select()
            ->orderBy('id','DESC')
            ->limit(200000)
            ->get()
            ->getResult();
    }











    public function writeBackupDate($date){
        return $this->backupTable->insert($date);
    }
    public function readBackupDate(){
        return $this->backupTable->select()->orderBy('id', 'DESC')->limit(1)->get()->getRow();
    }

    public function getContacts($region)
    {
        return $this->contactsTable
            ->select()
            ->where(['region' => $region])
            ->get()
            ->getRow();
    }
    public function getLastControlNumber()
    {
        return $this->transactionTable
            ->select('control_number')
            ->orderBy('id', 'DESC')
            ->limit(1)
            ->get()
            ->getRow();
    }
    public function saveRegionTarget($data)
    {
        return $this->regionTarget
            ->set($data)
            ->insert();
    }
    public function saveActivityTarget($data)
    {
        return $this->activityTarget
            ->set($data)
            ->insert();
    }
    public function getRegionTarget()
    {
        return $this->regionTarget
            ->select('regional_targets.*,centerNumber,centerName')
            ->join('collectioncenter','collectioncenter.centerNumber = regional_targets.region')
            ->orderBy('region', 'ASC')
            ->get()
            ->getResult();
    }
    public function getActivityTarget($region)
    {
        return $this->activityTarget
            ->select()
            ->where(['region' => $region])
            ->orderBy('activity', 'ASC')
            ->get()
            ->getResult();
    }
    public function getActivityMonthlyTarget($region, $month, $year, $activity)
    {
        return $this->activityTarget
            ->select()
            ->where(['region' => $region])
            ->where(['month' => $month])
            ->where(['year' => $year])
            ->where(['activity' => $activity])
            ->get()
            ->getRow();
    }
    public function getActivityAnnualTargetInstruments($region, $year, $activity)
    {
        return $this->activityTarget
            ->select()
            ->where(['region' => $region])
            ->where(['year' => $year])
            ->where(['activity' => $activity])
            ->get()
            ->getResult();
    }
    public function getActivityAnnualTarget($region, $year, $activity)
    {
        return $this->activityTarget
            ->select()
            ->where(['region' => $region])
        // ->where('month BETWEEN ' . $monthFrom . ' AND ' . $monthTo . '')
            ->where(['year' => $year])
            ->where(['activity' => $activity])
            ->get()
            ->getResult();
    }
    public function editRegionTarget($id)
    {
        return $this->regionTarget
            ->select()
            ->where(['id' => $id])
            ->get()
            ->getRow();
    }
    public function editActivityTarget($id)
    {
        return $this->activityTarget
            ->select()
            ->where(['id' => $id])
            ->get()
            ->getRow();
    }
    public function updateRegionTarget($id, $data)
    {
        return $this->regionTarget
            ->set($data)
            ->where(['id' => $id])
            ->update();

    }
    public function updatedActivityTarget($data, $id)
    {
        return $this->activityTarget
            ->set($data)
            ->where(['id' => $id])
            ->update();

    }
    public function readRegionTarget($month, $region)
    {
        return $this->regionTarget
            ->select('amount')
            ->where(['region' => $region])
            ->where(['month' => $month])
            ->get()
            ->getRow();

    }
    public function fetchTarget($params)
    {
        return $this->regionTarget
             ->select()
            ->selectSum('amount')
            ->where($params)
            ->get()
            ->getResult();


    }
    public function fetchTargetAnnually($region,$d1,$d2)
    {
        return $this->regionTarget
            ->selectSum('amount')
            ->where(['region'=>$region])
            ->where(['date >=' => $d1])
            ->where(['date <=' => $d2])
            ->get()
            ->getResult();


    }
    public function readOverallTarget($region)
    {
        return $this->regionTarget
            ->select()
            ->where('month BETWEEN 1 AND 12')
            ->where(['region' => $region])
            ->get()
            ->getResult();

    }
}