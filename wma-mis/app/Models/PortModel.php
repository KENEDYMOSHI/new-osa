<?php

namespace App\Models;

use CodeIgniter\Model;
use Config\Database;

class PortModel extends Model
{
    protected $db;
    private $shipParticularsTable;
    private $timeLogTable;
    private $shipUllageB4DischargeTable;
    private $shipUllageAfterDischargeTable;
    private $transactionTable;
    private $portsTable;
    private $certificateOfQuantityTable;
    private $noteOfFactBeforeTable;
    private $noteOfFactAfterTable;
    private $dischargingSequenceTable;
    private $lineDisplacementTable;
    private $provisionalReportTable;
    private $pressureLogTable;
    private $dischargeOrderTable;
    private $tankMeasurementParticularsTable;
    private $shoreTanksTable;
    private $tankMeasurementsTable;
    private $sealPositionTable;
    private $sealNumberTable;
    private $shoreTankStatusTable;
    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->shipParticularsTable = $this->db->table('ship_particulars');
        $this->shipUllageB4DischargeTable = $this->db->table('ship_ullage_before_discharging');
        $this->shipUllageAfterDischargeTable = $this->db->table('ship_ullage_after_discharging');
        $this->portDocumentsTable = $this->db->table('port_documents');
        $this->certificateOfQuantityTable = $this->db->table('certificate_of_quantity');
        $this->noteOfFactBeforeTable = $this->db->table('note_of_fact_before_discharge');
        $this->noteOfFactAfterTable = $this->db->table('note_of_fact_after_discharge');
        $this->dischargingSequenceTable = $this->db->table('discharging_sequence');
        $this->lineDisplacementTable = $this->db->table('line_displacement');
        $this->provisionalReportTable = $this->db->table('provisional_report');
        $this->pressureLogTable = $this->db->table('pressure_log');
        $this->dischargeOrderTable = $this->db->table('discharge_order');

        $this->timeLogTable = $this->db->table('time_log');
        $this->portsTable = $this->db->table('ports');
        $this->transactionTable = $this->db->table('transactions');

        //=================on shore====================
        $this->tankMeasurementParticularsTable = $this->db->table('tank_measurement_particulars');
        $this->tankMeasurementsTable = $this->db->table('shore_tank_measurements');
        $this->shoreTankStatusTable = $this->db->table('shore_tank_status');
        $this->shoreTanksTable = $this->db->table('shore_tanks');
        $this->sealPositionTable = $this->db->table('shore_tank_seal_positions');
        $this->sealNumberTable = $this->db->table('shore_tank_seal_number');

    }

    //=================ports Details====================
    public function portDetails()
    {
        return $this->portsTable
            ->select()
            ->get()
            ->getResult();
    }

    // public function getShipPortAndUser($sipId){
    //     $this->shipParticularsTable
    //     ->select('

    //     ')
    //     ->where(['ship_id' =>$shipId])
    //     ->join()
    // }

    //=================save time log ====================
    public function saveTimeLog($data)
    {

       return $this->timeLogTable->insert($data);
       
    }
    //=================save ship docs ====================
    public function saveShipDocuments($data)
    {

       return $this->portDocumentsTable->insert($data);
        
    }
    //=================save Ship particulars ====================
    public function saveShipParticulars($data)
    {

       return $this->shipParticularsTable->insert($data);
        
    }

//=================Get last time log====================

    public function getLastTimeLog($sipId, $uniqueId)
    {
        return $this->timeLogTable
            ->select()
            ->where(['ship_id' => $sipId])
        // ->where(['unique_id' => $uniqueId])
            ->orderBy('id', 'DESC')
            ->get()
            ->getRow();
    }

//=================Get All time Logs====================

    public function getAllTimeLogs($sipId, $uniqueId)
    {
        return $this->timeLogTable
            ->select()
            ->where(['ship_id' => $sipId])
        // ->where(['unique_id' => $uniqueId])
            ->orderBy('id', 'ASC')
            ->get()
            ->getResult();
    }
//=================Select a ship/time log====================

    public function getShipList($id)
    {
        return $this->shipParticularsTable
            ->select()
            ->where(['unique_id' => $id])
            ->get()
            ->getResult();
    }

//=================Find  a match for a ship====================
    public function findMatch()
    {

        return $this->shipParticularsTable
            ->select()
            ->get()
            ->getResultArray();
    }

    public function getSelectedShip($id)
    {
        return $this->shipParticularsTable
            ->select()
            ->select('ship_particulars.terminal,region,phone_number,email,postal_address,fax')
            ->where(['ship_id' => $id])
            ->join('ports', 'ports.terminal = ship_particulars.terminal')
            ->get()
            ->getRow();
    }
