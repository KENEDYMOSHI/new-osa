<?php
namespace Config;
use CodeIgniter\Config\BaseConfig;

class CILogViewer extends BaseConfig {
    public $logFilePattern = 'log-*.log';
    public $viewName = 'Pages/admin/LogsView'; //where logs exists in app/Views/logs.php
    // public $viewName = 'logs'; //where logs exists in app/Views/logs.php
}