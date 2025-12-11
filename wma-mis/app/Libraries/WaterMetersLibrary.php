<?php

namespace App\Libraries;

use App\Libraries\SmsLibrary;
use App\Models\WaterMeterModel;



class WaterMetersLibrary
{
    public function getMeters($serialNumbers)
    {
        $waterMetersModel = new WaterMeterModel();
        $metersData = $waterMetersModel->getVerifiedMeters($serialNumbers);
        return $metersData;
    }


    public function pushMetersToGovesb($queuePayload)
    {
        $esb = new EsbLibrary();
        $payload = json_decode(json_encode($queuePayload));
        // $type = gettype($payload);
        // file_put_contents('billXml.xml',$type);

        // exit;
        $metersData = $this->getMeters($payload->meterSerialNumbers);

        // $sendToEsb = $esb->asyncSuccessResponse($metersData, $queuePayload['requestId']);
         $sendToEsb = $esb->asyncSuccessResponse($metersData, $payload->requestId);

        if ($sendToEsb) {
            // $this->govEsbModel->logContents();
            $smsLibrary = new SmsLibrary();
            $dateTime = date('Y-m-d H:i:s');
            $count = count($metersData);
            $smsLibrary->sendSms('0659851xxx', "$count Meters  Sent Successfully " . $dateTime);
        } 
    }
}
