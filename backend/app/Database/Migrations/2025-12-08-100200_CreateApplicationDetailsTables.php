<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateApplicationDetailsTables extends Migration
{
    public function up()
    {
        $this->forge->dropTable('application_qualifications', true);
        $this->forge->dropTable('application_tools', true);

        // Qualifications Table
        $this->forge->addField([
            'id' => [
                'type' => 'CHAR',
                'constraint' => 36,
            ],
            'license_application_id' => [
                'type' => 'CHAR',
                'constraint' => 36,
            ],
            'institution' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'award' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'year' => [
                'type' => 'VARCHAR',
                'constraint' => 4,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('license_application_id', 'license_applications', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('application_qualifications');

        // Experiences Table (Linked to initial application as per generic user data, often collected early, BUT specifically mention in Module 2 in prompt.
        // Prompt says: "Module 2: License Details, Applicant Qualifications, Tools & Equipments". 
        // NOTE: Prompt removed Experience from Initial Application earlier. So I will attach it to License Application Module 2.)
        
        // Actually, prompt listed "Applicant Qualifications" and "Tools & Equipments" for Module 2.
        // It did NOT explicitly list "Experience" for Module 2 in the text "User fills: License Details, Applicant Qualifications, Tools & Equipments".
        // However, typically Qualifications and Experience go together. I will add experience to Module 2 to be safe, or omit if not requested.
        // The previous cleanup removed Experience from Initial.
        // I will add 'application_tools' now.

        // Tools Table
        $this->forge->addField([
            'id' => [
                'type' => 'CHAR',
                'constraint' => 36,
            ],
            'license_application_id' => [
                'type' => 'CHAR',
                'constraint' => 36,
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'serial_number' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'capacity' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('license_application_id', 'license_applications', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('application_tools');
    }

    public function down()
    {
        $this->forge->dropTable('application_tools');
        $this->forge->dropTable('application_qualifications');
    }
}
