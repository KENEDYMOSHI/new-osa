<?php namespace App\Controllers;

use App\Libraries\CommonTasksLibrary;
use App\Models\PortModel;
use App\Models\ProfileModel;
 class CertificateOfQuantity extends BaseController{
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

  public function searchExistingShips()
  {
    $ships = $this->portUnitModel->findMatch();
    return json_encode($ships);
  }

  public function selectedShip(){
    if($this->request->getMethod()=='post'){
       $id = $this->getVariable('id');
       $request = $this->portUnitModel->getSelectedShip($id);

       if ($request) {
       echo json_encode($request);
       }
    }
  }


  

 
   public function index(){

 
   
    $uniqueId = $this->uniqueId;
    $role = $this->role;
    $data['profile'] = $this->profileModel->getLoggedUserData($uniqueId);
    $data['role'] = $role;       
    $data['page'] = [
            "title"   => "Certificate Of Quantity",
            "heading" => "Certificate Of Quantity"
    ];

    
    
    $data['genderValues'] = ['Male','Female'];
       return view('Pages/Port/quantityCertificate',$data);
   }

   public function addCertificateOfQuantity(){
    if($this->request->getMethod() == 'POST'){
       
      $quantityCert = [
        'ship_id'=>$this->getVariable('shipId'),
      
        'usbbls_60F'=>$this->getVariable('USBBLS_60'),
        'us_gallons_60F'=>$this->getVariable('USGallons_60'),
       
        
      
        'unique_id'=>$this->uniqueId,
      ];


      // echo json_encode($quantityCert);
      // exit;
      $request = $this->portUnitModel->saveCertificateOfQuantity($quantityCert);
      if($request){
        echo json_encode('Added');
      }else{
        echo json_encode('Something Went Wrong');
      }


    }

   }

 
   public function getCertificateOfQuantity(){
 
    if($this->request->getMethod() == 'POST'){
       
     
     $shipId =  $this->getVariable('shipId');
      $request = $this->portUnitModel->getCertificateOfQuantity($shipId,$this->uniqueId);
      if($request){
        echo json_encode($request);
      }else{
        echo json_encode('nothing');
      }


    }
   }

    //=================Processing Certificate of Quantity====================
  public  function processCertificateOfQuantity($arr) {
    
 

  }


 
//$data['documents'] = $this->portUnitModel->downloadDocument($id);
function downloadCertificateOfQuantity($id){
  if (!$this->session->has('loggedUser')) {
    return redirect()->to('/login');
}

$dompdf = new \Dompdf\Dompdf();
$metric_tons_in_air = 0;
$metric_tons_in_vac = 0;
$long_tons = 0;
$litres_20c = 0;
$litres_15c = 0;

$density_20 = 0;

$observedVolume = 0;
$shipInfo = $this->portUnitModel->getSelectedShip($id);

$page=
certificateOfQuantityHeader($shipInfo->ship_name,$shipInfo->arrival_date,$shipInfo->terminal,$shipInfo->cargo,$shipInfo->port,$shipInfo->phone_number,$shipInfo->fax,$shipInfo->postal_address,$shipInfo->email).'
    <div class="wrapper">
    <table class="main-table" border="0" ';
                $certificate = $this->portUnitModel->getCertificateOfQuantity($id,$this->uniqueId);
                $density_15 =  $certificate[0]->density_15C; 
                $density_20 =  $certificate[0]->density_20C; 
                $usbbls_60F = $certificate[0]->usbbls_60F; ;
                $us_gallons_60F = $certificate[0]->us_gallons_60F; ;
                $WCFT_15 =  $density_15 - 0.0011;
                $WCFT_20 =  $density_20 - 0.0011;

                foreach($certificate as $cert){
                  $metric_tons_in_air += ($cert->GSV20Centigrade * $WCFT_20);
                  $metric_tons_in_vac += ($cert->GSV20Centigrade *  $density_20);
                  $observedVolume+=$cert->totalObservedVolume;
                  $litres_15c+=$cert->GSV15Centigrade;
                  $litres_20c+=$cert->GSV20Centigrade;
                }
                  $page.='<table class="table" border="0" style="width: 60%;">

                  <tbody>
                 
                      <tr>
                          <td> Metric Tons in Air</td>
                          <td > = </td>
                          <td>'.round($metric_tons_in_air,3).'</td>
                      </tr>
                      <tr>
                          <td>Metric Tons in Vac.</td>
                          <td > = </td>
                          <td>'.round($metric_tons_in_vac,3).'</td>
                      </tr>
                      <tr>
                          <td> Long Tons</td>
                          <td> = </td>
                          <td>'.round(($metric_tons_in_air * 0.984206),3).'</td>
                      </tr>
                      <tr>
                          <td> Litres @ 20&deg;C</td>
                          <td > = </td>
                          <td>'.number_format($litres_20c*1000).'</td>
                      </tr>
                      <tr>
                          <td>Observed Volume (Liters)</td>
                          <td > = </td>
                          <td>'.number_format($observedVolume*1000).'</td>
                      </tr>
                      <tr>
                          <td>Litres @ 15&deg;C</td>
                          <td> = </td>
                          <td>'.number_format($litres_15c*1000).'</td>
                      </tr>
                      <tr>
                          <td> Litres @ 20&deg;C</td>
                          <td> = </td>
                          <td>'.number_format($litres_20c*1000).'</td>
                      </tr>
                      <tr>
                          <td> US BBLS @ 60&deg;F</td>
                          <td> = </td>
                          <td>'.number_format($usbbls_60F).'</td>
                      </tr>
                      <tr>
                          <td>US GALLONS @ 60&deg;F</td>
                          <td > = </td>
                          <td>'.number_format($us_gallons_60F).'</td>
                      </tr>
                      <tr>
                          <td>Std density@20</td>
                          <td> = </td>
                          <td>'.$density_20.'</td>
                      </tr>
                      <tr>
                          <td>Std density@15</td>
                          <td> = </td>
                          <td>'.$density_15.'</td>
                      </tr>
                 
                  </tbody>
                  </table>
                 ' ;
                
              

                 
                 '
                 ';

                 $page.=documentFooter($cert->first_name,$cert->last_name,$shipInfo->captain);

$dompdf->loadHtml($page);
// (Optional) Setup the paper size and orientation
$dompdf->setPaper('A4', 'portrait');
$dompdf->set_option('isRemoteEnabled', TRUE);

// Render the HTML as PDF
$dompdf->render();

// Output the generated PDF to Browser
$dompdf->stream($shipInfo->ship_name.' Certificate Of Quantity' . ".pdf", array("Attachment" => 1));
}
}
