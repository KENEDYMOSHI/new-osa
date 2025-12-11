<?php

namespace App\BackgroundTasks;

use LSS\XML2Array;
use App\Libraries\SmsLibrary;
use App\Libraries\XmlLibrary;

class ControlNumberTask
{
    protected $db;
    protected $xmlLibrary;
    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->xmlLibrary = new XML2Array;
    }

    public function processControlNumber(array $queueData)
    {
        $data = json_decode(json_encode($queueData));
        $xml = $data->xmlPayload;
        $array = XML2Array::createArray($xml);

        $controlNumberData = json_decode(json_encode($array));
      // print_r($controlNumberData->billData->date);

        $message = "Hello Your control number is " . $controlNumberData->billData->controlNumber ." and amount is " . $controlNumberData->billData->amounts;
        $sms = new SmsLibrary();
        $sms->sendSms($data->phoneNumber, $message); 


    }
}
