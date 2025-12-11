<?php namespace App\Libraries;
 use App\Models\BillModel;

 class BillLibrary{
    protected $billModel;

    public function __constructor(){
        $this->billModel = new BillModel(); 
    }

    public function createBill (...$data)
    {
      $hashArray = [];
      for ($i = 0; $i < count($products); $i++) {
         array_push($amountArray, (int)$totalAmount);
         array_push($controlNumberArray, $controlNumber);
         array_push($uniqueIds, $this->uniqueId);
         array_push($activityArray, $activityType);
      }
      $data = [
         'hash' => [],
         'product_id' => [],
         'control_number' => [],
         'amount' =>  [],
         // 'activity_type' =>  $activityArray,
      ];
      // creating multidimensional array for batch insertion
      foreach ($data as $key => $value) {
         for ($i = 0; $i < count($value); $i++) {
            $billingData[$i][$key] = $value[$i];
         }
      }  
    }
 }

?>