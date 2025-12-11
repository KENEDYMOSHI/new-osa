<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class MisConfig extends BaseConfig
{

    public  $Gfs = [
        'vtv' => '142101210024',
        'sbl' => '142101210025',
        'waterMeter' => '142101210026',
        'prePackages' => '142101210031',
        'bst' => '142101210028',
        'wagonTank' => '142101210029',
        'fuelPump' => '142101210030',
        'counterScale' => '142101210032',
        'flowMeter' => '142101210022',
        'checkPump' => '142101210033',
        'fst' => '142101210034',
        'springBalance' => '142101210004',
        'fine' => '140202'
    ];



    public  $liveGfs = [
        'vtv' => '142101210003',
        'weighBridge' => '142101210004',
        'fst' => '142101210005',
        'bst' => '142101210006',
        'prePackages' => '142101210007',
        'wagonTank' => '142101210008',
        'fuelPump' => '142101210009',
        'cngFillingStation' => '142101210010',
        'flowMeter' => '142101210011',
        'checkPump' => '142101210012',
        'waterMeter' => '142101210013',
        'metrological' => '142101210014',
        'pressureGauges' => '142101210015',
        'provingTank' => '142101210016',
        'taxiMeter' => '142101210017',
        'metreRule' => '142101210018',
        'tapeMeasure' => '142101210019',
        'measuresOfLength' => '142101210020',
        'brimMeasureSystem' => '142101210021',
        'steelYard' => '142101210022',
        'suspendedDigitalWare' => '142101210023',
        'counterScale' => '142101210024',
        'platformScale' => '142101210025',
        'springBalance' => '142101210026',
        'balance' => '142101210027',
        'koroboi' => '142101210028',
        'vibaba' => '142101210029',
        'pishi' => '142101210030',
        'weigher' => '142101210031',
        'automaticWeigher' => '142101210032',
        'beamScale' => '142101210033',
        'sbl' => '142101210035',
        'electricityMeter' => '142101210036',
        'otherMeasuringInstrument' => '142101210037',
        'otherMeasuresOfLength' => '142101210038',
        'domesticGasMeter' => '142101210039',
        'weights' => '142101210040',
        'miscellaneousReceipts' => '142201611278',
        'fine' => '142202080006',
    ];


    public $billTesting = [

        'spCode' => 'SP19960',
        'subSpCode' => '1001',
        'systemId' => 'TWMATR001',
        'apiUrl' => 'https://uat1.gepg.go.tz/api/',

        'systemCode' => 'TWMATR001',
        'spGroupCodeCombined' => 'SPG1005',
        'spGroupCodeSingle' => 'SP99419',
        'wmaSpCode' => 'SP19960',
        'wmaSubSpCode' => '1001',
        'trSpCode' => 'SP19966',
        'trSubSpCode' => '1001',
        'gepgApiUrl' => 'https://uat1.gepg.go.tz/api/'
    ];

    /*

    $spGroupCode = 'SPG1103';
    $systemCode = 'TWMATR001';
    $wmaSpCode = 'SP19960';
    $trSpCode = 'SP19966';



    <SpCode>SP99419</SpCode>
<SpCode>SP99517</SpCode>


<CollSp>SP99419</CollSp>
<CollSp>SP99517</CollSp>

    */

    public $billLive = [

        'apiUrl' => 'http://154.118.230.202/api/',
        'apiUrlGepg' => 'http://154.118.230.202:80/api/',
        'systemCode' => 'LWMA003',
        'systemId' => 'LWMA003',
        'spGroupName' => 'WMATROCOMBINE',
        'spGroupCodeCombined' => 'SPG1005',
        'spGroupCodeSingle' => 'SP99419',

        //wma params
        'wmaSpCode' => 'SP99419', //'SP419',
        'wmaSubSpCode' => '1002',

        //TR params
        'trSpCode' => 'SP99517',//'SP517',
        'trSubSpCode' => '1002',
    ];



















    // public $billLive = [

    //     'spCode' => 'SP419',
    //     'spGroupName' => 'WMATROCOMBINE',
    //     'subSpCode' => '1002',
    //     'systemId' => 'LWMA003',
    //     'apiUrl' => 'http://154.118.230.202:80/api/',

    //     'systemCode' => 'TWMATR001',
    //     'spGroupCode' => 'SPG1103',
    //     'wmaSpCode' => 'SP19960',
    //     'wmaSubSpCode' => '1001',
    //     'trSpCode' => 'SP19966',
    //     'trSubSpCode' => '1001',
    //     'gepgApiUrl' => 'http://154.118.230.202/api'
    // ];
}
