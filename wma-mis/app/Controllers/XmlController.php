<?php

namespace App\Controllers;

use DOMDocument;
use LSS\Array2XM;
use LSS\Array2XML;
use LSS\XML2Array;
use SimpleXMLElement;
use GuzzleHttp\Client;
use BaconQrCode\Writer;
use GuzzleHttp\Psr7\Request;
use App\Controllers\BaseController;
use App\Models\BillModel;

class XmlController extends BaseController
{


    public function loop()
    {

        $billModel = new BillModel();
        $updated = false;

        // Connect to the database


        // Continuously check the record's status
        while (!$updated) {
            // Select the record from the database
            $result = $billModel->getB;
            $row = $result->fetch_assoc();

            // Check if the record has been updated
            if ($row['status'] == 'updated') {
                $updated = true;
            } else {
                // Wait for a little bit before checking again
                sleep(1);
            }
        }

        // The record has been updated, so select it

    }








    public function fileContent()
    {
        echo file_get_contents('signature/text.txt');
    }

    public function billDate()
    {
        $date = date('Y-m-d\TH:i:s', strtotime('18-December-2022'));



        echo $date;
    }

    function arrayXml()
    {



        // Associative array
        $empData = array(
            'title' => 'Employee Details',
            'employee' => array(
                array('firstname' => 'Sanjay', 'lastname' => 'Kumar', 'username' => 'sk987'),
                array('firstname' => 'Ashish', 'lastname' => 'Kumar', 'username' => 'ashish.985'),
                array('firstname' => 'Vijay', 'lastname' => 'Rohila', 'username' => 'vijayk.ro'),
                array('firstname' => 'Dhananjay', 'lastname' => 'Negi', 'username' => 'dj.negi'),
                array('firstname' => 'Siddharth', 'lastname' => 'Singh', 'username' => 'sid.992')
            )
        );

        // Function to convert Array To XML
        function createXMLFile($empData)
        {

            $xmlDocument = new DOMDocument();

            $root = $xmlDocument->appendChild($xmlDocument->createElement("gepgBillSubReq"));
            $empRecords = $root->appendChild($xmlDocument->createElement('people'));
            foreach ($empData['employee'] as $employee) {
                if (!empty($employee)) {
                    $empRecord = $empRecords->appendChild($xmlDocument->createElement('employee'));
                    foreach ($employee as $key => $val) {
                        $empRecord->appendChild($xmlDocument->createElement($key, $val));
                    }
                }
            }
            $xmlDocument->formatOutput = true;
            return  $xmlDocument->saveXML();
        }

        echo createXMLFile($empData); // Function call


    }


    public function curl()
    {



     




        $xml = "<?xml version='1.0' encoding='UTF-8' standalone='yes'?>
<Gepg>
    <gepgSpReconcResp>
        <ReconcBatchInfo>
            <SpReconcReqId>1825734690</SpReconcReqId>
            <SpCode>SP19960</SpCode>
            <SpName>WMA</SpName>
            <ReconcStsCode>7101</ReconcStsCode>
        </ReconcBatchInfo>
        <ReconcTrans>
            <ReconcTrxInf>
                <SpBillId>T487hVctZYmjfiszPp2ra0XOG9lyJgwoxk3qe5d6HU</SpBillId>
                <BillCtrNum>199600000148</BillCtrNum>
                <pspTrxId>1672746434856</pspTrxId>
                <PaidAmt>410000</PaidAmt>
                <CCy>TZS</CCy>
                <PayRefId>923003000023547</PayRefId>
                <TrxDtTm>2023-01-03 14:47:14.0</TrxDtTm>
                <CtrAccNum>GEPG0123456</CtrAccNum>
                <UsdPayChnl>Tigo</UsdPayChnl>
                <PspName>GePG</PspName>
                <PspCode>PSP047</PspCode>
                <DptCellNum>255689520000</DptCellNum>
                <DptName>Buffy Ortiz</DptName>
                <DptEmailAddr></DptEmailAddr>
                <Remarks>Successful</Remarks>
                <ReconcRsv1>Buffy Ortiz</ReconcRsv1>
                <ReconcRsv2></ReconcRsv2>
                <ReconcRsv3></ReconcRsv3>
            </ReconcTrxInf>
            <ReconcTrxInf>
                <SpBillId>T487hVctZYmjfiszPp2ra0XOG9lyJgwoxk3qe5d6HU</SpBillId>
                <BillCtrNum>19960000085</BillCtrNum>
                <pspTrxId>1672746434856</pspTrxId>
                <PaidAmt>25000</PaidAmt>
                <CCy>TZS</CCy>
                <PayRefId>923003000023547</PayRefId>
                <TrxDtTm>2023-01-03 14:47:14.0</TrxDtTm>
                <CtrAccNum>GEPG0123456</CtrAccNum>
                <UsdPayChnl></UsdPayChnl>
                <PspName>GePG</PspName>
                <PspCode>PSP047</PspCode>
                <DptCellNum>2554741265</DptCellNum>
                <DptName>Jane Doe</DptName>
                <DptEmailAddr></DptEmailAddr>
                <Remarks>Successful</Remarks>
                <ReconcRsv1>Jane Doe</ReconcRsv1>
                <ReconcRsv2></ReconcRsv2>
                <ReconcRsv3></ReconcRsv3>
            </ReconcTrxInf>
        </ReconcTrans>
    </gepgSpReconcResp>
    <gepgSignature>
        O9HcolmCKSRkNiGApohQtJ7cTfkICtqUjXfjNPN17ZmtNNccvWVAnG7DC2aDtB9lao2RzsKPODtj6GcS/HWt2Fuo7kS4pK/8xoBQ27GSTFyH2RDe1BdfO06UkvESt0DLytXPfO7nRkbircGFJpEaKA2rvqB31Gv5Y4IBSbG2rvOfBs/SSsskkMSedxr4fSsbltvpii0QDU9kTDWZ80njXzq2peY2Awp24JuIjTVOg7gTtU0SQKWzThYI9WnezGq3WYdcp1kgI1BAbZSt17oTJV1nRtQM7/ee+bejJoX/h5rHpjOfIAXc/cE2S/nvrt5cqU3FFhJ+xnRNGcuMKjUHFg==
    </gepgSignature>
</Gepg>";



        $options = [
            CURLOPT_URL            => 'https://uat1.gepg.go.tz',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $xml,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/xml',
            ],
        ];

        // Initialize cURL session
        $ch = curl_init();
        curl_setopt_array($ch, $options);

        // Execute cURL session and get the response
        $response = curl_exec($ch);

        // Check for cURL errors
        if (curl_errno($ch)) {
            // Handle cURL error
            echo 'cURL Error: ' . curl_error($ch);
        }

        // Close cURL session
        curl_close($ch);

        // Process the response if needed
        echo 'Response: ' . $response;
    }
}
