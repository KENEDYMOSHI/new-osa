<?php

namespace App\Libraries;

use LSS\Array2XML;

class WmaGepgProcess
{

    public function billSubmission($content, $params)
    {
        $env = setting('System.env');
        //$env = 'testing';
        if ($env == 'testing') {
            $gepgPublicKey = 'gepgpubliccertificate.pfx';
            $password = 'passpass';
        } else {
            $gepgPublicKey = 'GePGNewPublicKey.pfx';
            $password = 'S3R1KAL1';
        }
      

        // $gepgPublicKey = 'GePGNewPublicKey.pfx';
        // $password = 'S3R1KAL1';

        // echo $content;
        // exit;
        if (!$cert_store = file_get_contents("signature/wmaprivate.pfx")) {

            echo "Error: Unable to read the cert file\n";
            exit;
        } else {
            if (openssl_pkcs12_read($cert_store, $cert_info, "wma_mis_Tz@255")) {

                // echo "Certificate Information\n";



                //create signature
                openssl_sign($content, $signature, $cert_info['pkey'], "sha1WithRSAEncryption");

                //output encrypted data base64 encoded
                $signature = base64_encode($signature);


                //Combine signature and content signed
                $data = "<Gepg>" . $content . " <gepgSignature>" . $signature . "</gepgSignature></Gepg>";


                $resultCurlPost = "";
            //    $serverIp = "https://uat1.gepg.go.tz/api/";
                $serverIp = 'http://154.118.230.202:80/api/'; //setting('Bill.apiUrl');


                $dataString = $data;
                // echo "Message details" . "\n" . $dataString . "\n";
                // echo "\n";
                // echo "Request Length :\n";
                // echo strlen($dataString);
                // echo "\n";

                // echo $dataString;
                // exit;Gepg-Com:default.sp.in

                $ch = curl_init($serverIp . $params->uri);
                curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt(
                    $ch,
                    CURLOPT_HTTPHEADER,
                    array(
                        'Content-Type:application/xml',
                        $params->GepgCom,
                        'Gepg-Code:SP419',
                        'Content-Length:' . strlen($dataString)
                    )
                );

                curl_setopt($ch, CURLOPT_TIMEOUT, 50);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 50);

                $resultCurlPost = curl_exec($ch);
                $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                if ($statusCode === 503) {
                    // $res = Array2XML::createXML([
                    //     'TrxStsCode' => 5101,
                    // ]);
                    return (object)[
                        'status' => 0,
                        'msg' => '(503)Service Unavailable  TRY LATER',
                        'TrxStsCode' => '',
                        'resultCurlPost' => '',
                    ];
                    // echo '503 Error: Service Unavailable    TRY LATER';
                } elseif ($statusCode >= 400) {
                    // echo 'HTTP Error: ' . $statusCode;
                    return (object)[
                        'status' => 0,
                        'msg' => 'HTTP Error: ' . $statusCode,
                        'TrxStsCode' => $statusCode,
                        'resultCurlPost' => '',
                    ];
                } else {
                    if (!empty($resultCurlPost)) {


                        return (object)[
                            'status' => 1,
                            'resultCurlPost' => $resultCurlPost,
                        ];
                        // return XML2Array::createArray($resultCurlPost);

                        //Tags used in substring response content
                        // $dataTag = "gepgBillSubReqAck";
                        $dataTag = $params->dataTag;
                        $sigTag = "gepgSignature";

                        //Get data and signature from response
                        $vData = $this->getDataString($resultCurlPost, $dataTag);
                        $vSignature = $this->getSignatureString($resultCurlPost, $sigTag);

                        // echo "\n";
                        // echo "Data Received:\n";
                        return $vData;
                        // echo "\n";
                        // echo "Data Length:\n";
                        // echo strlen($vData);
                        // echo "\n";
                        // echo "Signature Received:\n";
                        //echo $vSignature;
                        // echo "\n";

                        //Get Certificate contents
                        if (!$pCertStore = file_get_contents("signature/$gepgPublicKey")) {
                            // echo "Error: Unable to read the cert file\n";
                            return (object)[
                                'status' => 0,
                                'TrxStsCode' => '',
                                'msg' => 'Error: Unable to read the cert file',
                            ];
                            exit;
                        } else {

                            //Read Certificate
                            if (openssl_pkcs12_read($pCertStore, $pCertInfo, $password)) {
                                //Decode Received Signature String
                                $rawSignature = base64_decode($vSignature);

                                //Verify Signature and state whether signature is okay or not
                                openssl_verify($vData, $rawSignature, $pCertInfo['extracerts']['0']);
                                // $ok = openssl_verify($vData, $rawSignature, $pCertInfo['extracerts']['0']);
                                // if ($ok == 1) {
                                //     echo "Signature Status:";
                                //     echo "GOOD";
                                // } elseif ($ok == 0) {
                                //     echo "Signature Status:";
                                //     echo "BAD";
                                // } else {
                                //     echo "Signature Status:";
                                //     echo "UGLY, Error checking signature";
                                // }
                            }
                        }
                    } else {
                        // echo "No result Returned" . "\n";
                        return (object)[
                            'status' => 0,
                            'resultCurlPost' => '',
                            'TrxStsCode' => '',
                            'msg' => 'No result Returned From GePG'.$statusCode,
                        ];
                    }
                }
                curl_close($ch);
            } else {

                // echo "Error: Unable to read the cert store.\n";
                return (object)[
                    'status' => 0,
                    'resultCurlPost' => '',
                    'msg' => 'Error: Unable to read the cert store',
                ];

                // exit;
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
