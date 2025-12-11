<?php
namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class UpdateMis extends BaseCommand
{
    /**
     * The Command's Group
     *
     * @var string
     */
    protected $group = 'CodeIgniter';

    /**
     * The Command's Name
     *
     * @var string
     */
    protected $name = 'mis:update';

    /**
     * The Command's Description
     *
     * @var string
     */
    protected $description = 'Update Mis Configurations';

    /**
     * The Command's Usage
     *
     * @var string
     */
    protected $usage = 'mis:update [--env live|test]';

    /**
     * The Command's Options
     *
     * @var array
     */
    protected $options = [
        '--env' => 'Environment to update the configuration for: live or test',
    ];

    /**
     * Actually execute a command.
     *
     * @param array $params
     */
    public function run(array $params)
    {
        $env = CLI::getOption('env') ?? 'live';
        
        if (!in_array($env, ['live', 'test'])) {
            CLI::error('Invalid environment provided. Please specify "live" or "test".');
            return;
        }

        helper('setting');

        $gfs = $env == 'live' ? 'liveGfs' : 'Gfs';
        foreach (setting("MisConfig.$gfs") as $key => $value) {
            setting()->set('Gfs.' . $key, $value);
        }

        $billConfig = $env == 'live' ? 'billLive' : 'billTesting';
        foreach (setting("MisConfig.$billConfig") as $key => $value) {
            setting()->set('Bill.' . $key, $value);
        }

        if ($env == 'live') {
            setting()->set('System.env', 'production');
        } else {
            setting()->set('System.env', 'testing');
        }

        $text = "WMA MIS $env Config Updated ✔ ";
        CLI::write('Status: ' . CLI::color($text, 'green'));
    }
}
