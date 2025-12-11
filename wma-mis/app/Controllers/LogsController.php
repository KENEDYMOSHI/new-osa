<?php

namespace App\Controllers;

// use Monolog\Level;
// use Monolog\Logger;

use App\Models\ProfileModel;
use CodeIgniter\Controller;
use CILogViewer\CILogViewer;
use Monolog\Handler\StreamHandler;;


class LogsController extends Controller
{
    protected $logger;

    public function __construct()
    {

        // $this->logger = new Logger('my_logger');
        // $this->logger->pushHandler(new StreamHandler(WRITEPATH . 'logs/' . date('Y-m-d') . '.log', Logger::DEBUG));
        helper('setting');
        helper(setting('App.helpers'));
    }




    public function index()
    {

        $data['page'] = [
            'title' => 'System Logs',
            'heading' => 'System Logs',
        ];

        $logViewer = new CILogViewer();
        return $logViewer->showLogs();
        // printer($logViewer);
    }

    public function activityLogs()
    {
        $data['page'] = [
            'title' => 'Activity Logs',
            'heading' => 'Activity Logs',
        ];

        $data['logs'] = (new ProfileModel())->activityLogs();

        return view('Pages/admin/ActivityLogs', $data);
    }
}
