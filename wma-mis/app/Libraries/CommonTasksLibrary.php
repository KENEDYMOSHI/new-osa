<?php

namespace App\Libraries;

use App\Models\AdminModel;
use App\Models\ProfileModel;

class CommonTasksLibrary
{







    function processFile($file)
    {
        if ($file->isValid() && !$file->hasMoved()) {

            $randomName = substr($file->getName(), 0, -4) . '_' . $file->getRandomName();
            if ($file->move(FCPATH . '/uploads/documents/', $randomName)) {


                return    base_url() . '/uploads/documents/' . $randomName;
            }
        }
    }
    function nextYear($currentDate)
    {
        $date = strtotime($currentDate);
        $nexDate = strtotime("+1 Years", $date);
        return date("Y-m-d", $nexDate);
    }
    function nextFiveYears($currentDate)
    {
        $date = strtotime($currentDate);
        $nexDate = strtotime("+5 Years", $date);
        return date("Y-m-d", $nexDate);
    }

    function dateFormatter($actualDate)
    {
        $date = strtotime($actualDate);
        return date("d M Y", $date);
    }


    function collectionCenters()
    {
        $profileModal = new ProfileModel();
        return $profileModal->getCollectionCenters();
    }
    function getCollectionCenterName($code)
    {
        $profileModal = new ProfileModel();
        return $profileModal->getCollectionCenterName($code);
    }

    public function getCenterAddress()
    {
        $profileModal = new ProfileModel();
      
        $res = $profileModal->findCollectionCenter(auth()->user()->collection_center);


        return $res;
    }
}
