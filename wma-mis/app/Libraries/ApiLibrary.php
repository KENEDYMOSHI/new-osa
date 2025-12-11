<?php namespace App\Libraries ;

use App\Models\ApiModel;

class ApiLibrary{
protected $apiModel;
public function __construct()
{
    $this->apiModel = new ApiModel();
}

//verify if pos device id exists in the database
public function verifyPosId($posId){
    $device = $this->apiModel->verifyPosId($posId);
    if(!empty($device)){
        return true;
    }else{
        return false;
    }
}
public function updatePosUsageDetails(){
    
}
}
