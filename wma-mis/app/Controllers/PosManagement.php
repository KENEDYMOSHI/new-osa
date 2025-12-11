<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Libraries\CommonTasksLibrary;
use App\Models\ApiModel;

class PosManagement extends BaseController
{


  protected $apiModel;
  protected $token;
  protected $user;
  public function __construct()
  {
    $this->apiModel = new ApiModel();
    $this->token = csrf_hash();
    $this->user = auth()->user();

    helper('setting');
    helper(setting('App.helpers'));
  }

  public function getVariable($var)
  {
    return $this->request->getVar($var, FILTER_SANITIZE_SPECIAL_CHARS);
  }


  public function index()
  {
    $data['page'] = [
      "title"   => "Pos Management",
      "heading" => 'Pos Management'
    ];

    $data['centers'] = (new CommonTasksLibrary)->collectionCenters();
    $data['user'] = $this->user;
    $data['posData'] = $this->posHtml($this->apiModel->getAllDevices());

    return view('Pages/admin/WmaPosManagement', $data);
  }

  public function addPos()
  {
    try {
      $data = [
        'centerName' => $this->getVariable('centerName'),
        'deviceId' => $this->getVariable('deviceId'),
      ];
      $request = $this->apiModel->registerPos($data);
      if ($request) {
        return  $this->response->setJSON([
          'status' => 1,
          'token' => $this->token,
          'msg' => 'Pos Added Successfully',
          'posData' => $this->posHtml($this->apiModel->getAllDevices()),
        ]);
      } else {

        return  $this->response->setJSON([
          'status' => 0,
          'token' => $this->token,
          'msg' => 'Something Went Wrong',
        ]);
      }
    } catch (\Throwable $th) {
      $response = [
        'status' => 0,
        'msg' => $th->getMessage(),
        'token' => $this->token
      ];
    }
    return $this->response->setJSON($response)->setStatusCode(500);
  }
  public function updatePos()
  {
    try {
      $id = $this->getVariable('posId');
      $data = [
        'centerName' => $this->getVariable('centerName'),
        'deviceId' => $this->getVariable('deviceId'),
      ];
      $request = $this->apiModel->updatePos($id,$data);
      if ($request) {
        return  $this->response->setJSON([
          'status' => 1,
          'token' => $this->token,
          'msg' => 'Pos Updated Successfully',
          'posData' => $this->posHtml($this->apiModel->getAllDevices()),
        ]);
      } else {

        return  $this->response->setJSON([
          'status' => 0,
          'token' => $this->token,
          'msg' => 'Something Went Wrong',
        ]);
      }
    } catch (\Throwable $th) {
      $response = [
        'status' => 0,
        'msg' => $th->getMessage(),
        'token' => $this->token
      ];
    }
    return $this->response->setJSON($response)->setStatusCode(500);
  }

  public function editPos()
  {
    try {
      $posId = $this->getVariable('posId');
      $pos = $this->apiModel->findPos($posId);
      return $this->response->setJSON([
        'status' => 1,
        'pos' => $pos,
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

  public function posHtml($posData)
  {

    $tr = '';
    foreach ($posData as $data) {
      $status = $data->isActive == 1 ? 'Active' : 'Inactive';
      $tr .= <<<HTML
                <tr>
                        <td>$data->centerName</td>
                        <td>$data->deviceId</td>
                        <td>$status</td>
                        <td>$data->loginTime</td>
                        <td>$data->logoutTime</td>
                        <td>$data->lastUser</td>
                        <td>

                          
                                <button data-toggle="tooltip" data-placement="top" title="Activate" class="btn btn-danger btn-xs"><i class="fas fa-lock-alt"></i></button>
                           
                              
                           



                            <button data-toggle="tooltip" data-placement="top" title="Edit" type="button" onclick="editPos('$data->id')" class="btn btn-primary btn-xs">
                                <div class="">
                                    <i class="fas fa-pen"></i>

                                </div>

                            </button>

                            <button data-toggle="tooltip" data-placement="top" title="Delete" type="button" onclick="deletePos('$data->id')" class="btn bg-danger btn-xs"><i class="fas fa-trash-alt"></i></button>


                        </td>
                    </tr> 
       HTML;
    }

    return $tr;
  }
}