//=================Select a ship/time log====================

    public function getShipDocuments($id)
    {
        return $this->portDocumentsTable
            ->select()
            ->where(['ship_id' => $id])
            ->get()
            ->getRow();
    }
//=================Download onboard documents====================
    public function downloadDocument($id, $uniqueId)
    {
        return $this->portDocumentsTable
            ->select()
            ->select('users.first_name,users.last_name')
            ->where(['port_documents.ship_id' => $id])
            ->join('users', 'users.unique_id = port_documents.unique_id')
            ->join('ship_particulars', 'ship_particulars.ship_id = port_documents.ship_id')
            ->join('ports', 'ports.terminal = ship_particulars.terminal')
            ->get()
            ->getResult();
    }
//=================download time log document====================
    public function downloadTimeLog($id, $uniqueId)
    {
        return $this->timeLogTable
            ->select()
            ->select('users.first_name,users.last_name')
            ->where(['time_log.ship_id' => $id])
            ->join('users', 'users.unique_id = time_log.unique_id')
            ->join('ship_particulars', 'ship_particulars.ship_id = time_log.ship_id')
            ->join('ports', 'ports.terminal = ship_particulars.terminal')
            ->get()
            ->getResult();
    }

//=================SHIP ULLAGE BEFORE DISCHARGE====================
    //=================save ullage b4 discharge ====================
    public function saveShipUllageB4Discharge($data)
    {

       return $this->shipUllageB4DischargeTable->insert($data);
        
    }

//=================Get all Available Ship ullage B4 discharge====================

    public function getAllShipUllageB4Discharge($shipId, $uniqueId)
    {
        return $this->shipUllageB4DischargeTable
            ->select()
            ->select('draft,aftr,trim,list,density_15C,density_20C')
            ->where(['ship_ullage_before_discharging.ship_id' => $shipId])
        // ->where(['ship_ullage_before_discharging.unique_id' => $uniqueId])
            ->orderBy('id', 'ASC')
            ->join('ship_particulars', 'ship_particulars.ship_id = ship_ullage_before_discharging.ship_id')
            ->get()
            ->getResult();
    }
    public function getAllUllageB4DischargeValue($shipId, $uniqueId)
    {
        return $this->shipUllageB4DischargeTable
            ->select('GSV15Centigrade AS gsv15,GSV20Centigrade AS gsv20,density_15C AS DN15,density_20C AS DN20')
            ->where(['ship_ullage_before_discharging.ship_id' => $shipId])
        // ->where(['ship_ullage_before_discharging.unique_id' => $uniqueId])
            ->join('ship_particulars', 'ship_particulars.ship_id = ship_ullage_before_discharging.ship_id')
            ->get()
            ->getResult();
    }
//=================download ullage b4 discharge====================
    public function downloadUllageB4Discharge($id, $uniqueId)
    {
        return $this->shipUllageB4DischargeTable
            ->select()
            ->select('users.first_name,users.last_name,draft,aftr,trim,list,density_15C,density_20C')
            ->where(['ship_ullage_before_discharging.ship_id' => $id])
        // ->where(['ship_ullage_before_discharging.unique_id' => $uniqueId])
            ->join('users', 'users.unique_id = ship_ullage_before_discharging.unique_id')
            ->join('ship_particulars', 'ship_particulars.ship_id = ship_ullage_before_discharging.ship_id')
            ->get()
            ->getResult();
    }
// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

//=================CERTIFICATE OF QUANTITY====================
    //=================save ullage b4 discharge ==================== getCertificateOfQuantity
    public function saveCertificateOfQuantity($data)
    {

       return $this->certificateOfQuantityTable->insert($data);
       
    }

    public function getCertificateOfQuantity($shipId, $uniqueId)
    {

        return $this->shipUllageB4DischargeTable
            ->select()
            ->select('users.first_name,users.last_name,draft,aftr,trim,list,density_15C,density_20C,usbbls_60F,us_gallons_60F')
            ->where(['ship_ullage_before_discharging.ship_id' => $shipId])
        // ->where(['ship_ullage_before_discharging.unique_id' => $uniqueId])
            ->join('users', 'users.unique_id = ship_ullage_before_discharging.unique_id')
            ->join('ship_particulars', 'ship_particulars.ship_id = ship_ullage_before_discharging.ship_id')
            ->join('certificate_of_quantity', 'certificate_of_quantity.ship_id = ship_ullage_before_discharging.ship_id')
            ->get()
            ->getResult();
    }

