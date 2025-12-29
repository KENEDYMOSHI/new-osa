<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class FindOrphanDocuments extends BaseCommand
{
    protected $group       = 'Database';
    protected $name        = 'db:find-orphans';
    protected $description = 'Find documents with null application_id';

    public function run(array $params)
    {
        $db = \Config\Database::connect();
        
        CLI::write('=== FINDING ORPHAN DOCUMENTS ===', 'red');
        CLI::newLine();
        
        // Get documents with null application_id
        $query = $db->query("
            SELECT 
                att.id,
                att.user_id,
                att.application_id,
                att.document_type,
                att.category,
                att.status,
                att.original_name,
                att.created_at,
                CONCAT(p.first_name, ' ', p.last_name) as applicant_name,
                u.id as user_table_id
            FROM license_application_attachments att
            LEFT JOIN users u ON u.id = att.user_id
            LEFT JOIN practitioner_personal_infos p ON p.user_uuid = u.uuid
            WHERE att.application_id IS NULL OR att.application_id = ''
            ORDER BY att.user_id, att.created_at
        ");
        
        $orphans = $query->getResult();
        
        CLI::write('Total Orphan Documents: ' . count($orphans), 'yellow');
        CLI::newLine();
        
        if (empty($orphans)) {
            CLI::write('No orphan documents found!', 'green');
            return;
        }
        
        // Group by user
        $byUser = [];
        foreach ($orphans as $doc) {
            $userId = $doc->user_id ?? 'null';
            if (!isset($byUser[$userId])) {
                $byUser[$userId] = [
                    'name' => $doc->applicant_name ?? 'Unknown',
                    'docs' => []
                ];
            }
            $byUser[$userId]['docs'][] = $doc;
        }
        
        // Display
        foreach ($byUser as $userId => $data) {
            CLI::write("User ID: $userId | Name: {$data['name']}", 'cyan');
            CLI::write("  Documents: " . count($data['docs']), 'yellow');
            
            foreach ($data['docs'] as $doc) {
                CLI::write("    - {$doc->document_type} ({$doc->category}) | {$doc->original_name}", 'white');
            }
            
            // Try to find matching application
            if ($userId && $userId !== 'null') {
                $appQuery = $db->query("
                    SELECT id, status, created_at 
                    FROM license_applications 
                    WHERE user_id = ? 
                    ORDER BY created_at DESC
                    LIMIT 5
                ", [$userId]);
                
                $apps = $appQuery->getResult();
                
                if (!empty($apps)) {
                    CLI::write("  Possible Applications for this user:", 'green');
                    foreach ($apps as $app) {
                        CLI::write("    - App ID: {$app->id} | Status: {$app->status} | Created: {$app->created_at}", 'white');
                    }
                } else {
                    CLI::write("  No applications found for this user!", 'red');
                }
            }
            
            CLI::newLine();
        }
        
        // Suggest fix
        CLI::write('=== SUGGESTED FIX ===', 'green');
        CLI::write('Run the following command to fix orphan documents:', 'yellow');
        CLI::write('php spark db:fix-orphans', 'white');
    }
}
