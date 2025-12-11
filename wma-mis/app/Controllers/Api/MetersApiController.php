<?php

namespace App\Controllers\Api;


use DateTime;
use DateTimeZone;
use App\Models\SearchModel;
use App\Libraries\EsbLibrary;
use App\Models\WaterMeterModel;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\ResourceController;
use Config\Queue;

class MetersApiController extends ResourceController
{
  use ResponseTrait;
  protected $helpers = ['setting'];
  protected $searchModel;
  protected $apiKey;
  protected $waterMetersModel;
  public function __construct()
  {
    $this->searchModel = new SearchModel();
    $this->waterMetersModel = new WaterMeterModel();
    $this->apiKey = env('API_KEY');
  }

  public function getVariable($var)
  {
    return $this->request->getVar($var, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
  }

 



  private function validateApiKey($requestApiKey)
  {
    $esb = new EsbLibrary();

    if (empty($requestApiKey) || $requestApiKey != $this->apiKey) {
      $error =   [
        'message' => 'Invalid API KEY'
      ];
      return $esb->failureResponse($error);
    }

    return true; // API key is valid
  }

  public function verifiedMetersRequest()
  {

    $content = $this->request->getBody();
    $esb = new EsbLibrary();
    $reqData = $esb->verifyAndGetData($content);

    if (!$reqData) {
      return $esb->failureResponse("Invalid payload signature");
    }

    $esbBody = $reqData["esbBody"];
    $requestId = $reqData["requestId"];
    $apiKey = $esbBody['apiKey'];
    $meterSerialNumbers = $esbBody['meterSerialNumbers'];

    $queueData = [
      'requestId' => $requestId,
      'meterSerialNumbers' => $meterSerialNumbers,
    ];


    //add queueData to queue
    if (!empty($meterSerialNumbers)) {
      $queueService = service('queue');

      $queueService->push('sendmeterdataqueue', 'watermetersjob', $queueData, 'high');
    }


    $apiKeyValidation = $this->validateApiKey($apiKey);
    if ($apiKeyValidation !== true) {
      return $apiKeyValidation;
    }


    $ackData = [
      'ack' => true,
    ];

    // return $this->response->setJSON($ackData);

    return $esb->successResponse($ackData);
  }


  public function invite(){
    $queueService = service('queue');
    $queueData = [
      'phoneNumber' => '0659851709',
      'message' => 'Hello world, u re invited to join the Hackathon 2025',
    ];
    $queueService->push('invite', 'invitation', $queueData, 'high');
  }

  public function review(){
    $queueService = service('queue');
    $queueData = [
      'phoneNumber' => '0659851709',
      'message' => 'Whats up, we will have code review today and each saturday',
    ];
    $queueService->push('code', 'review', $queueData, 'high');
  }







}
