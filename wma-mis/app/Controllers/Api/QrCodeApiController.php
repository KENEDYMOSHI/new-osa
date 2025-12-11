<?php

namespace App\Controllers\Api;

use App\Models\SearchModel;
use App\Models\VtcModel;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\ResourceController;

class QrCodeApiController extends ResourceController
{
  use ResponseTrait;
  protected $helpers = ['setting'];
  public function __construct(
    protected $searchModel = new SearchModel()
  ) {
  }

  public function getVariable($var)
  {
    return $this->request->getVar($var, FILTER_SANITIZE_SPECIAL_CHARS);
  }



  public function verifyInstrument()
  {
    try {
      $vehicleId =  $this->getVariable('vehicleId');
      $activity =  $this->getVariable('activity');

      $id = substr($vehicleId, 0, -42);
      $vtvModel = new VtcModel();
      $vehicle = $vtvModel->getVehicle($id);
      if (!empty($vehicle)) {
        $sticker = $vehicle->sticker_number;
        $request = $vtvModel->getVehicleBySticker($sticker);
        $theId = $request->id;
        $response = [
          'status' => 1,
          'data' => $this->searchModel->selectItem($theId, $activity),
          'activity' => $activity,
          'id' => $id,


        ];
      } else {
        $response = [
          'status' => 0,
          'data' => []
        ];
      }
    } catch (\Throwable $th) {
      $response = [
        'status' => 0,
        'data' => [
          'msg' => $th->getMessage(),
        ]

      ];
    }
    return $this->response->setJSON($response)->setStatusCode(500);
  }
}
