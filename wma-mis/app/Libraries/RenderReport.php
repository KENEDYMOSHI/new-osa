<?php

namespace App\Libraries;

use App\Libraries\ActivitiesLibrary;


class RenderReport
{



    public function renderActivities($data)
    {
        //create instance of activities array
        $url = $data->downloadUrl;
        $activities = new ActivitiesLibrary();
        switch ($data->Activity) {
            case 'All':

                $title = 'All Activities ';
                $title .= $data->reportTitle;

                // echo json_encode($activities->allActivities($data));

                echo json_encode([
                    'data' => $activities->allActivities($data),
                    'url' => $url,
                    'title' => $title
                ]);


                break;

            case 'vtc':
                $title = 'Vehicle Tank Verification ';
                $title .= $data->reportTitle;


                echo json_encode([
                    'data' => $activities->allActivities($data)['vtc'],
                    'url' => $url,
                    'title' => $title
                ]);
                break;
            case 'sbl':
                $title = 'Sandy and Ballast Lorries ';
                $title .= $data->reportTitle;


                echo json_encode([
                    'data' => $activities->allActivities($data)['sbl'],
                    'url' => $url,
                    'title' => $title
                ]);
                break;
            case 'water':
                $title = 'Water Meters ';
                $title .= $data->reportTitle;


                echo json_encode([
                    'data' => $activities->allActivities($data)['waterMeter'],
                    'url' => $url,
                    'title' => $title
                ]);
                # code...
                break;
            case 'prePackage':
                $title = 'Pre Package ';
                $title .= $data->reportTitle;

                // $allPrePackage =  $activities->allActivities($data)['prePackage'];

                echo json_encode([
                    'data' => $activities->allActivities($data)['prePackage'],
                    'url' => $url,
                    'title' => $title
                ]);
                // echo json_encode($data->prePackage);

                # code...
                break;

            default:
                # code...
                break;
        }
    }
}
