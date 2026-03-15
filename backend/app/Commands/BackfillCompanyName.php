<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class BackfillCompanyName extends BaseCommand
{
    protected $group       = 'App';
    protected $name        = 'app:backfill-company';
    protected $description = 'Backfills null company names and IDs in attachments';

    public function run(array $params)
    {
        $db = \Config\Database::connect();
        
        // 1. Fetch all documents missing company_id
        $docs = $db->table('license_application_attachments')
                   ->where('company_id IS NULL OR company_name IS NULL')
                   ->get()->getResult();

        if (empty($docs)) {
            CLI::write('No documents need backfilling.', 'green');
            return;
        }

        $usersObj = auth()->getProvider(); // Shield UserModel
        $updatedCount = 0;

        foreach ($docs as $doc) {
            $user = $usersObj->findById($doc->user_id);
            if ($user && isset($user->uuid)) {
                $businessInfo = $db->table('practitioner_business_infos')
                                   ->where('user_uuid', $user->uuid)
                                   ->get()->getRow();

                if ($businessInfo) {
                    $db->table('license_application_attachments')
                       ->where('id', $doc->id)
                       ->update([
                           'company_id'   => $businessInfo->id,
                           'company_name' => $businessInfo->company_name
                       ]);
                    $updatedCount++;
                }
            }
        }
        
        CLI::write("Successfully backfilled {$updatedCount} company names and IDs.", 'green');
    }
}
