<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CertificateModel;


class VerifyController extends BaseController
{
    public function index($type, $certificateId)
    {
        try {


            $certificateModel = new CertificateModel();

            if ($type === 'Correctness') {
                $certificateData = $certificateModel->fetchCorrectnessCertificate(['certificateId' => $certificateId]);
            } else if ($type === 'Conformity') {
                $certificateData = $certificateModel->fetchConformityCertificate(['certificateId' => $certificateId]);
            } else {
                $certificateData = null;
            }

            if (!$certificateData) {
                $data['message'] = 'Invalid Certificate';
                return view('Pages/Invalid', $data);
            }


            $billItems =  $certificateModel->getItems(['controlNumber' => $certificateData->controlNumber]);

            $data['certificate'] = $certificateData;
            $data['region'] = $certificateModel->region($certificateData->region)->centerName;
            $data['items'] = array_map(fn($item) => $item->ItemName, $billItems);
            $data['officer'] = $certificateModel->officer($certificateData->officer);
            $data['amount'] = array_sum(array_map(fn($item) => $item->amount, $billItems));
            $data['type'] = strtoupper($type);
            $data['activity'] = is_array($certificateData->activities) ? $certificateData->activities : [$certificateData->activities];



            return view('Pages/VerifyCertificate', $data);
        } catch (\Throwable $th) {

            // echo $th->getMessage();

            $data['message'] = 'Something Went Wrong Scan The QR Code Again';
            return view('Pages/Invalid', $data);
        }
    }
}
