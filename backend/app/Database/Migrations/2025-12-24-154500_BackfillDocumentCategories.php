<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class BackfillDocumentCategories extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();
        
        $qualifications = [
            'psle', 'Primary School Leaving Certificate (PSLE)',
            'csee', 'Certificate of Secondary Education Examination (CSEE)',
            'acsee', 'Advanced Certificate of Secondary Education Examination (ACSEE)',
            'veta', 'Basic Certificate - Vocational Education and Training Authority (VETA)',
            'nta4', 'Basic Certificate (NTA Level 4)',
            'nta5', 'Technician Certificate (NTA Level 5)',
            'nta6', 'Ordinary Diploma (NTA Level 6)',
            'specialized', 'Other Specialized Certificates',
            'bachelor', "Bachelor's Degree"
        ];

        // Safe update for qualifications
        $builder = $db->table('license_application_attachments');
        $builder->whereIn('document_type', $qualifications);
        $builder->update(['category' => 'qualification']);
        
        // Update everything else to 'attachment' if category is null
        $sql = "UPDATE license_application_attachments SET category = 'attachment' WHERE category IS NULL";
        $db->query($sql);
    }

    public function down()
    {
        // No easy rollback for data updates, generally we leave it or set to null
        $db = \Config\Database::connect();
        $db->table('license_application_attachments')->update(['category' => null]);
    }
}
