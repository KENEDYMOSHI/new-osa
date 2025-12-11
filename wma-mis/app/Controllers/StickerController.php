<?php

namespace App\Controllers;

use App\Models\AppModel;


use App\Libraries\StickerLibrary;
use App\Controllers\BaseController;
use App\Models\CertificateModel;
use App\Models\CertModel;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;


class StickerController extends BaseController
{

    protected $user;
    protected $uniqueId;
    protected $appModel;
    protected $token;


    public function __construct()
    {
        $this->appModel = new AppModel();
        helper('date');
        $this->token = csrf_hash();
        // $this->user = auth()->user();
        // $this->uniqueId = $this->user->unique_id;



        //     $dumpSettings = array(),
        // $pdoSettings = array()
    }

    public function getVariable($var)
    {
        return $this->request->getVar($var, FILTER_SANITIZE_SPECIAL_CHARS);
    }

    public function index()
    {



        $data['page'] = ['title' => 'Sticker Search', 'heading' => 'Sticker Search',];


        return view('Pages/Search/StickerSearch', $data);
    }


    public function searchSticker()
    {
        try {
            $controlNumber = trim($this->getVariable('controlNumber'));
            $activity = $this->getVariable('activity');



            // Set the folder path
            $folderPath = 'public/stickers/'; // Adjust the folder path accordingly

            // Set the activity and controlNumber
            // $controlNumber = '199600002296';
            // $activity = '142101210004';

            // Combine controlNumber and activity to form the expected name
            $name = "$controlNumber-$activity";

            // Build the search pattern
            $searchPattern = $folderPath . "*{$name}*";

            // Use glob to search for matching files
            $matchingFiles = glob($searchPattern);

            $filePathArray = [];

            if (!empty($matchingFiles)) {
                // Files matching the pattern were found
                foreach ($matchingFiles as $file) {
                    // Get the relative path to the file from the public directory
                    $relativeFilePath = str_replace(ROOTPATH, '', $file);

                    // Construct the full URL using base_url()
                    $fileUrl = base_url($relativeFilePath);

                    // Add the URL to the filePathArray
                    $filePathArray[] = $fileUrl;
                }
                return $this->response->setJSON([
                    'status' => 1,
                    'stickers' => $this->renderSticker($filePathArray),
                    'token' => $this->token,
                    'msg' => '',

                ]);
            } else {
                $query = $this->appModel->fetchStickers(['activity' => $activity, 'controlNumber' => $controlNumber]);
                if (!empty($query)) {



                    return $this->response->setJSON([
                        'status' => 1,
                        'stickers' => $this->renderSticker($query),
                        'msg' => '',
                        'token' => $this->token
                    ]);
                } else {
                    return $this->response->setJSON([
                        'status' => 0,
                        'stickers' => '<h5>No Sticker Found</h5>',

                        'token' => $this->token
                    ]);
                }
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

    public function renderSticker($stickers)
    {
        $div = '';

        $all = array_map(function ($sticker) {
  
            $sticker->verificationDate = date('d-m-Y', strtotime($sticker->verificationDate));
            $sticker->nextVerification = date('d-m-Y', strtotime($sticker->dueDate));
            $sticker->instrument = $this->getInstrument( $sticker->activity);
            return $sticker;
        }, $stickers);
        $allStickers = json_encode($all);

        foreach ($stickers as $sticker) {
           // $certificate = (new CertificateModel())->fetchCorrectnessCertificate(['controlNumber' => $sticker->controlNumber]);

            $stickerData = (object)[
                'stickerId' => $sticker->stickerId,
                'instrument' => $this->getInstrument($sticker->activity),
                'verificationDate' => date('d-m-Y', strtotime($sticker->verificationDate)),
                'nextVerification' => date('d-m-Y', strtotime($sticker->dueDate)),
                'certificateNumber' => '',// $certificate->certificateNumber ?? 'N/A',
                'stickerNumber' => $sticker->stickerNumber,
            ];


            $jsonStickerData = json_encode($stickerData);

            $emblem = base_url('assets/images/emblem.png');
            $wmaLogo = base_url('assets/images/wma1.png');

            $url = base_url("verification/verifySticker/$sticker->stickerId");

            $qrCode = QRCode($url);
            // $instrument =($sticker->instrument);

            $div .= <<<HTML
                   

                <div class="col-md-3 mb-4">
                    <div class="stickerBody">
                        <textarea name="" id="$stickerData->stickerNumber" hidden >$jsonStickerData</textarea>
                        <div class="stickerHeader">
                            <div class="leftLogo">
                                <img src="$emblem" alt="">
                            </div>
                            <div class="heading">
                                <h5>THE UNITED REPUBLIC OF TANZANIA</h5>
                                <h5>WEIGHTS AND MEASURES AGENCY </h5>
                            </div>
                        </div>
                        <div class="stickerContent">
                            <div class="details">
                                <h6>Instrument: $sticker->instrument</h6>
                                <h6>Verification Date:  $stickerData->verificationDate</h6>
                                <h6>Next Verification:  $stickerData->nextVerification</h6>
                                <h6>Sticker Number: $stickerData->stickerNumber </h6>
                            </div>
                            <div class="wmaLogo">
                                <img src="$wmaLogo" alt="">
                            </div>
                        </div>
                        <div class="stickerFooter">
                            <div class="qrCode">
                                <img src="$qrCode" alt="">
                                <!-- <img src="https://cdn-icons-png.flaticon.com/128/14021/14021433.png" alt=""> -->
                            </div>
                            <!-- <div class="stickerNumber">
                                <h4>  $stickerData->certificateNumber</h4>
                            </div> -->
                        </div>
                    </div>
                    <!-- print button -->
                    <div class="d-flex justify-content-end mt-1">
                        <button class="btn btn-primary" onclick="printSticker('$stickerData->stickerNumber')"> <i class="fal fa-print"></i>Print Sticker</button>
                
                    </div>
                </div>
                
        
            HTML;
        }

        return <<<HTML
                 <div class="row">
                 <div class="col-md-12 mb-2">
                    <button class="btn btn-primary" onclick="printAllStickers()"><i class="fal fa-print"></i> Print All Stickers</button>
                </div>
                 </div>
                 <div class="row">
                    <div class="col-md-12">
                        <textarea name="" class="form-control mb-2" id="allStickers" hidden >$allStickers</textarea>
                    </div>
                    $div
                   
                </div>   
                
        HTML;
    }



    public function getInstrument($code)
    {
        $codes = [
            '142101210003' => 'VTV',
            '142101210004' => 'WB',
            '142101210005' => 'FST',
            '142101210006' => 'BST',
            '142101210007' => 'P',
            '142101210008' => 'WGT',
            '142101210009' => 'FP',
            '142101210010' => 'CNGFS',
            '142101210011' => 'FM',
            '142101210012' => 'CHP',
            '142101210013' => 'WM',
            '142101210014' => 'MS',
            '142101210015' => 'PG',
            '142101210016' => 'PT',
            '142101210017' => 'TMX',
            '142101210018' => 'MR',
            '142101210019' => 'TM',
            // '142101210020' => 'MLE',
            '142101210021' => 'BRIM',
            '142101210022' => 'SY',
            '142101210023' => 'SDW',
            '142101210024' => 'CS',
            '142101210025' => 'PS',
            '142101210026' => 'SB',
            '142101210027' => 'BAL',
            '142101210028' => 'KOR',
            '142101210029' => 'VIB',
            '142101210030' => 'PIS',
            '142101210031' => 'AXW',
            '142101210032' => 'AUW',
            '142101210033' => 'BS',
            '142101210034' => 'SY',
            '142101210035' => 'SBL',
            '142101210036' => 'EM',
            '142101210037' => 'OMI',
            '142101210038' => 'OML',
            '142101210039' => 'DM',
            '142101210040' => 'WT',
            '142201611278' => 'MR',
            '142202080006' => 'FPF',
        ];

        if (array_key_exists($code, $codes)) {
            return $codes[$code];
        } else {
            return '0000';
        }
    }
}
