<?php

namespace App\Controllers;

use App\Libraries\EsbLibrary;
use App\Controllers\BaseController;
use App\Models\PetroleumImportModel;
use CodeIgniter\HTTP\ResponseInterface;

class PetroleumImportController extends BaseController
{
    protected $model;
    protected $user;
    protected $token;
    protected $apiKey;
    //add a constructor method
    public function __construct()
    {
        $this->model = new PetroleumImportModel();
        $this->user = auth()->user();
        $this->token = csrf_hash();
        $this->apiKey = 'DjJnA1SsJNBtVA1pp1dHAJEeD4v3sdZG';
    }

    public function getVariable($variableName)
    {
        return  $this->request->getVar($variableName, FILTER_SANITIZE_SPECIAL_CHARS);
    }

    public function vessels()
    {
        $data['page'] = [
            'title' => 'Vessels Management',
            'heading' => 'Vessels Management',
        ];

        $data['user'] = $this->user;
        $data['vessels'] = $this->model->getVessels();
        return view('Pages/PetroleumImport/Vessels', $data);
    }
    public function importers()
    {
        $data['page'] = [
            'title' => 'Importers Management',
            'heading' => 'Importers Management',
        ];

        $data['user'] = $this->user;
        $data['importers'] = $this->model->getImporters();
        return view('Pages/PetroleumImport/Importers', $data);
    }
    public function petroleumData()
    {
        $data['page'] = [
            'title' => 'Petroleum Data Management',
            'heading' => 'Petroleum Data Management',
        ];
        $data['vessels'] = $this->model->getVessels();
        $data['importers'] = $this->model->getImporters();

        $data['user'] = $this->user;
        $data['petroleumData'] = $this->model->getPetroleumDataAll();

        // dd($data['petroleumData']);
        return view('Pages/PetroleumImport/PetroleumData', $data);
    }




    //add vessel method
    public function addVessel()
    {
        try {
            $vesselData = [
                'vesselName' => $this->getVariable('vesselName'),
                'productType' => $this->getVariable('productType'),
                'port' => $this->getVariable('port'),
                'arrivalDate' => $this->getVariable('arrivalDate'),
                'berthingDate' => $this->getVariable('berthingDate'),
                'port' => $this->getVariable('port'),
                'vesselId' => 'VS' . numString(10)
            ];
            $query = $this->model->addVessel($vesselData);
            if ($query) {
                $response = [
                    'status' => 1,
                    'msg' => 'Vessel added successfully',
                    'token' => $this->token
                ];
            } else {
                $response = [
                    'status' => 0,
                    'msg' => 'Failed to add vessel',
                    'token' => $this->token
                ];
            }
        } catch (\Throwable $th) {
            $response = [
                'status' => 0,
                'msg' => $th->getMessage(),
                'trace' => $th->getTrace(),
                'token' => $this->token
            ];
        }
        return $this->response->setJSON($response);
    }

    //add importer method
    public function addImporter()
    {
        try {
            $importerData = [
                'importerName' => $this->getVariable('importerName'),
                'licenseNumber' => $this->getVariable('licenseNumber'),
                'importerId' => 'IM' . numString(10)
            ];
            $query = $this->model->addImporter($importerData);
            if ($query) {
                $response = [
                    'status' => 1,
                    'msg' => 'Importer added successfully',
                    'token' => $this->token
                ];
            } else {
                $response = [
                    'status' => 0,
                    'msg' => 'Failed to add importer',
                    'token' => $this->token
                ];
            }
        } catch (\Throwable $th) {
            $response = [
                'status' => 0,
                'msg' => $th->getMessage(),
                'trace' => $th->getTrace(),
                'token' => $this->token
            ];
        }
        return $this->response->setJSON($response);
    }


