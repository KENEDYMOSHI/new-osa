<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

/**
 * Backfill request_number for all license_applications that are missing one.
 * Run: php spark app:backfill-request-numbers
 */
class BackfillRequestNumbers extends BaseCommand
{
    protected $group       = 'App';
    protected $name        = 'app:backfill-request-numbers';
    protected $description = 'Generate request_number (OSA/REQ/YYYY/MM/NN) for all existing applications that are missing one.';

    public function run(array $params)
    {
        $db = \Config\Database::connect();

        // Fetch all applications without a request_number, ordered by created_at ASC
        $applications = $db->table('license_applications')
            ->where('request_number IS NULL OR request_number =', '')
            ->orderBy('created_at', 'ASC')
            ->get()
            ->getResultArray();

        if (empty($applications)) {
            CLI::write('All applications already have a request number. Nothing to do.', 'green');
            return;
        }

        CLI::write('Backfilling request_number for ' . count($applications) . ' application(s)...', 'yellow');

        // Keep a counter per year-month to generate sequential numbers
        $counters = [];

        // Also count already-existing applications per month so we don't collision
        foreach ($applications as $app) {
            $ym = date('Y-m', strtotime($app['created_at']));
            $year  = date('Y', strtotime($app['created_at']));
            $month = date('m', strtotime($app['created_at']));

            if (!isset($counters[$ym])) {
                // Count already-assigned ones for this month
                $existing = $db->table('license_applications')
                    ->where("YEAR(created_at)", $year)
                    ->where("MONTH(created_at)", $month)
                    ->where('request_number IS NOT NULL')
                    ->where('request_number !=', '')
                    ->countAllResults();

                $counters[$ym] = $existing;
            }

            $counters[$ym]++;
            $sequence      = str_pad($counters[$ym], 2, '0', STR_PAD_LEFT);
            $requestNumber = "OSA/REQ/{$year}/{$month}/{$sequence}";

            $db->table('license_applications')
               ->where('id', $app['id'])
               ->update(['request_number' => $requestNumber]);

            CLI::write("  ✓ ID: {$app['id']} → {$requestNumber}", 'cyan');
        }

        CLI::write('Done! ' . count($applications) . ' application(s) updated.', 'green');
    }
}
