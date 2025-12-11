<?php

namespace App\Controllers;

use App\Models\LorriesModel;
use App\Models\MiscellaneousModel;
use App\Models\PortModel;
use App\Models\ProfileModel;
use App\Models\VtcModel;
use App\Models\WaterMeterModel;
use App\Models\PrePackageModel;




class Analytics extends BaseController
{

    private $uniqueId;
    //   public $uniqueId;
    private $role;
    private $city;
    private $portUnitModel;
    private $session;
    private $profileModel;
    private $prePackageModel;

    private $contacts;
    private $token;

    private $vtcModel;
    private $lorriesModel;
    private $waterMeterModel;
    private $regionTarget;

    public function __construct()
    {
        $this->portUnitModel = new PortModel();
        $this->profileModel = new ProfileModel();
        $this->session = session();
        $this->token = csrf_hash();

        $this->vtcModel = new VtcModel();
        $this->lorriesModel = new LorriesModel();
        $this->waterMeterModel = new WaterMeterModel();
        $this->prePackageModel = new PrePackageModel();
        $this->contacts = new MiscellaneousModel();
        $this->regionTarget = new MiscellaneousModel();

        $this->uniqueId =auth()->user()->unique_id;
        // $this->uniqueId = $this->session->get('loggedUser');
        $this->role = auth()->user()->role;
       

        // helper(['array', 'regions', 'date', 'documents']);
    }

    public function getVariable($var)
    {
        return $this->request->getVar($var, FILTER_SANITIZE_SPECIAL_CHARS);
    }

    public function targets()
    {

        $data['page'] = [
            'title' => 'Collection Targets',
            'heading' => 'Collection Targets',
        ];
        $uniqueId = $this->uniqueId;
        $role = $this->role;
        $data['profile'] = $this->profileModel->getLoggedUserData($uniqueId);
        $data['role'] = $role;
        return view('Pages/Target', $data);
    }

