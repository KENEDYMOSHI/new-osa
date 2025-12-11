<?php

namespace App\Libraries;

class AcknowledgementTr
{
    public function acknowledgementProcessing($response, $params)
    {
        // $env = setting('System.env');
        // // $env = 'testing';
        // if ($env == 'testing') {
        //     $gepgPublicKey = 'gepgpubliccertificate.pfx';
        //     $password = 'passpass';
        // } else {
        //     $gepgPublicKey = 'GePGNewPublicKey.pfx';
        //     $password = 'S3R1KAL1';
        // }

        $gepgPublicKey = 'GePGNewPublicKey.pfx';
        $password = 'S3R1KAL1';

        // $gepgPublicKey = 'gepgpubliccertificate.pfx';
        // $password = 'passpass';



        if (!empty($response)) {

            //Tag for response
            $dataTag = $params->dataTag;
            $sigTag = "signanture";

            $vData = $this->getDataString($response, $dataTag);
            $vSignature = $this->getSignatureString($response, $sigTag);

            if (!$pCertStore = file_get_contents("signature/$gepgPublicKey")) {
                // echo "Error: Unable to read the cert file\n";
                // echo file_put_contents("Response/Callback.txt", "Error: Unable to read the cert file\n");
                log_message('critical', 'Error: Unable to read the cert file');
                exit;
            } else {

                // echo file_put_contents("Response/Callback.txt", "start reading cert");
                //Read Certificate
                if (openssl_pkcs12_read($pCertStore, $pCertInfo, $password)) {

                    //Decode Received Signature String
                    $rawSignature = base64_decode($vSignature);

                    //Verify Signature and state whether signature is okay or 
                    openssl_verify($vData, $rawSignature, $pCertInfo['extracerts']['0']);
                }
            }


            if (!$prCertStore = file_get_contents("signature/wmaprivate.pfx")) {
                print "Error: Unable to read the cert file\n";
                // echo file_put_contents("Response/Callback.txt", 'Error: Unable to read the cert file');
                log_message('critical', 'Error: Unable to read the cert file');
                exit;
            } else {
                if (openssl_pkcs12_read($prCertStore, $certIfo, "wma_mis_Tz@255")) {
                    //Response Content Ack  
                    $content = $params->content;
                    //Create signature 
                    openssl_sign($content, $signature, $certIfo['pkey'], 'sha1WithRSAEncryption');
                    $signature = base64_encode($signature);
                    //Compose  response request
                    $data = "<Gepg>" . $content . "<signature>" . $signature . "</signature></Gepg>";
                }

                // file_put_contents("Response/Callback.txt", $data);
                log_message('critical',  'ACK DATA '.$data);

                echo $data;

                
            }
        }
    }




    public function getDataString($inputStr, $dataTag)
    {
        $dataStartPos = strpos($inputStr, $dataTag);
        $dataEndPos = strrpos($inputStr, $dataTag);
        $data = substr($inputStr, $dataStartPos - 1, $dataEndPos + strlen($dataTag) + 2 - $dataStartPos);
        return $data;
    }


    public function getSignatureString($inputStr, $signTag)
    {
        $sigStartPos = strpos($inputStr, $signTag);
        $sigEndPos = strrpos($inputStr, $signTag);
        $signature = substr($inputStr, $sigStartPos + strlen($signTag) + 1, $sigEndPos - $sigStartPos - strlen($signTag) - 3);
        return $signature;
    }
}
