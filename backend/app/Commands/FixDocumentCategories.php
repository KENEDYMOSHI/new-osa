<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class FixDocumentCategories extends BaseCommand
{
    protected $group       = 'Database';
    protected $name        = 'db:fix-categories';
    protected $description = 'Fix documents that are in wrong category';

    public function run(array $params)
    {
        $db = \Config\Database::connect();
        
        CLI::write('=== CHECKING DOCUMENT CATEGORIES ===', 'green');
        CLI::newLine();
        
        // Define qualification types
        $qualificationTypes = [
            'psle', 'csee', 'acsee', 'veta', 'nta4', 'nta5', 'nta6',
            'specialized', 'bachelor', 'diploma', 'degree', 'master', 'phd',
            'cv', 'certificate', 'professional_certificate'
        ];
        
        // Get all documents
        $query = $db->query("
            SELECT id, document_type, category 
            FROM license_application_attachments
        ");
        
        $docs = $query->getResult();
        $fixed = 0;
        $correct = 0;
        
        foreach ($docs as $doc) {
            $docType = strtolower($doc->document_type);
            $shouldBeQual = in_array($docType, $qualificationTypes);
            $expectedCategory = $shouldBeQual ? 'qualification' : 'attachment';
            
            if ($doc->category !== $expectedCategory) {
                CLI::write("Fixing: {$doc->document_type} | Current: {$doc->category} → Expected: {$expectedCategory}", 'yellow');
                
                $db->table('license_application_attachments')
                   ->where('id', $doc->id)
                   ->update(['category' => $expectedCategory]);
                
                $fixed++;
            } else {
                $correct++;
            }
        }
        
        CLI::newLine();
        CLI::write('=== SUMMARY ===', 'green');
        CLI::write("Correct: $correct documents", 'green');
        CLI::write("Fixed: $fixed documents", $fixed > 0 ? 'yellow' : 'green');
        
        if ($fixed > 0) {
            CLI::newLine();
            CLI::write('Categories have been corrected!', 'green');
        } else {
            CLI::newLine();
            CLI::write('All documents are in correct categories!', 'green');
        }
    }
}
