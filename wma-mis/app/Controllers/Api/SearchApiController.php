<?php

namespace App\Controllers\Api;

use App\Models\SearchModel;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\ResourceController;

class SearchApiController extends ResourceController
{
  use ResponseTrait;
  protected $helpers = ['setting'];
  protected $searchModel;
  protected $apiKey;
  public function __construct() {
    $this->searchModel = new SearchModel();
    $this->apiKey = env('API_KEY');
  }

  public function getVariable($var)
  {
    return $this->request->getVar($var, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
  }


  public function verifyApiKey(){
    
  }



  //=================searching vtc====================
  public function searchInstrument()
  {
     try {
      $keyword =  $this->getVariable('keyword');
      $activity =  $this->getVariable('activity');
  
  
  
      $data = $this->searchModel->searchItem($keyword, $activity);
  
      if (!empty($data)) {
        return $this->response->setJSON([
          'status' => 1,
          'data' => $data,
          // 'activity' => $activity,
        ]);
      } else {
        return $this->response->setJSON([
          'status' => 0,
          'data' => [],
          // 'activity' => $activity,
        ]);
      }
            } catch (\Throwable $th) {
                $response = [
                    'status' => 0,
                    'msg' => $th->getMessage(),
                 
                ];
                return $this->response->setJSON($response)->setStatusCode(500);
            }
  }
  public function selectInstrument()
  {
    try {
      $id =  $this->getVariable('id');
      $activity =  $this->getVariable('activity');
  
  
  
      $request = $this->searchModel->selectItem($id, $activity);
  
      $data = [
        'status' => 1,
        'data' => $request
      ];
  
      return $this->response->setJSON($data);
           } catch (\Throwable $th) {
               $response = [
                   'status' => 0,
                   'msg' => $th->getMessage(),
                 
               ];
           }
       return $this->response->setJSON($response)->setStatusCode(500);
  }
}
