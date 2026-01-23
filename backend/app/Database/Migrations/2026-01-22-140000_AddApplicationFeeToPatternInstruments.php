<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddApplicationFeeToPatternInstruments extends Migration
{
    public function up()
    {
        $fields = [
            'application_fee' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => 0.00,
                // 'after'      => 'pattern_application_id' 
            ],
        ];

        // 1. Standard Instruments (pattern_application_instruments)
        if ($this->db->tableExists('pattern_application_instruments')) {
            if (!$this->db->fieldExists('application_fee', 'pattern_application_instruments')) {
                 $this->forge->addColumn('pattern_application_instruments', $fields);
            }
        }

        // 2. Weighing Instruments
        if ($this->db->tableExists('weighing_instruments')) {
             if (!$this->db->fieldExists('application_fee', 'weighing_instruments')) {
                 $this->forge->addColumn('weighing_instruments', $fields);
             }
        }

        // 3. Capacity Measure Instruments
        if ($this->db->tableExists('capacity_measure_instruments')) {
            if (!$this->db->fieldExists('application_fee', 'capacity_measure_instruments')) {
                $this->forge->addColumn('capacity_measure_instruments', $fields);
            }
        }

        // 4. Meter Instruments (if table exists)
        if ($this->db->tableExists('meter_instruments')) {
            if (!$this->db->fieldExists('application_fee', 'meter_instruments')) {
                $this->forge->addColumn('meter_instruments', $fields);
            }
        }
    }

    public function down()
    {
        if ($this->db->tableExists('pattern_application_instruments')) {
             if ($this->db->fieldExists('application_fee', 'pattern_application_instruments')) {
                 $this->forge->dropColumn('pattern_application_instruments', 'application_fee');
             }
        }

        if ($this->db->tableExists('weighing_instruments')) {
            if ($this->db->fieldExists('application_fee', 'weighing_instruments')) {
                $this->forge->dropColumn('weighing_instruments', 'application_fee');
            }
        }

        if ($this->db->tableExists('capacity_measure_instruments')) {
            if ($this->db->fieldExists('application_fee', 'capacity_measure_instruments')) {
                $this->forge->dropColumn('capacity_measure_instruments', 'application_fee');
            }
        }
        
        if ($this->db->tableExists('meter_instruments')) {
            if ($this->db->fieldExists('application_fee', 'meter_instruments')) {
                $this->forge->dropColumn('meter_instruments', 'application_fee');
            }
        }
    }
}
