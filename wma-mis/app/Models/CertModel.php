<?php

namespace App\Models;

use CodeIgniter\Model;

class CertModel extends Model
{
    // protected $table            = 'correctness_certificate';
    // protected $primaryKey       = 'id';
    // protected $useAutoIncrement = true;
    // protected $returnType       = 'object';
    // protected $useSoftDeletes   = false;
    // protected $protectFields    = true;
    // protected $allowedFields    = [];




    public function getCertificates2($params)
    {
        $this->builder()
            ->select();
            // ->where($params);
        // $perPage = 5;
        return (object)[
            'certificates'  => $this->paginate(10),
            'pager' => $this->pager,
        ];
    }

    public function getCertificates($limit, $offset)
    {
       return $this->db->table('correctness_certificate')->select()->limit($limit, $offset)->get()->getResult();
    }

    public function countData()
    {
        return $this->db->table('correctness_certificate')->countAllResults();
    }

    
}
