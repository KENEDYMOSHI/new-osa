<?php

namespace App\Models;

use CodeIgniter\Model;

class PetroleumImportModel extends Model
{
    protected $db;
    protected $vesselTable;
    protected $importersTable;
    protected $petroleumData;
    protected $vesselSailing;
    protected $vesselOutturn;

    protected $coqTable;

    //add a constructor method
    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->vesselTable = $this->db->table('vessels');
        $this->importersTable = $this->db->table('importers');
        $this->petroleumData = $this->db->table('petroleum_data');
        $this->coqTable = $this->db->table('certificate_of_quantity');
        $this->vesselSailing = $this->db->table('vessel_sailing');
        $this->vesselOutturn = $this->db->table('vessel_outturn');
    }


    //add vessel method
    public function addVessel($data)
    {
        return $this->vesselTable->insert($data);
    }

    //select vessel method
    public function selectVessel($id)
    {
        return $this->vesselTable->select()->where(['id' => $id])->get()->getRow();
    }

    //update vessel method
    public function updateVessel($id, $data)
    {
        return $this->vesselTable->where('id', $id)->set($data)->update();
    }
    //delete vessel method
    public function deleteVessel($id)
    {
        return $this->vesselTable->select()->where(['id' => $id])->delete();
    }

    //get all vessels method
    public function getVessels()
    {
        return $this->vesselTable->select()->get()->getResult();
    }


    //add importer method
    public function addImporter($data)
    {
        return $this->importersTable->insert($data);
    }


    //select importer method
    public function selectImporter($id)
    {
        return $this->importersTable->select()->where(['id' => $id])->get()->getRow();
    }

    //update importer method
    public function updateImporter($id, $data)
    {
        return $this->importersTable->where('id', $id)->set($data)->update();
    }
    //delete importer method
    public function deleteImporter($id)
    {
        return $this->importersTable->select()->where(['id' => $id])->delete();
    }

    //get all importers method
    public function getImporters()
    {
        return $this->importersTable->select()->get()->getResult();
    }



    //add petroleum data method
    public function addPetroleumData($data)
    {
        return $this->petroleumData->insertBatch($data);
    }
    //get all petroleum data method
    public function getPetroleumData($params)
    {
        return $this->petroleumData->select()->where($params)->get()->getResult();
    }


    //get all petroleum data method
    public function getPetroleumDataGrouped($params)
    {
        return $this->petroleumData->select()->where($params)->groupBy('vesselId')->get()->getResult();
    }


    //get all petroleum data method
    public function getPetroleumDataAll()
    {
        return $this->petroleumData
        ->select('
         vessels.vesselName,
         vessels.productType,
         vessels.arrivalDate,
         vessels.berthingDate,
         importers.importerName,
         importers.licenseNumber,
          petroleum_data.*')
        ->join('vessels', 'vessels.vesselId = petroleum_data.vesselId')
        ->join('importers', 'importers.importerId = petroleum_data.importerId')
        // ->groupBy('petroleum_data.vesselId')
        ->get()
        ->getResult();
    }



    //select petroleum data method
    public function selectPetroleumData($params)
    {
        return $this->petroleumData->select()->where($params)->get()->getRow();
    }

    //update petroleum data method
    public function updatePetroleumData($id, $data)
    {
        return $this->petroleumData->where('id', $id)->set($data)->update();
    }

    //batch update petroleum data method
    public function batchUpdatePetroleumData($data)
    {
        return $this->petroleumData->updateBatch($data, 'id');
    }

//1535359086

    // ================CERTIFICATE OF QUANTITY===================
    //add certificate of quantity method
    public function addCertificateOfQuantity($data)
    {
        return $this->coqTable->insert($data);
    }

    //get all certificate of quantity method
    public function getCertificatesOfQuantity($params)
    {
        return $this->coqTable->select()->where($params)->get()->getResult();
    }

    //select certificate of quantity method
    public function selectCertificateOfQuantity($params)
    {
        return $this->coqTable
        ->select()
        ->join('vessels', 'vessels.vesselId = certificate_of_quantity.vesselId')
        ->where($params)
        ->get()
        ->getRow();
    }

    //update certificate of quantity method
    public function updateCertificateOfQuantity($id, $data)
    {
        return $this->coqTable->where('id', $id)->set($data)->update();
    }
    //delete certificate of quantity method
    public function deleteCertificateOfQuantity($id)
    {
        return $this->coqTable->select()->where(['id' => $id])->delete();
    }



    // ================VESSEL SAILING===================
    //add vessel sailing method
    public function addVesselSailing($data)
    {
        return $this->vesselSailing->insert($data);
    }


    //get all vessel sailing method
    public function getVesselSailing($params)
    {
        return $this->vesselSailing->select()->where($params)->get()->getResult();
    }

    //select vessel sailing method
    public function selectVesselSailing($params)
    {
        return $this->vesselSailing
        ->select()
        ->join('vessels', 'vessels.vesselId = vessel_sailing.vesselId')
        ->where($params)
        ->get()
        ->getRow();
    }
    public function selectVesselSailingReports()
    {
        return $this->vesselSailing
        ->select()
        ->join('vessels', 'vessels.vesselId = vessel_sailing.vesselId')
        // ->where($params)
        ->get()
        ->getResult();
    }

    //update vessel sailing method
    public function updateVesselSailing($id, $data)
    {
        return $this->vesselSailing->where('id', $id)->set($data)->update();
    }

    //delete vessel sailing
    public function deleteVesselSailing($id)
    {
        return $this->vesselSailing->select()->where(['id' => $id])->delete();
    }





    //outturn report
    public function addVesselOutturn($data)
    {
        return $this->vesselOutturn->insert($data);
    }

    //get all reports
    public function getVesselOutturnReports($params)
    {
        return $this->vesselOutturn->select()->where($params)->get()->getResult();
    }

    //select outturn report
    public function selectVesselOutturn($params)
    {
        return $this->vesselOutturn->select()->where($params)->get()->getRow();
    }

    //update outturn report
    public function updateVesselOutturn($id, $data)
    {
        return $this->vesselOutturn->where('id', $id)->set($data)->update();
    }

    //delete outturn report
    public function deleteVesselOutturn($id)
    {
        return $this->vesselOutturn->select()->where(['id' => $id])->delete();
    }
}