//=================SHIP ULLAGE AFTER DISCHARGE====================
    //=================save ullage b4 discharge ====================
    public function saveShipUllageAfterDischarge($data)
    {

       return $this->shipUllageAfterDischargeTable->insert($data);
       
    }

//=================Get all Available Ship ullage After discharge====================

    public function getAllShipUllageAfterDischarge($shipId, $uniqueId)
    {
        return $this->shipUllageAfterDischargeTable
            ->select()
            ->select('draft,aftr,trim,list,density_15C,density_20C')
            ->where(['ship_ullage_after_discharging.ship_id' => $shipId])
        // ->where(['ship_ullage_after_discharging.unique_id' => $uniqueId])
            ->orderBy('id', 'ASC')
            ->join('ship_particulars', 'ship_particulars.ship_id = ship_ullage_after_discharging.ship_id')
            ->get()
            ->getResult();
    }
//=================download ullage After discharge====================
    public function downloadUllageAfterDischarge($id, $uniqueId)
    {
        return $this->shipUllageAfterDischargeTable
            ->select()
            ->select('users.first_name,users.last_name')
            ->where(['ship_id' => $id])
        // ->where(['ship_ullage_after_discharging.unique_id' => $uniqueId])
            ->join('users', 'users.unique_id = ship_ullage_after_discharging.unique_id')
            ->get()
            ->getResult();
    }

    //=================NOTE OF FACT BEFORE DISCHARGING====================
    public function saveNoteOfFactBefore($data)
    {

       return $this->noteOfFactBeforeTable->insert($data);
       
    }

    public function getNoteOfFactBefore($shipId)
    {
        return $this->noteOfFactBeforeTable
            ->select()
            ->select('users.first_name,users.last_name')
            ->where(['note_of_fact_before_discharge.ship_id' => $shipId])
            ->join('ship_particulars', 'ship_particulars.ship_id = note_of_fact_before_discharge.ship_id')
            ->join('users', 'users.unique_id = note_of_fact_before_discharge.unique_id')
            ->join('ports', 'ports.terminal = ship_particulars.terminal')
            ->get()
            ->getRow();

    }

    //=================NOTE OF FACT AFTER DISCHARGING====================
    public function saveNoteOfFactAfter($data)
    {

       return $this->noteOfFactAfterTable->insert($data);
       
    }

    public function getNoteOfFactAfter($shipId)
    {
        return $this->noteOfFactAfterTable
        // ->select()
            ->select('
            note_of_fact_after_discharge.ship_id,
            at_loading,
            at_discharging,
            at_transfer,
            at_shore,
            at_vessel,
            bill_of_lading_qty,
            note_of_fact_after_discharge.created_at,
            ship_discharging_qty,
            shore_outturn_qty,
            qty_diff,
            diff_percentage,
            qty_diff_2,
            diff_percentage_2,
            discharging_qty_15c,
            discharging_qty_20c,
            master,
            receiver,
            terminal_rep,

            users.first_name,
            users.last_name,
            ship_name,
            captain,
            cargo,
            port,
            ports.email,
            ports.fax,
            ports.phone_number,
            ports.postal_address,
            ship_particulars.terminal,



            ')
            ->where(['note_of_fact_after_discharge.ship_id' => $shipId])
            ->join('ship_particulars', 'ship_particulars.ship_id = note_of_fact_after_discharge.ship_id')
            ->join('users', 'users.unique_id = note_of_fact_after_discharge.unique_id')
            ->join('ports', 'ports.terminal = ship_particulars.terminal')
            ->get()
            ->getRow();

    }

    //=================Pressure Log====================

    public function savePressureLog($data)
    {
       return $this->pressureLogTable->insert($data);
       

    }

    //=================get the last log====================
    public function getLastPressureLog($shipId)
    {
        return $this->pressureLogTable
            ->select()
            ->where(['ship_id' => $shipId])
            ->orderBy('id', 'DESC')
            ->limit(1)
            ->get()
            ->getRow();
    }
    public function getAllPressureLogs($shipId)
    {
        return $this->pressureLogTable
            ->select('
            pressure_log.ship_id,
            date,
            time,
            pressure,
            rate,
            users.first_name,
            users.last_name,
            ship_name,
            captain,
            cargo,
            port,
            ports.email,
            ports.fax,
            ports.phone_number,
            ports.postal_address,
            ship_particulars.terminal,
            ')
            ->where(['pressure_log.ship_id' => $shipId])
            ->join('ship_particulars', 'ship_particulars.ship_id = pressure_log.ship_id')
            ->join('users', 'users.unique_id = pressure_log.unique_id')
            ->join('ports', 'ports.terminal = ship_particulars.terminal')
            ->get()
            ->getResult();
    }

    //=================DISCHARGING SEQUENCE====================

    public function saveTankDischargingSequence($data)
    {

       return $this->dischargingSequenceTable->insert($data);
       
    }

    //=================LINE DISPLACEMENT====================

    public function saveLineDisplacement($data)
    {

       return $this->lineDisplacementTable->insert($data);
        
    }

    public function getLineDisplacement($shipId)
    {
        return $this->lineDisplacementTable
            ->select()
            ->select('
            ship_particulars.ship_name,
            ship_particulars.arrival_date,
            ship_particulars.cargo,
            ship_particulars.captain,

            ports.port_name,
            ports.phone_number,
            ports.fax,
            ports.postal_address,
            ports.postal_address,
            ports.email,
            ports.port_name,
            ports.terminal,

            users.first_name,
            users.last_name,
            ')
            ->where(['line_displacement.ship_id' => $shipId])
            ->join('ship_particulars', 'ship_particulars.ship_id = line_displacement.ship_id')
            ->join('users', 'users.unique_id = line_displacement.unique_id')
            ->join('ports', 'ports.terminal = ship_particulars.terminal')
            ->get()
            ->getResult();
    }

    //=================PROVISIONAL REPORT====================

    public function saveProvisionalReport($data)
    {

       return $this->provisionalReportTable->insert($data);
        
    }

    public function getProvisionalReport($shipId)
    {
        return $this->provisionalReportTable
            ->select()
            ->select('
            ship_particulars.ship_name,
            ship_particulars.arrival_date,
            ship_particulars.cargo,
            ship_particulars.captain,

            ports.port_name,
            ports.phone_number,
            ports.fax,
            ports.postal_address,
            ports.postal_address,
            ports.email,
            ports.port_name,
            ports.terminal,

            users.first_name,
            users.last_name,
            ')
            ->where(['provisional_report.ship_id' => $shipId])
            ->join('ship_particulars', 'ship_particulars.ship_id = provisional_report.ship_id')
            ->join('users', 'users.unique_id = provisional_report.unique_id')
            ->join('ports', 'ports.terminal = ship_particulars.terminal')
            ->get()
            ->getResult();
    }

    //=================Discharge Order====================
    public function saveDischargeOrder($data)
    {

       return $this->dischargeOrderTable->insert($data);
        
    }

    public function getAllDischargeOrders($shipId)
    {
        return $this->dischargeOrderTable

            ->select('

            discharge_order.ship_id,
            receiving_terminal,
            receiver,
            discharge_order.quantity,
            destination,



            ship_particulars.ship_name,
            ship_particulars.arrival_date,
            ship_particulars.cargo,
            ship_particulars.captain,

            ports.port_name,
            ports.phone_number,
            ports.fax,
            ports.postal_address,
            ports.postal_address,
            ports.email,
            ports.port_name,
            ports.terminal,

            users.first_name,
            users.last_name,
            ')
            ->where(['discharge_order.ship_id' => $shipId])
            ->join('ship_particulars', 'ship_particulars.ship_id = discharge_order.ship_id')
            ->join('users', 'users.unique_id = discharge_order.unique_id')
            ->join('ports', 'ports.terminal = ship_particulars.terminal')
            ->get()
            ->getResult();
    }

    public function getArrivalQuantity_billOfLading($shipId)
    {
        return $this->shipUllageB4DischargeTable
            ->select('
                GSV20Centigrade,
                density_20C,
                billOfLading1
      ')
            ->where(['note_of_fact_before_discharge.ship_id' => $shipId])
            ->join('ship_particulars', 'ship_particulars.ship_id = ship_ullage_before_discharging.ship_id')
            ->join('note_of_fact_before_discharge', 'note_of_fact_before_discharge.ship_id = ship_ullage_before_discharging.ship_id')
            ->get()
            ->getResult();
    }

    //=================get Tank Info====================
    public function getTankInfo($shipId)
    {
        return $this->dischargingSequenceTable
            ->select('
    discharging_sequence.id,
    discharging_sequence.tank_number,
    discharging_sequence.line_displacement,
    discharging_sequence.tank_number,
    discharging_sequence.time_from,
    discharging_sequence.date_from,
    discharging_sequence.time_to,
    discharging_sequence.date_to,

    note_of_fact_before_discharge.arrivalQuantity1,
    note_of_fact_before_discharge.billOfLading1,

    ship_particulars.ship_name,
    ship_particulars.arrival_date,
    ship_particulars.cargo,
    ship_particulars.captain,

    ports.port_name,
    ports.phone_number,
    ports.fax,
    ports.postal_address,
    ports.postal_address,
    ports.email,
    ports.port_name,
    ports.terminal,

    users.first_name,
    users.last_name,


    ')
            ->where(['discharging_sequence.ship_id' => $shipId])
            ->join('ship_particulars', 'ship_particulars.ship_id = discharging_sequence.ship_id')
            ->join('users', 'users.unique_id = discharging_sequence.unique_id')
            ->join('note_of_fact_before_discharge', 'note_of_fact_before_discharge.ship_id = discharging_sequence.ship_id')
            ->join('ports', 'ports.terminal = ship_particulars.terminal')
            ->get()
            ->getResult();
    }

    public function updateTank($id, $data)
    {
        return $this->dischargingSequenceTable
            ->set($data)
            ->where(['id' => $id])
            ->update();
    }
    //===============================================================
    #                                                               #
    #======================= ON SHORE ================================
    #
    #=================================================================

    public function getMeasurementParticulars()
    {
        return $this->tankMeasurementParticularsTable->select()->get()->getResult();
    }

    public function getSealPositions()
    {
        return $this->sealPositionTable->select()->get()->getResult();
    }

    public function addShoreTank($data)
    {

       return $this->shoreTanksTable->insert($data);
        
    }

    //=================get all tanks====================

    public function getSingleTank($tankId)
    {
        return $this->shoreTanksTable
            ->select(
                '
                ship_name,
                captain,
                density_20C,

                after_loading,
                before_loading,
                date,
                time,
                tank_number,
                tank_id,
                shore_tanks.product,
                shore_tanks.terminal,


                users.first_name,
                users.last_name,

                ports.port_name,
                ports.phone_number,
                ports.fax,
                ports.postal_address,
                ports.postal_address,
                ports.email,
                ports.port_name,
                ports.terminal,

                users.first_name,
                users.last_name,



                '
            )
            ->where(['tank_id' => $tankId])
            ->join('ship_particulars', 'ship_particulars.ship_id = shore_tanks.ship_id')
            ->join('users', 'users.unique_id = shore_tanks.unique_id')
            ->join('ports', 'ports.terminal = ship_particulars.terminal')
            ->get()
            ->getRow();
    }

    public function getShoreTanks($shipId)
    {
        return $this->shoreTanksTable
            ->select('tank_number,tank_id')
            ->where(['ship_id' => $shipId])
            ->orderBy('tank_id', 'DESC')
            ->get()
            ->getResult();
    }

    public function checkExistingMeasurement($tankId, $particularId)
    {
        return $this->tankMeasurementsTable->select()
            ->where(['tank_id' => $tankId])
            ->where(['particular_id' => $particularId])
            ->get()
            ->getRow();
    }

    public function addMeasurementData($data)
    {

       return $this->tankMeasurementsTable->ignore(true)->insert($data);
       
    }

    public function getMeasurementData($tankId)
    {
        return $this->tankMeasurementsTable
            ->select()
            ->where(['tank_id' => $tankId])
            ->orderBy('shore_tank_measurements.particular_id', 'ASC')
            ->join('tank_measurement_particulars', 'tank_measurement_particulars.particular_id = shore_tank_measurements.particular_id ')
            ->get()
            ->getResult();

    }

    public function addSealPosition($data)
    {

       return $this->sealNumberTable->insert($data);
        
    }

    public function getSeals($tankId)
    {
        return $this->sealNumberTable
            ->select()
            ->where(['tank_id' => $tankId])
            ->orderBy('shore_tank_seal_number.seal_position_id', 'ASC')
            ->join('shore_tank_seal_positions', 'shore_tank_seal_positions.id = shore_tank_seal_number.seal_position_id')
            ->get()
            ->getResult();

    }

    public function addStatus($data)
    {
       return $this->shoreTankStatusTable->insert($data);
       

    }

    public function getStatus($tankId)
    {

        return $this->shoreTankStatusTable
            ->select()
            ->where(['tank_id' => $tankId])
            ->orderBy('id', 'DESC')
            ->limit(1)
            ->get()
            ->getRow();

    }

}