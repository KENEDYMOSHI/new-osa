<?php
$responseBody = file_get_contents('php://input');

echo file_put_contents("GePG_Response.txt", file_get_contents('php://input'));

if (!empty($responseBody)) {

    //Tag for response
    $dataTag = "gepgBillSubResp";
    $sigTag = "gepgSignature";

    $vData = getDataString($responseBody, $dataTag);
    $vSignature = getSignatureString($responseBody, $sigTag);

    if (!$pCertStore = file_get_contents("gepgpubliccertificate.pfx")) {
        echo "Error: Unable to read the cert file\n";
        exit;
    } else {

        //Read Certificate
        if (openssl_pkcs12_read($pCertStore, $pCertInfo, "passpass")) {

            //Decode Received Signature String
            $rawSignature = base64_decode($vSignature);

            //Verify Signature and state whether signature is okay or not
            $ok = openssl_verify($vData, $rawSignature, $pCertInfo['extracerts']['0']);
            if ($ok == 1) {
                echo "\nResponse Signature Status:";
                echo "GOOD\n";
            } elseif ($ok == 0) {
                echo "\nResponse Signature Status:";
                echo "BAD\n";
            } else {
                echo "\n\n Response Signature Status:";
                echo "UGLY, Error checking signature";
            }
        }
    }


    if (!$cert_store = file_get_contents("gepgclientprivate.pfx")) {
        print "Error: Unable to read the cert file\n";
        exit;
    } else {
        if (openssl_pkcs12_read($cert_store, $cert_info, "passpass")) {
            //Response Content Ack  
            $responseContentAck = "<gepgBillSubRespAck><TrxStsCode>7101</TrxStsCode></gepgBillSubRespAck>";
            //Create signature 
            openssl_sign($responseContentAck, $signature, $cert_info['pkey'], "sha1WithRSAEncryption");
            $signature = base64_encode($signature);
            //Compose  response request
            $data = "<Gepg>" . $responseContentAck . "<gepgSignature>" . $signature . "</gepgSignature></Gepg>";
        }
        echo $data;
    }
}

function getDataString($inputStr, $dataTag)
{
    $dataStartPos = strpos($inputStr, $dataTag);
    $dataEndPos = strrpos($inputStr, $dataTag);
    $data = substr($inputStr, $dataStartPos - 1, $dataEndPos + strlen($dataTag) + 2 - $dataStartPos);
    return $data;
}

function getSignatureString($inputStr, $signTag)
{
    $sigStartPos = strpos($inputStr, $signTag);
    $sigEndPos = strrpos($inputStr, $signTag);
    $signature = substr($inputStr, $sigStartPos + strlen($signTag) + 1, $sigEndPos - $sigStartPos - strlen($signTag) - 3);
    return $signature;
}
