<?php namespace App\Controllers;

use App\Libraries\CommonTasksLibrary;
use App\Models\PortModel;
use App\Models\ProfileModel;
 class DischargingSequence extends BaseController{
  public $uniqueId;
  public $managerId;
  public $role;
  public $city;
  public $portUnitModel;
  public $session;
  public $profileModel;
  public $CommonTasks;

  public $sessionExpiration;

  public $variable;
  public $appRequest;

  public function __construct()
  {
          $this->appRequest = service('request');
          $this->portUnitModel = new PortModel();
          $this->profileModel = new ProfileModel();
          $this->session = session();
        
          $this->uniqueId = $this->session->get('loggedUser');
          $this->managerId = $this->session->get('manager');
          $this->role = $this->profileModel->getRole($this->uniqueId)->role;
          $this->city = $this->session->get('city');
          $this->CommonTasks = new CommonTasksLibrary();
          helper(['form', 'array', 'regions','date','documents','image']);

          
  }

  


  function getVariable($var){
    return $this->appRequest->getVar($var,FILTER_SANITIZE_STRING);
  }

 
 
   public function index(){

 
   
    $uniqueId = $this->uniqueId;
    $role = $this->role;
    $data['profile'] = $this->profileModel->getLoggedUserData($uniqueId);
    $data['role'] = $role;       
    $data['page'] = [
            "title"   => "Discharging Sequence",
            "heading" => "Discharging Sequence"
    ];

    
    
    
       return view('Pages/Port/dischargingSeq',$data);
   }

   public function addTankDischargingSequence(){
    if($this->request->getMethod() == 'POST'){
       
      $tank = [
        'ship_id'=> $this->getVariable('shipId'),
        'tank_number'=> $this->getVariable('tankNumber'),
        'line_displacement'=> $this->getVariable('lineDisplacement'),
        'time_from'=> $this->getVariable('timeFrom'),
        'date_from'=> dateFormatter($this->getVariable('dateFrom')),
        'unique_id'=> $this->uniqueId,
      
        
      ];


      // echo json_encode($tank);
      // exit;
      $request = $this->portUnitModel->saveTankDischargingSequence($tank);
      if($request){
        echo json_encode('Added');
      }else{
        echo json_encode('Something Went Wrong');
      }


    }

   }


   public function checkTanks(){
    // json_encode('nothing');
     if($this->request->getMethod() == 'POST'){
       $shipId = $this->getVariable('shipId');
       $request = $this->portUnitModel->getTankInfo($shipId);
       if($request){
        return json_encode($request);
       }else{
        return json_encode('nothing');
       }
     }
   }
   
   public function updateTankTimeDate(){
     if($this->request->getMethod() == 'POST'){
        $id = $this->getVariable('id');

        $tank = [
          'time_to' => $this->getVariable('timeTo'),
          'date_to' => $this->getVariable('dateTo'),
        ];

        // echo json_encode($tank);
        // exit;
        $request = $this->portUnitModel->updateTank($id,$tank);
        if($request){
         return json_encode('updated');
        }else{
         return json_encode('nothing');
        }
      
     }
   }

   //=================time conversion method====================

 
   public function getDischargingSequence(){
    if (!$this->session->has('loggedUser')) {
      return redirect()->to('/login');
    }
    if($this->request->getMethod() == 'POST'){
       
     
     $shipId =  $this->getVariable('shipId');
      $tanks = $this->portUnitModel->getTankInfo($shipId);
      if($tanks){
        $table = '
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th colspan="0">Receiving Terminal</th>
                    <th>Product</th>
                    <th>Quantity M/Tonnes</th>
                    <th colspan="2">From</th>
                    <th colspan="2">To</th>

                </tr>
            </thead>
            <tbody>';

          
        
        foreach ($tanks as $tank) {

          if ($tank->time_to !='' && $tank->date_to !='') {
            # code...
          

          $table.= '
          <div>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td><b>Time</b></td>
                        <td><b>Date</b></td>
                        <td><b>Time</b></td>
                        <td><b>Date</b></td>

                    </tr>
                    <tr>
                        <td>'.$tank->terminal.'</td>
                        <td>'.$tank->cargo.'</td>
                        <td>'.$tank->arrivalQuantity1.'</td>
                        <td>'.to24Hours($tank->time_from).'</td>
                        <td>'.$tank->date_from.'</td>
                        <td>'.to24Hours($tank->time_to).'</td>
                        <td>'.dateFormatter($tank->date_to).'</td>
                        

                    </tr>
                    <tr>
                        <td>Tank No '.$tank->tank_number .'</td>
                        <td>Line Displ</td>
                        <td>'.$tank->line_displacement.' M<sup>3</sup></td>
                        <td colspan="4" style="text-align: center;"><b>Duration '.timeDifference($tank->time_from,$tank->time_to).' Hours</b></td>
                    </tr>
                    <tr>
                        <td colspan="8" style="text-align: center;">Ship Stop</td>
                    </tr>
                </div>
          ';
        }
        }
        $table .='
        </tbody>
        </table>
        ';

        return $table;
        // echo json_encode($request);
      }else{
        return '<h4>No Data Available</h4>';
      }


    }
   }

    //=================Download note of fact before discharge====================
  public  function downloadDischargingSequence($shipId) {
 
  
$date = date('d-M,Y h:i:s ');
$dompdf = new \Dompdf\Dompdf();
$options = new \Dompdf\Options();

$title = ' Discharging Sequence';
$data['title'] = ' Discharging Sequence';


 
// $data['details'] = $this->portUnitModel->downloadDocument($shipId);
$data['tanks'] = $this->portUnitModel->getTankInfo($shipId);
$dompdf->loadHtml(view('PortUnitTemplates/DischargingSequencePdf',$data));
$dompdf->setPaper('A4', 'portrait');
$options->set('isRemoteEnabled', TRUE);

// Render the HTML as PDF
$dompdf->render();

$dompdf->stream($title.':'.$shipId. '.pdf', array('Attachment' => 1));
 

  }



}


?>