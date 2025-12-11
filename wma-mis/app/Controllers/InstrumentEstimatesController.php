<?php

namespace App\Controllers;

use App\Libraries\CommonTasksLibrary;
use App\Models\EstimatesModel;


class InstrumentEstimatesController extends BaseController
{
  private $estimatesModel;
  public $session;
  public $uniqueId;
  public $user;


  public $token;
  public function __construct()
  {

    helper(['form', 'array', 'regions', 'date']);
    $this->session         = session();
    $this->token         = csrf_hash();
    $this->estimatesModel        = new EstimatesModel();
    $this->uniqueId        = auth()->user()->unique_id;
    $this->user = auth()->user();
  }

  public function getVariable($var)
  {
    return $this->request->getVar($var, FILTER_SANITIZE_SPECIAL_CHARS);
  }



  public function index()
  {
    // if (!$this->user->hasPermission('estimate.manage')) {
    //     return redirect()->to('dashboard');
    // }

    $data['page'] = [
      'title' => 'Instrument Estimates',
      'heading' => 'Instrument Estimates',
    ];

    $data['user'] = $this->user;
    // $estimates = $this->getTargets();
    $data['estimates'] = $this->estimatesModel->getInstrumentEstimates(['year'=>date('Y')]);
    // Printer($data['estimates']);
    // exit;
    $data['regions'] = (new CommonTasksLibrary())->collectionCenters();
    return view('Pages/InstrumentEstimates', $data);
  }


  public function updateInstrumentEstimate()
  {
    try {
      $id = $this->getVariable('id');
      $month = $this->getVariable('month');
      $year = $this->getVariable('year');
      $instruments = $this->getVariable('instruments');
  
      $data = [
      
        'month' => $month,
        'year' => $year,
        'instruments' => $instruments,
      ];
  
      $query = $this->estimatesModel->updateInstrumentEstimate($data,$id);
  
      if($query){
        return $this->response->setJSON([
          'status' => 1,
          'meg' => 'Estimate Updated',
          'token' => $this->token
        ]);
      }
           } catch (\Throwable $th) {
               
               return $this->response->setJSON([
                'status' => 0,
                'msg' => $th->getMessage(),
                'token' => $this->token
            ]);
           }

    
  }
  public function createInstrumentEstimate()
  {
    try {
      $region = $this->getVariable('region');
      $month = $this->getVariable('month');
      $year = $this->getVariable('year');
      $instruments = $this->getVariable('instruments');
  
      $data = [
        'region' => $region,
        'regionName' => str_replace('Wakala Wa Vipimo','',wmaCenter($region)->centerName),
        'month' => $month,
        'year' => $year,
        'instruments' => $instruments,
        'userId' => $this->uniqueId,
      ];
  
      $query = $this->estimatesModel->createInstrumentEstimate($data);
  
      if($query){
        return $this->response->setJSON([
          'status' => 1,
          'meg' => 'Estimate Created',
          'token' => $this->token
        ]);
      }
           } catch (\Throwable $th) {
               
               return $this->response->setJSON([
                'status' => 0,
                'msg' => $th->getMessage(),
                'token' => $this->token
            ]);
           }

    
  }

  public function editInstrumentEstimate(){
     try {
         $id =  $this->getVariable('id');
         $query = $this->estimatesModel->getInstrumentEstimate(['id' => $id]);
          return $this->response->setJSON([
            'status' => 1,
            'estimate' => $query,
            'token' => $this->token
          ]);
            } catch (\Throwable $th) {
                $response = [
                    'status' => 0,
                    'msg' => $th->getMessage(),
                    'token' => $this->token
                ];
            }
        return $this->response->setJSON($response);
  }
}