    public function saveRegionalTarget()
    {
        if ($this->request->getMethod() == 'POST') {
            $day = rand(1, 28);
            $month = $this->getVariable('targetMonth');
            $year = $this->getVariable('targetYear');

            $vtc = (int)$this->getVariable('vtcAmt');
            $sbl = (int)$this->getVariable('sblAmt');
            $water_meters = (int)$this->getVariable('waterMeterAmt');
            $data = [
                'region' => $this->getVariable('targetRegion'),
                'vtc_qty ' => $this->getVariable('vtc'),
                'sbl_qty ' => $this->getVariable('sbl'),
                'water_meters_qty ' => $this->getVariable('waterMeter'),
                //=====================================
                'vtc_amt ' => $this->getVariable('vtcAmt'),
                'sbl_amt ' => $this->getVariable('sblAmt'),
                'water_meters_amt ' => $this->getVariable('waterMeterAmt'),
                'month' => $month,
                'amount' => $vtc  + $sbl  + $water_meters,
                'year' => $year,
                'date' => date('Y-m-d', strtotime("$year-$month-$day")),
                'unique_id' => $this->uniqueId,
            ];

            // return json_encode([
            //     'data' => $data,
            //     'token' => $this->token

            // ]);

            // exit;

            $request = $this->regionTarget->saveRegionTarget($data);
            if ($request) {
                return json_encode([
                    'status' => 1,
                    'token' => $this->token

                ]);
            }
        }
    }
    public function saveActivityTarget()
    {
        if ($this->request->getMethod() == 'POST') {
            $data = [
                'region' => $this->city,
                'activity' => $this->getVariable('activity'),
                'amount' => $this->getVariable('amount'),
                'instruments' => $this->getVariable('instruments'),
                'month' => $this->getVariable('month'),
                'year' => $this->getVariable('year'),
                'unique_id' => $this->uniqueId,
            ];

            // return json_encode($data);
            // exit;

            $request = $this->regionTarget->saveActivityTarget($data);
            if ($request) {
                return json_encode([
                    'status' => 1,
                    'token' => $this->token
                    // 'targets' => $this->regionTarget->getRegionTarget(),
                ]);
            }
        }
    }
    public function updateActivityTarget()
    {
        if ($this->request->getMethod() == 'POST') {
            $id = $this->getVariable('id');
            $month = $this->getVariable('month');
            $year = $this->getVariable('year');
            $day = rand(1, 28);
            $data = [

                'activity' => $this->getVariable('activity'),
                'amount' => $this->getVariable('amount'),
                'instruments' => $this->getVariable('instruments'),
                'month' => $month,
                'year' => $year,
                'date' => date('Y-m-d', strtotime("$year-$month-$day")),
                'id' => $id,
            ];

            // return json_encode($data);
            // exit;

            $request = $this->regionTarget->updatedActivityTarget($data, $id);
            if ($request) {
                return json_encode([
                    'message' => 'Updated',
                    'token' => $this->token
                    // 'targets' => $this->regionTarget->getRegionTarget(),
                ]);
            }
        }
    }
    public function updateRegionTarget()
    {
        if ($this->request->getMethod() == 'POST') {
            $day = rand(1, 28);
            $id = $this->getVariable('targetId');
            $month = $this->getVariable('targetMonth');
            $year = $this->getVariable('targetYear');

            $vtc = (int)$this->getVariable('vtcAmt');
            $sbl = (int)$this->getVariable('sblAmt');
            $water_meters = (int)$this->getVariable('waterMeterAmt');
            $data = [
                'region' => $this->getVariable('targetRegion'),
                'vtc_qty ' => $this->getVariable('vtc'),
                'sbl_qty ' => $this->getVariable('sbl'),
                'water_meters_qty ' => $this->getVariable('waterMeter'),
                //=====================================
                'vtc_amt ' => $this->getVariable('vtcAmt'),
                'sbl_amt ' => $this->getVariable('sblAmt'),
                'water_meters_amt ' => $this->getVariable('waterMeterAmt'),
                'month' => $month,
                'amount' => $vtc  + $sbl  + $water_meters,
                'year' => $year,
                'date' => date('Y-m-d', strtotime("$year-$month-$day")),
                'unique_id' => $this->uniqueId,
            ];

            // return json_encode([
            //     'data' => $data,
            //     'token' => $this->token

            // ]);

            // exit;
            $request = $this->regionTarget->updateRegionTarget($id, $data);
            if ($request) {
                return json_encode([

                    'message' => 'Updated',
                    'status' => 1,
                    'token' => $this->token
                    // 'targets' => $this->regionTarget->getRegionTarget(),
                ]);
            }
        }
    }
    public function getRegionTargets()
    {
        return json_encode($this->regionTarget->getRegionTarget());
    }




    public function getActivityTargets()
    {
        return json_encode($this->regionTarget->getActivityTarget($this->city));
    }
    public function editRegionTarget()
    {
        $id = $this->getVariable('id');
        return json_encode(
            [
                'data' => $this->regionTarget->editRegionTarget($id),
                'token' => $this->token,
                'id' => $id,


            ]

        );
    }
    public function editActivityTarget()
    {
        $id = $this->getVariable('id');

        return json_encode(
            [
                'data' => $this->regionTarget->editActivityTarget($id),
                'token' => $this->token


            ]

        );
    }

    public function index()
    {

        $data['page'] = [
            'title' => 'Collection Analytics',
            'heading' => 'Collection Analytics',
        ];
        $uniqueId = $this->uniqueId;
        $role = $this->role;
        $data['profile'] = $this->profileModel->getLoggedUserData($uniqueId);
        $data['role'] = $role;

        return view('Pages/projection', $data);
    }



    public function strToInt($num)
    {
        return (int) str_replace(',', '', $num);
    }

    public function regionOverallTargetAmount()
    {

        $amount = 0;
        $target = $this->regionTarget->readOverallTarget($this->city);

        if (count($target) > 0) {
            foreach ($target as $tg) {
                $amount += $tg->amount;
            }
        } else {

            return 0;
        }

        return $amount;
    }
    //=====================================
    public function regionTargetAmount($month, $region)
    {

        $target = $this->regionTarget->readRegionTarget($month, $region);
        if ($target != '') {
            return (int) $target->amount;
        } else {
            return 1;
        }
    }

