<?php

namespace App\Controllers;

use Ifsnop\Mysqldump as IMysqldump;
use App\BackupConfig\Bootstrap;
use App\Libraries\SmsLibrary;
use App\Models\MiscellaneousModel;
use BackupManager\Filesystems\Destination;


class BackupController extends BaseController
{

    public $user;
    public $session;
    public $uniqueId;
    public $profileModel;

    public $token;
    // public $dumpSettings;
    // public $pdoSettings;
    public $_dumpSettings;
    public $db;

    //9-154.118.224.218-0

    public function index()
    {


        $miscModel = new MiscellaneousModel();
        $data['page'] = ['title' => 'Database Backup', 'heading' => 'Database Backup',];

        $data['user'] = auth()->user();
        $data['date'] = $miscModel->readBackupDate() ?  $miscModel->readBackupDate()->date : '';

        return view('Pages/admin/backup', $data);
    }



    public function getVariable($var)
    {
        return $this->request->getVar($var, FILTER_SANITIZE_SPECIAL_CHARS);
    }

    public function createBackup()
    {

        $sms = new SmsLibrary();
        $title = 'Backup-' . date('d-m-Y H;i;s');
        try {
            $dump = new IMysqldump\Mysqldump('mysql:host=localhost;dbname=vipimo', 'root', '');
            $dump->start("public/database/$title.sql");
            $sms->sendSms('255659851709', 'Backup Completed file: ' . $title);
            echo 'Backup Complete';
        } catch (\Exception $e) {
            echo 'mysqldump-php error: ' . $e->getMessage();
        }
    }
}
