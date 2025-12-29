<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class FixOrphanDocuments extends BaseCommand
{
    protected $group       = 'Database';
    protected $name        = 'db:fix-orphans';
    protected $description = 'Fix documents with null application_id by linking to user applications';

    public function run(array $params)
    {
        $db = \Config\Database::connect();
        
        CLI::write('=== FIXING ORPHAN DOCUMENTS ===', 'green');
        CLI::newLine();
        
        // Get orphan documents grouped by user
        $query = $db->query("
            SELECT 
                att.id as doc_id,
                att.user_id,
                att.document_type,
                att.category,
                CONCAT(p.first_name, ' ', p.last_name) as applicant_name
            FROM license_application_attachments att
            LEFT JOIN users u ON u.id = att.user_id
            LEFT JOIN practitioner_personal_infos p ON p.user_uuid = u.uuid
            WHERE att.application_id IS NULL OR att.application_id = ''
            ORDER BY att.user_id
        ");
        
        $orphans = $query->getResult();
        
        if (empty($orphans)) {
            CLI::write('No orphan documents found!', 'green');
            return;
        }
        
        CLI::write('Found ' . count($orphans) . ' orphan documents', 'yellow');
        CLI::newLine();
        
        $fixed = 0;
        $failed = 0;
        
        // Group by user and fix
        $byUser = [];
        foreach ($orphans as $doc) {
            $userId = $doc->user_id;
            if (!isset($byUser[$userId])) {
                $byUser[$userId] = [];
            }
            $byUser[$userId][] = $doc;
        }
        
        foreach ($byUser as $userId => $docs) {
            if (!$userId) {
                CLI::write("Skipping documents with no user_id", 'red');
                $failed += count($docs);
                continue;
            }
            
            // Find the most recent application for this user
            $appQuery = $db->query("
                SELECT id, status, created_at 
                FROM license_applications 
                WHERE user_id = ? 
                ORDER BY created_at DESC
                LIMIT 1
            ", [$userId]);
            
            $app = $appQuery->getRow();
            
            if (!$app) {
                CLI::write("No application found for user $userId", 'red');
                $failed += count($docs);
                continue;
            }
            
            CLI::write("User $userId ({$docs[0]->applicant_name}): Linking " . count($docs) . " documents to application {$app->id}", 'cyan');
            
            // Update all documents for this user
            foreach ($docs as $doc) {
                $updated = $db->table('license_application_attachments')
                    ->where('id', $doc->doc_id)
                    ->update(['application_id' => $app->id]);
                
                if ($updated) {
                    CLI::write("  ✓ Fixed: {$doc->document_type} ({$doc->category})", 'green');
                    $fixed++;
                } else {
                    CLI::write("  ✗ Failed: {$doc->document_type}", 'red');
                    $failed++;
                }
            }
            
            CLI::newLine();
        }
        
        // Summary
        CLI::write('=== SUMMARY ===', 'green');
        CLI::write("Fixed: $fixed documents", 'green');
        if ($failed > 0) {
            CLI::write("Failed: $failed documents", 'red');
        }
        CLI::newLine();
        CLI::write('Done! Run "php spark db:check-docs" to verify.', 'yellow');
    }
}
