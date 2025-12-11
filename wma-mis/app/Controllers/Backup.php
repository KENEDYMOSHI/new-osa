<?php

// namespace App\Controllers;

// use Ifsnop\Mysqldump as IMysqldump;
// use App\Models\ProfileModel;
// use App\Controllers\BaseController;
// use App\Models\MiscellaneousModel;
// use CodeIgniter\Database\Query;


// class BackupController extends BaseController
// {

//     public $user;
//     public $session;
//     public $uniqueId;
//     public $profileModel;
//     public $appRequest;
//     public $token;
//     // public $dumpSettings;
//     // public $pdoSettings;
//     public $_dumpSettings;
//     public $db;

//     public function __construct(
//         $dumpSettings = array(),
//         $pdoSettings = array()
//     ) {
//         $this->appRequest = service('request');
//         $this->profileModel = new ProfileModel();
//         $this->db = \Config\Database::connect();
//         $this->session = session();
//         $this->token = csrf_hash();
//         $this->uniqueId = $this->session->get('loggedUser');
//         $this->user = auth()->user();

//         // $this->dumpSettings = $dumpSettings;
//         // $this->pdoSettings = $pdoSettings;
//         $this->_dumpSettings;


//         //     $dumpSettings = array(),
//         // $pdoSettings = array()
//     }


//     public function index()
//     {


//         $miscModel = new MiscellaneousModel();
//         $data['page'] = ['title' => 'Database Backup', 'heading' => 'Database Backup',];
 
//         $data['user'] = auth()->user();
//         $data['date'] = $miscModel->readBackupDate() ?  $miscModel->readBackupDate()->date : '';

//         return view('Pages/admin/backup', $data);
//     }


//     public function tables()
//     {

//         $serverName = "localhost";
//         $username = "root";
//         $password = "";
//         $dbname = "vipimo";

//         $conn =  mysqli_connect($serverName, $username, $password, $dbname);
//         $sql = "show tables";

//         $result = mysqli_query($conn, $sql); // run the query and assign the result to $result
//         while ($table = mysqli_fetch_array($result)) { // go through each row that was returned in $result
//             echo ($table[0] . "<BR>");    // print the table that was returned on that row.

//         }
//     }

//     public function createBackup()
//     {
//         $miscModel = new MiscellaneousModel();
//         try {
//             $dump = new IMysqldump\Mysqldump('mysql:host=localhost;dbname=vipimo', 'root', '');

//             $date  = date('d M Y h;i a l') . ' ' . str_shuffle(time());
//             $dir = WRITEPATH . 'Backups';

//             $title = $dir . "\ $date.sql";

//             if (!is_dir($dir)) {
//                 mkdir($dir, 0777, true);
//             }

//             $period = 'DAY';

//             $day = '1';
//             $month = '2';

//             $dump->setTableWheres(array(
//                 'transactions' => "created_on > NOW() - INTERVAL $day $period",
//                 'measurement_sheet' => "created_at > NOW() - INTERVAL $day $period",
//                 'product_details' => "created_at > NOW() - INTERVAL $day $period",
//                 // 'logs' => 'date_logged > NOW() - INTERVAL 1 DAY',
//                 // 'posts' => 'isLive=1'
//             ));



//             $dump->start($title);

//             $miscModel->writeBackupDate([
//                 'title' => $title,
//                 'date' => str_replace(';', ':', $date),
//                 'unique_id' => $this->uniqueId,
//             ]);



//             return $this->response->setJSON([
//                 'status' => 1,
//                 'data' => $miscModel->readBackupDate()->date,
//                 'msg' => 'Backup Created Successfully',
//                 'token' => $this->token

//             ]);
//         } catch (\Exception $e) {
//             return $this->response->setJSON([
//                 'status' => 0,
//                 'msg' => 'mysqldump-php error: ' . $e->getMessage(),
//                 'token' => $this->token

//             ]);
//         }
//     }
// }