    // add petroleum data method
    public function addPetroleumData()
    {
        try {
            $data = [
                'vesselId' => $this->getVariable('vesselId'),
                'importerId' => $this->getVariable('importerId'),
                'importStatus' => $this->getVariable('importStatus'),
                'storageDepotName' => $this->getVariable('storageDepotName'),
                'portOfDischarge' => $this->getVariable('portOfDischarge'),
                'billOfLadingNo' => $this->getVariable('billOfLadingNo'),
                'notifyingParty' => $this->getVariable('notifyingParty'),
                'billOfLadingQuantityMt' => $this->getVariable('billOfLadingQuantityMt'),
                'billOfLadingQuantityLitre' => $this->getVariable('billOfLadingQuantityLitre'),
                'loadPortQuantityMt' => $this->getVariable('loadPortQuantityMt'),
                'loadPortQuantityLitre' => $this->getVariable('loadPortQuantityLitre'),
                'arrivalQuantityMt' => $this->getVariable('arrivalQuantityMt'),
                'arrivalQuantityLitre' => $this->getVariable('arrivalQuantityLitre'),
                'dischargePortQuantityMt' => $this->getVariable('dischargePortQuantityMt'),
                'dischargePortQuantityLitre' => $this->getVariable('dischargePortQuantityLitre'),
                'outturnQuantityMt' => $this->getVariable('outturnQuantityMt'),
                'outturnQuantityLitre' => $this->getVariable('outturnQuantityLitre'),
                'differenceOutturnBillMt' => $this->getVariable('differenceOutturnBillMt'),
                'differenceOutturnBillLitre' => $this->getVariable('differenceOutturnBillLitre'),
                'differenceOutturnArrivalMt' => $this->getVariable('differenceOutturnArrivalMt'),
                'differenceOutturnArrivalLitre' => $this->getVariable('differenceOutturnArrivalLitre'),
            ];

            $query = $this->model->addPetroleumData($data);
            if ($query) {
                $response = [
                    'status' => 1,
                    'msg' => 'Petroleum data added successfully',
                    'token' => $this->token
                ];
            } else {
                $response = [
                    'status' => 0,
                    'msg' => 'Failed to add petroleum data',
                    'token' => $this->token
                ];
            }
        } catch (\Throwable $th) {
            $response = [
                'status' => 0,
                'msg' => $th->getMessage(),
                'trace' => $th->getTrace(),
                'token' => $this->token
            ];
        }
        return $this->response->setJSON($response);


        return $this->response->setJSON($response);
    }



    public function consolidatedReport()
    {
        try {

            $content = $this->request->getBody();
            $esb = new EsbLibrary();
            $reqData = $esb->verifyAndGetData($content);


            if (!$reqData) {
                return $esb->failureResponse([
                    'status' => 0,
                    'msg' => 'Invalid payload signature',
                ]);
            }

            $apiKey = $reqData['esbBody']['apiKey'];


            if ($apiKey != $this->apiKey || empty($apiKey)) {

                return $esb->failureResponse([
                    'status' => 0,
                    'msg' => 'Invalid API Key',

                ]);
            }

            $consolidated =  $this->model->getPetroleumDataAll();

            return $esb->successResponse([
                'status' => 1,
                // 'requestDate' => date('Y-m-d H:i:s'),
                'data' => $this->consolidated($consolidated),
            ]);
        } catch (\Throwable $th) {

            return $esb->failureResponse([
                'status' => 0,
                'msg' => $th->getMessage(),


            ]);
        }
    }


    public function consolidatedReportX()
    {
        try {
            $consolidated =  $this->model->getPetroleumDataAll();




            $response = [
                'status' => 1,
                'data' => $this->consolidated($consolidated),
                'token' => $this->token
            ];
        } catch (\Throwable $th) {
            $response = [
                'status' => 0,
                'msg' => $th->getMessage(),
                'token' => $this->token
            ];
        }
        return $this->response->setJSON($response);
    }



    public function consolidated($report)
    {
        $grouped = [];

        foreach ($report as $item) {
            $key = $item->vesselId;

            if (!isset($grouped[$key])) {
                $grouped[$key] = [
                    'vesselName' => $item->vesselName,
                    'vesselId' => $item->vesselId,
                    'arrivalDate' => substr($item->arrivalDate, 0, 10),
                    'berthingDate' => substr($item->berthingDate, 0, 10),
                    'productType' => $item->productType,
                    'petroleumData' => []
                ];
            }

            $grouped[$key]['petroleumData'][] = [
                'importerName' => $item->importerName,
                'importerId' => $item->importerId,
                'importStatus' => $item->importStatus,
                'storageDepotName' => $item->storageDepotName,
                'portOfDischarge' => $item->portOfDischarge,
                'billOfLadingNo' => $item->billOfLadingNo,
                'notifyingParty' => $item->notifyingParty,
                'billOfLadingQuantityMt' => (float) $item->billOfLadingQuantityMt,
                'billOfLadingQuantityLitre' => (float) $item->billOfLadingQuantityLitre,
                'loadPortQuantityMt' => (float) $item->loadPortQuantityMt,
                'loadPortQuantityLitre' => (float) $item->loadPortQuantityLitre,
                'arrivalQuantityMt' => (float) $item->arrivalQuantityMt,
                'arrivalQuantityLitre' => (float) $item->arrivalQuantityLitre,
                'dischargePortQuantityMt' => (float) $item->dischargePortQuantityMt,
                'dischargePortQuantityLitre' => (float) $item->dischargePortQuantityLitre,
                'outturnQuantityMt' => (float) $item->outturnQuantityMt,
                'outturnQuantityLitre' => (float) $item->outturnQuantityLitre,
                'differenceOutturnBillMt' => (float) $item->differenceOutturnBillMt,
                'differenceOutturnBillLitre' => (float) $item->differenceOutturnBillLitre,
                'differenceOutturnArrivalMt' => (float) $item->differenceOutturnArrivalMt,
                'differenceOutturnArrivalLitre' => (float) $item->differenceOutturnArrivalLitre,
            ];
        }

        return array_values($grouped);
    }
}
