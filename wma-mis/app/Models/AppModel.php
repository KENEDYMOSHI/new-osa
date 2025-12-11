<?php

namespace App\Models;

use CodeIgniter\Model;
use Config\Database;

class AppModel extends Model
{
    public $db;
    public $tempIdTable;
    public $stickerTable;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->tempIdTable = $this->db->table('temp_data');
        $this->stickerTable = $this->db->table('stickers');
    }



    public function createTempId($data)
    {
        return $this->tempIdTable->insert($data);
    }

    public function getItemIds($params)
    {
        $res = $this->tempIdTable
            ->select('itemId')
            ->where($params)
            ->get()
            ->getResult();

        return !empty($res) ? array_map(fn ($items) => $items->itemId, $res) : [microtime()];
    }


    public function disposeItems($itemsId)
    {
        return $this->tempIdTable
            ->whereIn('itemId', $itemsId)
            ->delete();
    }
    public function purgeItems($itemsId, $params)
    {
        return $this->tempIdTable
            ->whereIn('itemId', $itemsId)
            ->where($params)
            ->delete();
    }

    public function addSticker($data)
    {
        return $this->stickerTable->insert($data);
    }

    public function fetchSticker($params)
    {
        return $this->stickerTable
            ->select()
            ->where($params)
            ->orderBy('id', 'DESC')
            ->get()
            ->getRow();
    }
    public function fetchStickers($params)
    {
        return $this->stickerTable
            ->select()
            ->where($params)
            ->get()
            ->getResult();
    }
}