    public function getActivityMonthlyTarget($region, $month, $year, $activity)
    {
        $target = $this->regionTarget->getActivityMonthlyTarget($region, $month, $year, $activity);
        if ($target != '') {
            return $target->amount;
        } else {
            return 0.01;
        }
    }

    public function getActivityAnnualTarget($region, $year, $activity)
    {
        $target = $this->regionTarget->getActivityAnnualTarget($region, $year, $activity);
        if ($target != '') {
            return $this->strToInt(totalAmount($target));
        } else {
            return 0.01;
        }
    }
    public function getActivityMonthlyTargetInstruments($region, $month, $year, $activity)
    {
        $target = $this->regionTarget->getActivityMonthlyTarget($region, $month, $year, $activity);
        if ($target != '') {
            return $target->instruments;
        } else {
            return 0;
        }
    }
    public function getActivityAnnualTargetInstruments($region, $year, $activity)
    {
        $instruments = 0;
        $targets = $this->regionTarget->getActivityAnnualTargetInstruments($region, $year, $activity);
        if (count($targets) > 0) {
            foreach ($targets as $target) {
                $instruments += (int) $target->instruments;
            }
        } else {
            return 0;
        }
        return $instruments;
    }



    public function getTarget($month, $year)
    {
        return $this->regionTarget->fetchTarget($month, $year);
    }

    public function xxx()
    {
        $month = $this->getVariable('month');
        $year = $this->getVariable('year');
        $region = $this->getVariable('region');



        $params = [];
        // $targetParams = [];
        $role = $this->role;
        $collectionRegion = $this->city;




        //=====================================




        if ($role == 1 || $role == 2) {



            if ($month == '0') {

                $QrParams = [
                    'customers.region' => $collectionRegion,
                    'created_on >=' => $year . '-07-01',
                    'created_on <=' => $year + 1 . '-06-30'
                ];
                $targetParams = [
                    'region' => $collectionRegion,
                    'date >=' => $year . '-07-01',
                    'date <=' => $year + 1 . '-06-30'
                ];
            } else {
                $QrParams = [
                    'customers.region' => $collectionRegion,
                    'YEAR(created_on) =' => $year,
                    'MONTH(created_on)' => $month
                ];

                $targetParams = ['region' => $collectionRegion,  'year' => $year, 'month' => $month];
            }
        } elseif ($role == 3 || $role == 7) {


            if ($month == '0') {


                if ($region == 'Tanzania') {
                    $QrParams = [
                        'created_on >=' => $year . '-07-01',
                        'created_on <=' => $year + 1 . '-06-30'
                    ];

                    $targetParams = [
                        'date >=' => $year . '-07-01',
                        'date <=' => $year + 1 . '-06-30'
                    ];
                } else {
                    $QrParams = [
                        'customers.region' => $region,
                        'created_on >=' => $year . '-07-01',
                        'created_on <=' => $year + 1 . '-06-30'
                    ];

                    $targetParams = [
                        'region' => $region,
                        'date >=' => $year . '-07-01',
                        'date <=' => $year + 1 . '-06-30'
                    ];
                }
            } else {


                if ($region == 'Tanzania') {
                    $QrParams = [
                        'YEAR(created_on)' => $year, 'MONTH(created_on)' => $month
                    ];

                    $targetParams = [
                        'year' => $year, 'month' => $month
                    ];
                } else {

                    $QrParams = ['customers.region' => $region,  'YEAR(created_on)' => $year, 'MONTH(created_on)' => $month];
                    $targetParams = ['region' => $region,  'year' => $year, 'month' => $month];
                    // $QrParams = [
                    //     'customers.region' => $region,
                    //     'created_on >=' => $year . '-07-01',
                    //     'created_on <=' => $year + 1 . '-06-30'
                    // ];

                    // $targetParams = [
                    //     'region' => $region,
                    //     'date >=' => $year . '-07-01',
                    //     'date <=' => $year + 1 . '-06-30'
                    // ];
                }



                // $QrParams =['YEAR(created_on)' => $year, 'MONTH(created_on)' => $month];
                // $targetParams = ['year' => $year, 'month' => $month];
            }
        }





        $vtc = $this->vtcModel->collectionSum($QrParams) != [] ? (int)$this->vtcModel->collectionSum($QrParams)[0]->amount : 0;
        $sbl = $this->lorriesModel->collectionSum($QrParams) != [] ? (int)$this->lorriesModel->collectionSum($QrParams)[0]->amount : 0;
        $waterMeter = $this->waterMeterModel->collectionSum($QrParams) != [] ? (int)$this->waterMeterModel->collectionSum($QrParams)[0]->amount : 0;
        //$prepackage = (int)$this->prePackageModel->collectionSum($QrParams)[0]->amount;

        //123




        $theTarget = $this->regionTarget->fetchTarget($targetParams)[0];

        return $this->response->setJSON(
            [

                'vtc' =>  [
                    'collectedAmount' => $vtc,
                    'instruments' => (int)$theTarget->vtc_qty,
                    'targetAmount' => (int)$theTarget->vtc_amt
                ],

                'sbl' =>  [
                    'collectedAmount' => $sbl,
                    'instruments' => (int)$theTarget->sbl_qty,
                    'targetAmount' => (int)$theTarget->sbl_amt
                ],
                'waterMeter' =>  [
                    'collectedAmount' => $waterMeter,
                    'instruments' => (int)$theTarget->water_meters_qty,
                    'targetAmount' => (int)$theTarget->water_meters_amt
                ],

                'prepPackage' =>  [
                    'collectedAmount' => [],
                    // 'instruments' => (int)$theTarget->water_meters_qty,
                    // 'targetAmount' => (int)$theTarget->water_meters_amt
                ],

                'target' => (int)$theTarget->amount,
                'token' => $this->token,
                // 'targetParr'=> $targetParams,

                'total' => $vtc + $sbl + $waterMeter
            ]
        );
    }

