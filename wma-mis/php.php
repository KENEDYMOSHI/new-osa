<?php 
function getPayment(){
 //get data from a callback as xml
$response = $this->request->getBody();
$dataArray = XML2Array::createArray($response);

$data = json_decode(json_encode($dataArray));
$paymentResponse = $data->Gepg->pmtSpNtfReq;
$header = $paymentResponse->PmtHdr;
$payments = $paymentResponse->PmtDtls->PmtTrxDtl;

$jsonStr = json_encode($payments);
$requestId = $header->ReqId;

$queueData = [
    'requestId' => $requestId,
    'body' => $jsonStr
];

//here i need a logic to add $queueData to queue and process later

//return ack
echo '<ack>true</ack>';


//some other processing logic

}


function processPayment(){
// get data from the queue here and process them
}

// i need a queue (composer require enqueue/enqueue) for a codeigniter 4 app which is effective and less complicated which does not require me to install stuffs in my ubuntu server. i need just a composer package also a worker to make sure my queue is active 24/7
?>