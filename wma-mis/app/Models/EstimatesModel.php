<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Libraries\ArrayLibrary;

class EstimatesModel extends Model
{
    protected $db;
    protected $estimates;
    protected $instrumentEstimates;
    protected $activityEstimate;
    protected $verifiedItems;
    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->estimates = $this->db->table('estimates');
        $this->instrumentEstimates = $this->db->table('instrument_estimates');
        $this->activityEstimate = $this->db->table('activity_estimates');
        $this->verifiedItems = $this->db->table('bill_items');
    }

    public function getVerifiedItems($params)
    {
        $builder = $this->verifiedItems->select('GfsCode,ItemQuantity,bill_items.CreatedAt,Status,wma_bill.CollectionCenter')
            ->join('wma_bill', 'wma_bill.BillId = bill_items.BillId')
            ->where(['deletedAt' => null, 'Status' => 'Pass'])
            ->where($params)
          //  ->limit(20)
            ->get()
            ->getResult();

        // return $builder;
        // exit;

        $activity = (new ArrayLibrary($builder))->map(fn ($item) => (int)$item->GfsCode)->get();

        if (!empty($builder)) {
            // $quantity = (new ArrayLibrary($builder))->map(fn ($item) => (int)$item->ItemQuantity ?? 1)->reduce(fn ($x, $y) => $x + $y)->get();
            $items = (new ArrayLibrary($builder))->map(fn ($item) => (object)['quantity' => $item->ItemQuantity  ?? 1, 'activity' => $item->GfsCode])->get();
        } else {
            $items = [];
        }

        return $items;
    }

    public function createEstimate($data)
    {

        return $this->estimates->insert($data);
    }

    public function updateEstimate($data, $id)
    {

        return $this->estimates->set($data)->where('id', $id)->update();
    }

    public function editEstimate($id)
    {
        return $this->estimates
            ->select()
            ->where('id', $id)
            ->get()
            ->getRow();
    }
    public function editActivityEstimate($id)
    {
        return $this->activityEstimate
            ->select()
            ->where('id', $id)
            ->get()
            ->getRow();
    }
    public function getEstimate($params)
    {
        return $this->estimates
            ->select()
            ->where($params)
            ->get()
            ->getRow();
    }
    public function getEstimates($params)
    {
        return $this->estimates
            ->select()
            ->where($params)
            ->get()
            ->getResult();
    }
    //=============================INSTRUMENTS ====================
    public function getInstrumentEstimates($params)
    {
        return $this->instrumentEstimates
            ->select()
            ->where($params)
            ->get()
            ->getResult();
    }

    //*************************************************************** */

    public function createInstrumentEstimate($data)
    {

        return $this->instrumentEstimates->insert($data);
    }
    public function updateActivityEstimate($id,$data)
    {

        return $this->activityEstimate->set($data)->where('id', $id)->update();;
    }

    public function getInstrumentEstimate($params)
    {
        return $this->instrumentEstimates
            ->select()
            ->where($params)
            ->get()
            ->getRow();
    }

    public function updateInstrumentEstimate($data, $id)
    {

        return $this->instrumentEstimates->set($data)->where('id', $id)->update();
    }


    public function updateRemainingInstruments($data)
    {

        $region = auth()->user()->collection_center;
        $params = ['region' => $region, 'month' => $data['month'], 'year' => $data['year']];
        $instrumentData = $this->instrumentEstimates->select()->where($params)->get()->getRow();
        $totalInstruments = $instrumentData->instruments ?? 0;
        $remainingInstruments = $instrumentData->remaining ?? 0;
        $totalAllocation = $remainingInstruments == 0 ? $totalInstruments : $remainingInstruments;
        // $remaining = $totalAllocation - $data['quantity'];

        $alreadyAllocated = $this->getAllAllocations($params);
        $remaining = $totalInstruments - $alreadyAllocated;
        return $this->instrumentEstimates->where($params)->update(['remaining' => $remaining]);
    }

    public function getAllAllocations($params)
    {
        return  $this->activityEstimate->selectSum('quantity')->where($params)->get()->getResult()[0]->quantity ?? 0;
    }

    public function allocateEstimate($data)
    {

        $insert = $this->activityEstimate->insert($data);
        $update = $this->updateRemainingInstruments($data);
        if ($insert && $update) {
            return true;
        } else {
            return false;
        }
    }

    public function getActivityEstimates($params)
    {
        return $this->activityEstimate->select()->where($params)->get()->getResult();
    }
}