    public function activitiesInRegion()
    {
        $data = [];
        $month = $this->getVariable('month');
        $year = $this->getVariable('year');
        $region = $this->getVariable('region');

        $vtc = $this->vtcModel->vtcMonthlyReportDirectorRegional($month, $year, $region);
        $vtcTargetAmount = $this->getActivityMonthlyTarget($region, $month, $year, 'vtc');
        $vtcTargetInstruments = $this->getActivityMonthlyTargetInstruments($region, $month, $year, 'vtc');

        $sbl = $this->lorriesModel->sblMonthlyReportDirectorRegional($month, $year, $region);
        $sblTargetAmount = $this->getActivityMonthlyTarget($region, $month, $year, 'sbl');
        $sblTargetInstruments = $this->getActivityMonthlyTargetInstruments($region, $month, $year, 'sbl');

        $waterMeters = $this->waterMeterModel->waterMeterMonthlyReportDirectorRegional($month, $year, $region);
        $waterMeterTargetAmount = $this->getActivityMonthlyTarget($region, $month, $year, 'waterMeter');
        $waterMeterTargetInstruments = $this->getActivityMonthlyTargetInstruments($region, $month, $year, 'waterMeter');

        // $sblTargetAmount = $this->getActivityMonthlyTarget($this->city, $month, $year, 'sbl');

        foreach ($vtc as $vehicle) {

            array_push($data, [
                'activity' => 'vtc',
                'target' => $vtcTargetAmount,
                'region' => $region,
                'amount' => $vehicle->amount,
                'payment' => $vehicle->payment,
                'instruments' => count($vtc),
                'instrumentsTarget' => $vtcTargetInstruments,

            ]);
        }

        foreach ($sbl as $vehicle) {

            array_push($data, [
                'activity' => 'sbl',
                'target' => $sblTargetAmount,
                'region' => $region,
                'amount' => $vehicle->amount,
                'payment' => $vehicle->payment,
                'instruments' => count($sbl),
                'instrumentsTarget' => $sblTargetInstruments,

            ]);
        }
        foreach ($waterMeters as $waterMeter) {

            array_push($data, (object) [
                'activity' => 'waterMeter',
                'target' => $waterMeterTargetAmount,
                'region' => $region,
                'amount' => $waterMeter->amount,
                'payment' => $waterMeter->payment,
                'instruments' => meterQuantityAll($waterMeters),
                'instrumentsTarget' => $waterMeterTargetInstruments,
            ]);
        }

        return json_encode($data);
    }
}
