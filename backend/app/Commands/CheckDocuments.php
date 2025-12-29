<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class CheckDocuments extends BaseCommand
{
    protected $group       = 'Database';
    protected $name        = 'db:check-docs';
    protected $description = 'Check all documents in database with categories';

    public function run(array $params)
    {
        $db = \Config\Database::connect();
        
        CLI::write('=== CHECKING ALL DOCUMENTS IN DATABASE ===', 'green');
        CLI::newLine();
        
        // Get all documents with application info
        $query = $db->query("
            SELECT 
                att.id,
                att.application_id,
                att.document_type,
                att.category,
                att.status,
                att.original_name,
                att.created_at,
                la.status as app_status,
                CONCAT(p.first_name, ' ', p.last_name) as applicant_name
            FROM license_application_attachments att
            LEFT JOIN license_applications la ON la.id = att.application_id
            LEFT JOIN users u ON u.id = la.user_id
            LEFT JOIN practitioner_personal_infos p ON p.user_uuid = u.uuid
            ORDER BY att.application_id, att.category, att.created_at
        ");
        
        $results = $query->getResult();
        
        CLI::write('Total Documents: ' . count($results), 'yellow');
        CLI::newLine();
        
        // Group by application
        $byApp = [];
        $categoryCounts = ['attachment' => 0, 'qualification' => 0, 'null' => 0];
        
        foreach ($results as $doc) {
            $appId = $doc->application_id;
            if (!isset($byApp[$appId])) {
                $byApp[$appId] = [
                    'applicant' => $doc->applicant_name ?? 'Unknown',
                    'app_status' => $doc->app_status ?? 'Unknown',
                    'attachment' => [],
                    'qualification' => []
                ];
            }
            
            $cat = $doc->category ?? 'null';
            $categoryCounts[$cat] = ($categoryCounts[$cat] ?? 0) + 1;
            
            if ($cat === 'attachment' || $cat === 'qualification') {
                $byApp[$appId][$cat][] = $doc;
            }
        }
        
        // Display by application
        foreach ($byApp as $appId => $data) {
            CLI::write("Application: $appId", 'cyan');
            CLI::write("  Applicant: {$data['applicant']}", 'white');
            CLI::write("  Status: {$data['app_status']}", 'white');
            
            CLI::write("  Required Attachments: " . count($data['attachment']), 'yellow');
            foreach ($data['attachment'] as $doc) {
                CLI::write("    - {$doc->document_type} | Status: {$doc->status} | File: {$doc->original_name}", 'white');
            }
            
            CLI::write("  Qualification Documents: " . count($data['qualification']), 'yellow');
            foreach ($data['qualification'] as $doc) {
                CLI::write("    - {$doc->document_type} | Status: {$doc->status} | File: {$doc->original_name}", 'white');
            }
            
            CLI::newLine();
        }
        
        // Summary
        CLI::write('=== SUMMARY ===', 'green');
        CLI::write("Total Applications with Documents: " . count($byApp), 'yellow');
        CLI::write("Documents by Category:", 'yellow');
        foreach ($categoryCounts as $cat => $count) {
            CLI::write("  $cat: $count", 'white');
        }
    }
}
