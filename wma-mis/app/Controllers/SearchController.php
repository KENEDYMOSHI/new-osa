<?php namespace App\Controllers;
use App\Models\SearchModel;

class SearchController extends BaseController
{
  private $searchModel;
  public $session;
  public $uniqueId;
  public $role;


  public $token;
  public function __construct()
  {
    
          helper(['form', 'array', 'regions', 'date']);
          $this->session         = session();
          $this->token         = csrf_hash();
          $this->searchModel        = new SearchModel();
          $this->uniqueId        = auth()->user()->unique_id;
          $this->role = auth()->user()->role;
  }

  public function getVariable($var)
  {
    return $this->request->getVar($var, FILTER_SANITIZE_SPECIAL_CHARS);
  }
public function index()

{
 
  $data['page']=[
    'title' => 'Searching Page',
    'heading' => 'Searching Page',
  ];




  $data['role']= $this->role;

  return view('Pages/Search/searchPage',$data);
}


//=================searching vtc====================
public function searchItem(){
  try {
    $keyword =  $this->getVariable('keyword');
    $activity =  $this->getVariable('activity');
  
    
  
    $data = $this->searchModel->searchItem($keyword,$activity);
  
     $response =[
    'status'=>1,
    'data'=> $data,
    'activity'=> $activity,
    'token'=>$this->token
    ];
      
         } catch (\Throwable $th) {
            $code = 500;
             $response = [
                 'status' => 0,
                 'msg' => $th->getMessage(),
                 'trace' => $th->getTrace(),
                 'token' => $this->token
             ];
         }
     return $this->response->setJSON($response)->setStatusCode($code??200);
  
}
public function selectItem(){
  $id =  $this->getVariable('id');
  $activity =  $this->getVariable('activity');

 
  $request = $this->searchModel->selectItem($id,$activity);

  $data= [
      'status' => 1,
      'data' => $request,
      'activity' => $activity,
      'token' => $this->token,
      'id' => $id,
  ];

  return $this->response->setJSON($data);
  
}



}