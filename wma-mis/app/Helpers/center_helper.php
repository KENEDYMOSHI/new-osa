<?php

use App\Models\ProfileModel;

function collectionCenter($centerId = '')
{

  $profileModal = new ProfileModel();

  $center = empty($centerId) ? auth()->user()->collection_center : $centerId;

  $res = $profileModal->getCollectionCenterName($center);


  return $res;
}
